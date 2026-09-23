<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Invoice;
use App\Models\Pembayaran;
use App\Models\Pengaduan;
use App\Models\Penghuni;
use Illuminate\Support\Collection;

/**
 * Builds dashboard activity from the application's transactional records.
 *
 * The application has no generic activity-log table.  This service deliberately
 * uses the business dates stored by each existing record instead of inventing
 * a second source of truth.
 */
class ActivityService
{
    public function __construct(private readonly ActivityTimeFormatter $timeFormatter)
    {
    }

    public function admin(): Collection
    {
        $bookings = Booking::with(['user', 'kamar'])
            ->orderByRaw('COALESCE(aktivitas_dibuat_pada, tanggal_booking) DESC')
            ->orderByDesc('id_booking')
            ->limit(12)
            ->get()
            ->map(fn (Booking $booking) => $this->adminBooking($booking));

        $payments = Pembayaran::with(['booking.user', 'booking.kamar'])
            ->where(fn ($query) => $query->whereNotNull('aktivitas_dikirim_pada')->orWhereNotNull('tanggal_bayar'))
            ->orderByRaw('COALESCE(gateway_notified_at, aktivitas_dikirim_pada, aktivitas_dibuat_pada, tanggal_bayar) DESC')
            ->orderByDesc('id_pembayaran')
            ->limit(12)
            ->get()
            ->map(fn (Pembayaran $payment) => $this->adminPayment($payment));

        $residents = Penghuni::with(['user', 'booking', 'kamar'])
            ->where(fn ($query) => $query->whereNotNull('aktivitas_masuk_pada')->orWhereNotNull('tanggal_masuk'))
            ->orderByRaw('COALESCE(aktivitas_masuk_pada, tanggal_masuk) DESC')
            ->orderByDesc('id_penghuni')
            ->limit(12)
            ->get()
            ->map(fn (Penghuni $resident) => $this->adminResident($resident));

        $complaints = Pengaduan::with(['penghuni.user'])
            ->orderByDesc('updated_at')
            ->orderByDesc('tanggal')
            ->orderByDesc('id_pengaduan')
            ->limit(12)
            ->get()
            ->map(fn (Pengaduan $complaint) => $this->adminComplaint($complaint));

        $invoices = Invoice::with(['penghuni.user'])
            ->where(fn ($query) => $query->whereNotNull('aktivitas_dibuat_pada')->orWhereNotNull('tanggal_invoice'))
            ->orderByRaw('COALESCE(aktivitas_dibuat_pada, tanggal_invoice) DESC')
            ->orderByDesc('id_invoice')
            ->limit(12)
            ->get()
            ->map(fn (Invoice $invoice) => $this->adminInvoice($invoice));

        return $this->latest($bookings, $payments, $residents, $complaints, $invoices);
    }

    public function calon(Booking $booking): Collection
    {
        $activities = collect([
            $this->activity('booking', 'Booking Dibuat', 'Anda mengajukan booking kamar ' . $this->room($booking) . '.', $booking->aktivitas_dibuat_pada ?? $booking->tanggal_booking, $booking->id_booking, route('booking.status', $booking)),
        ]);

        $activities->push(match ($booking->status_booking) {
            'Menunggu' => $this->activity('booking', 'Booking Menunggu Verifikasi', 'Booking kamar ' . $this->room($booking) . ' sedang menunggu verifikasi Admin.', $booking->aktivitas_status_pada ?? $booking->tanggal_booking, $booking->id_booking, route('booking.status', $booking)),
            'Disetujui' => $this->activity('booking', 'Booking Disetujui', 'Booking kamar ' . $this->room($booking) . ' telah disetujui Admin.', $booking->aktivitas_status_pada ?? $booking->tanggal_booking, $booking->id_booking, route('booking.status', $booking)),
            'Ditolak' => $this->activity('booking', 'Booking Ditolak', 'Booking kamar ' . $this->room($booking) . ' ditolak oleh Admin.', $booking->aktivitas_status_pada ?? $booking->tanggal_booking, $booking->id_booking, route('booking.status', $booking)),
            default => null,
        });

        foreach ($booking->pembayaran as $payment) {
            if ($payment->gateway_provider === 'DANA' && in_array($payment->gateway_status, ['SUCCESS', 'FAILED', 'EXPIRED'], true)) {
                $label = match ($payment->gateway_status) {
                    'SUCCESS' => ['Pembayaran DANA Berhasil', 'Pembayaran DANA berhasil diproses dan menunggu validasi Admin.'],
                    'EXPIRED' => ['Pembayaran DANA Kadaluarsa', 'Batas waktu pembayaran DANA telah berakhir.'],
                    default => ['Pembayaran DANA Gagal', 'Pembayaran DANA gagal atau dibatalkan.'],
                };
                $activities->push($this->activity('payment', $label[0], $label[1], $payment->gateway_notified_at ?? $payment->tanggal_bayar, $payment->id_pembayaran, route('booking.status', $booking)));
                continue;
            }
            if ($payment->status_bayar === 'Pending' && ! $payment->metode_bayar) {
                $activities->push($this->activity('payment', 'Menunggu Pembayaran', 'Silakan lakukan pembayaran ' . strtolower($payment->jenis_pembayaran) . ' untuk melanjutkan proses hunian.', $payment->aktivitas_dibuat_pada ?? $booking->tanggal_booking, $payment->id_pembayaran, route('booking.status', $booking)));
            } elseif ($payment->status_bayar === 'Pending') {
                $activities->push($this->activity('payment', 'Pembayaran Dikirim', 'Bukti pembayaran ' . strtolower($payment->jenis_pembayaran) . ' telah dikirim dan menunggu verifikasi Admin.', $this->paymentTime($payment), $payment->id_pembayaran, route('booking.status', $booking)));
            } elseif ($payment->status_bayar === 'Valid') {
                $activities->push($this->activity('payment', 'Pembayaran Diverifikasi', 'Pembayaran ' . strtolower($payment->jenis_pembayaran) . ' telah diverifikasi Admin.', $payment->tanggal_verifikasi ?? $this->paymentTime($payment), $payment->id_pembayaran, route('booking.status', $booking)));
            } elseif ($payment->status_bayar === 'Ditolak') {
                $activities->push($this->activity('payment', 'Pembayaran Ditolak', 'Pembayaran ' . strtolower($payment->jenis_pembayaran) . ' ditolak. Silakan periksa bukti pembayaran Anda.', $payment->tanggal_verifikasi ?? $this->paymentTime($payment) ?? $booking->tanggal_booking, $payment->id_pembayaran, route('booking.status', $booking)));
            }
        }

        return $this->latest($activities);
    }

    public function penghuni(Penghuni $resident): Collection
    {
        $payments = Pembayaran::where('id_booking', $resident->id_booking)
            ->where(fn ($query) => $query->whereNotNull('aktivitas_dikirim_pada')->orWhereNotNull('tanggal_bayar'))
            ->orderByRaw('COALESCE(aktivitas_dikirim_pada, aktivitas_dibuat_pada, tanggal_bayar) DESC')
            ->orderByDesc('id_pembayaran')
            ->limit(12)
            ->get()
            ->map(fn (Pembayaran $payment) => $this->residentPayment($payment));

        $invoices = Invoice::where('id_penghuni', $resident->id_penghuni)
            ->where(fn ($query) => $query->whereNotNull('aktivitas_dibuat_pada')->orWhereNotNull('tanggal_invoice'))
            ->orderByRaw('COALESCE(aktivitas_dibuat_pada, tanggal_invoice) DESC')
            ->orderByDesc('id_invoice')
            ->limit(12)
            ->get()
            ->map(fn (Invoice $invoice) => $this->activity('invoice', 'Invoice Baru', 'Invoice ' . ($invoice->nomor_invoice ?: '#'.$invoice->id_invoice) . ' tersedia.', $invoice->aktivitas_dibuat_pada ?? $invoice->tanggal_invoice, $invoice->id_invoice, route('penghuni.invoice.show', $invoice->id_invoice)));

        $complaints = Pengaduan::where('id_penghuni', $resident->id_penghuni)
            ->orderByDesc('updated_at')
            ->orderByDesc('tanggal')
            ->orderByDesc('id_pengaduan')
            ->limit(12)
            ->get()
            ->map(fn (Pengaduan $complaint) => $this->residentComplaint($complaint));

        return $this->latest($payments, $invoices, $complaints);
    }

    private function adminBooking(Booking $booking): array
    {
        $name = $booking->user?->name ?? $booking->nama_pemesan ?? 'Pemesan';

        return $this->activity('booking', 'Booking Baru', $name . ' melakukan booking kamar ' . $this->room($booking) . '.', $booking->aktivitas_dibuat_pada ?? $booking->tanggal_booking, $booking->id_booking, route('booking.show', $booking->id_booking));
    }

    private function adminPayment(Pembayaran $payment): array
    {
        $name = $payment->booking?->user?->name ?? $payment->booking?->nama_pemesan ?? 'Penghuni';

        $title = $payment->gateway_provider === 'DANA' ? 'Pembayaran DANA ' . match ($payment->gateway_status) {
            'SUCCESS' => 'Berhasil', 'EXPIRED' => 'Kadaluarsa', 'FAILED' => 'Gagal', default => 'Baru',
        } : 'Pembayaran Baru';
        return $this->activity('payment', $title, 'Pembayaran Rp' . number_format((float) $payment->jumlah_bayar, 0, ',', '.') . ' dari ' . $name . '.', $payment->gateway_notified_at ?? $this->paymentTime($payment), $payment->id_pembayaran, route('pembayaran.show', $payment->id_pembayaran));
    }

    private function adminResident(Penghuni $resident): array
    {
        $name = $resident->user?->name ?? $resident->booking?->nama_pemesan ?? 'Penghuni';

        return $this->activity('resident', 'Penghuni Baru', $name . ' telah menjadi penghuni Al Fazza Kost.', $resident->aktivitas_masuk_pada ?? $resident->tanggal_masuk, $resident->id_penghuni);
    }

    private function adminComplaint(Pengaduan $complaint): array
    {
        $name = $complaint->penghuni?->user?->name ?? 'Penghuni';
        $title = $complaint->status === 'Baru' ? 'Pengaduan Baru' : 'Pengaduan ' . $complaint->status;

        return $this->activity('complaint', $title, $name . ' mengirim pengaduan: ' . $complaint->judul . '.', $complaint->updated_at ?? $complaint->tanggal, $complaint->id_pengaduan, route('admin.keluhan.show', $complaint));
    }

    private function adminInvoice(Invoice $invoice): array
    {
        $name = $invoice->penghuni?->user?->name ?? 'Penghuni';

        return $this->activity('invoice', 'Invoice Baru', 'Invoice ' . ($invoice->nomor_invoice ?: '#'.$invoice->id_invoice) . ' dibuat untuk ' . $name . '.', $invoice->aktivitas_dibuat_pada ?? $invoice->tanggal_invoice, $invoice->id_invoice, route('invoice.show', $invoice->id_invoice));
    }

    private function residentPayment(Pembayaran $payment): array
    {
        $title = $payment->gateway_provider === 'DANA' ? match ($payment->gateway_status) {
            'SUCCESS' => 'Pembayaran DANA Berhasil', 'EXPIRED' => 'Pembayaran DANA Kadaluarsa', 'FAILED' => 'Pembayaran DANA Gagal', default => 'Pembayaran DANA Diproses',
        } : match ($payment->status_bayar) {
            'Valid' => 'Pembayaran Berhasil',
            'Ditolak' => 'Pembayaran Ditolak',
            default => 'Pembayaran Dikirim',
        };
        $description = match ($payment->status_bayar) {
            'Valid' => 'Pembayaran ' . strtolower($payment->jenis_pembayaran) . ' berhasil diverifikasi.',
            'Ditolak' => 'Pembayaran ' . strtolower($payment->jenis_pembayaran) . ' ditolak. Silakan periksa detail pembayaran.',
            default => 'Pembayaran ' . strtolower($payment->jenis_pembayaran) . ' sedang menunggu verifikasi Admin.',
        };

        return $this->activity('payment', $title, $description, $payment->tanggal_verifikasi ?? $this->paymentTime($payment), $payment->id_pembayaran, route('penghuni.pembayaran'));
    }

    private function residentComplaint(Pengaduan $complaint): array
    {
        $title = match ($complaint->status) {
            'Diproses' => 'Pengaduan Diproses',
            'Selesai' => 'Pengaduan Selesai',
            default => 'Pengaduan Diterima',
        };
        $description = match ($complaint->status) {
            'Diproses' => 'Pengaduan "' . $complaint->judul . '" sedang diproses Admin.',
            'Selesai' => 'Pengaduan "' . $complaint->judul . '" telah diselesaikan.',
            default => 'Pengaduan "' . $complaint->judul . '" telah diterima.',
        };

        return $this->activity('complaint', $title, $description, $complaint->updated_at ?? $complaint->tanggal, $complaint->id_pengaduan, route('penghuni.keluhan.index'));
    }

    private function latest(Collection ...$groups): Collection
    {
        return collect($groups)
            ->flatten(1)
            ->filter()
            ->sortByDesc(fn (array $activity) => sprintf('%020d-%020d', $activity['timestamp'], $activity['id']))
            ->take(6)
            ->values();
    }

    private function activity(string $type, string $title, string $description, mixed $date, int $id, ?string $url = null): array
    {
        return compact('type', 'title', 'description', 'id', 'url')
            + $this->timeFormatter->format($date ?: now());
    }

    private function paymentTime(Pembayaran $payment): mixed
    {
        return $payment->aktivitas_dikirim_pada
            ?? $payment->aktivitas_dibuat_pada
            ?? $payment->tanggal_bayar;
    }

    private function room(Booking $booking): string
    {
        return $booking->kamar?->nomor_kamar ?? '-';
    }
}
