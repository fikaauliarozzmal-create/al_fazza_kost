<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Invoice;
use App\Models\Kamar;
use App\Models\Pembayaran;
use App\Models\Pengaduan;
use App\Models\Penghuni;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate([
            'periode' => ['nullable', Rule::in(['semua', 'bulan_ini', 'bulan_lalu', 'tahun_ini'])],
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
        ]);

        [$tanggalMulai, $tanggalSelesai, $labelPeriode] = $this->resolvePeriod($data);

        $bookingQuery = $this->applyDateFilter(Booking::query(), 'tanggal_booking', $tanggalMulai, $tanggalSelesai);
        $penghuniQuery = $this->applyDateFilter(Penghuni::query(), 'tanggal_masuk', $tanggalMulai, $tanggalSelesai);
        $pembayaranQuery = $this->applyDateFilter(Pembayaran::query(), 'tanggal_bayar', $tanggalMulai, $tanggalSelesai);
        $invoiceQuery = $this->applyDateFilter(Invoice::query(), 'tanggal_invoice', $tanggalMulai, $tanggalSelesai);
        $pengaduanQuery = $this->applyDateFilter(Pengaduan::query(), 'tanggal', $tanggalMulai, $tanggalSelesai);

        $ringkasan = [
            'kamar' => [
                'total' => Kamar::count(),
                'tersedia' => Kamar::where('status_kamar', 'tersedia')->count(),
                'terisi' => Kamar::where('status_kamar', 'terisi')->count(),
                'perbaikan' => Kamar::where('status_kamar', 'perbaikan')->count(),
            ],
            'booking' => [
                'total' => (clone $bookingQuery)->count(),
                'menunggu' => (clone $bookingQuery)->where('status_booking', 'Menunggu')->count(),
                'disetujui' => (clone $bookingQuery)->where('status_booking', 'Disetujui')->count(),
                'ditolak' => (clone $bookingQuery)->where('status_booking', 'Ditolak')->count(),
            ],
            'penghuni' => [
                'aktif' => (clone $penghuniQuery)->where('status_penghuni', 'Aktif')->count(),
            ],
            'pembayaran' => [
                'total' => (clone $pembayaranQuery)->count(),
                'nominal_valid' => (clone $pembayaranQuery)->where('status_bayar', 'Valid')->sum('jumlah_bayar'),
                'pending' => (clone $pembayaranQuery)->where('status_bayar', 'Pending')->count(),
                'ditolak' => (clone $pembayaranQuery)->where('status_bayar', 'Ditolak')->count(),
            ],
            'invoice' => [
                'total' => (clone $invoiceQuery)->count(),
                'lunas' => (clone $invoiceQuery)->where('status_invoice', 'lunas')->count(),
            ],
            'keluhan' => [
                'total' => (clone $pengaduanQuery)->count(),
                'baru' => (clone $pengaduanQuery)->where('status', 'Baru')->count(),
                'diproses' => (clone $pengaduanQuery)->where('status', 'Diproses')->count(),
                'selesai' => (clone $pengaduanQuery)->where('status', 'Selesai')->count(),
            ],
        ];

        $kamar = Kamar::orderBy('nomor_kamar')->get();
        $booking = (clone $bookingQuery)->with('kamar')->latest('tanggal_booking')->latest('id_booking')->get();
        $penghuni = (clone $penghuniQuery)->with(['user', 'booking', 'kamar'])->latest('tanggal_masuk')->latest('id_penghuni')->get();
        $pembayaran = (clone $pembayaranQuery)->with('booking.kamar')->latest('tanggal_bayar')->latest('id_pembayaran')->get();
        $invoice = (clone $invoiceQuery)->with(['penghuni.user', 'penghuni.kamar', 'pembayaran'])->latest('tanggal_invoice')->latest('id_invoice')->get();
        $pengaduan = (clone $pengaduanQuery)->with(['penghuni.user', 'penghuni.kamar'])->latest('tanggal')->latest('id_pengaduan')->get();

        return view('admin.laporan.index', compact(
            'ringkasan',
            'kamar',
            'booking',
            'penghuni',
            'pembayaran',
            'invoice',
            'pengaduan',
            'tanggalMulai',
            'tanggalSelesai',
            'labelPeriode'
        ));
    }

    private function resolvePeriod(array $data): array
    {
        if (! empty($data['tanggal_mulai']) || ! empty($data['tanggal_selesai'])) {
            return [
                $data['tanggal_mulai'] ?? null,
                $data['tanggal_selesai'] ?? null,
                'Rentang tanggal pilihan',
            ];
        }

        return match ($data['periode'] ?? 'semua') {
            'bulan_ini' => [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString(), 'Bulan ini'],
            'bulan_lalu' => [
                now()->subMonthNoOverflow()->startOfMonth()->toDateString(),
                now()->subMonthNoOverflow()->endOfMonth()->toDateString(),
                'Bulan lalu',
            ],
            'tahun_ini' => [now()->startOfYear()->toDateString(), now()->endOfYear()->toDateString(), 'Tahun ini'],
            default => [null, null, 'Semua periode'],
        };
    }

    private function applyDateFilter(Builder $query, string $column, ?string $tanggalMulai, ?string $tanggalSelesai): Builder
    {
        if ($tanggalMulai) {
            $query->whereDate($column, '>=', Carbon::parse($tanggalMulai)->toDateString());
        }

        if ($tanggalSelesai) {
            $query->whereDate($column, '<=', Carbon::parse($tanggalSelesai)->toDateString());
        }

        return $query;
    }
}
