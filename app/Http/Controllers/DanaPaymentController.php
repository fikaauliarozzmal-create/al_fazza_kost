<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Services\Dana\DanaService;
use App\Services\NotifikasiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DanaPaymentController extends Controller
{
    public function startInitial(Request $request, Pembayaran $pembayaran, DanaService $dana, NotifikasiService $notifications)
    {
        $this->authorizeInitial($request, $pembayaran);
        return $this->start($request, $pembayaran, $dana, $notifications, route('pembayaran.awal.bayar', $pembayaran));
    }

    public function startResident(Request $request, Pembayaran $pembayaran, DanaService $dana, NotifikasiService $notifications)
    {
        abort_unless(Auth::user()?->isPenghuniAktif() && $pembayaran->booking?->id_user === Auth::id(), 403);
        return $this->start($request, $pembayaran, $dana, $notifications, route('pembayaran.bayar', $pembayaran));
    }

    public function returned(Request $request)
{
    $partnerReference = $request->query('originalPartnerReferenceNo');

    abort_unless(filled($partnerReference), 404);

    $pembayaran = Pembayaran::where('gateway_provider', 'DANA')
        ->where('gateway_partner_reference', $partnerReference)
        ->firstOrFail();

    $status = $request->query('status');

    // Browser redirect tidak mengubah status pembayaran.
    // Status resmi hanya diubah melalui Finish Notify yang sudah diverifikasi.
    return view('pembayaran.dana-return', compact('pembayaran', 'status'));
}

    public function webhook(Request $request, DanaService $dana, NotifikasiService $notifications)
    {
        $raw = $request->getContent();
        if (! $this->freshTimestamp($request->header('X-TIMESTAMP')) || ! $dana->verifyWebhook($raw, $request->headers->all())) {
            Log::warning('Rejected DANA webhook signature.');
            return response()->json(['responseCode' => '4015601', 'responseMessage' => 'Invalid signature'], 401);
        }

        $payload = json_decode($raw, true);
        if (! is_array($payload)) return response()->json(['responseCode' => '4005601', 'responseMessage' => 'Invalid payload'], 400);

        $result = DB::transaction(function () use ($payload, $notifications) {
            $payment = Pembayaran::where('gateway_provider', 'DANA')
                ->where('gateway_partner_reference', $payload['originalPartnerReferenceNo'] ?? null)
                ->lockForUpdate()->first();
            if (! $payment || $payment->gateway_reference !== ($payload['originalReferenceNo'] ?? null)
                || $payment->gateway_external_id !== ($payload['originalExternalId'] ?? null)
                || ($payload['merchantId'] ?? null) !== config('services.dana.merchant_id')
                || (float) ($payload['amount']['value'] ?? -1) !== (float) $payment->jumlah_bayar) return false;

            $status = match ($payload['latestTransactionStatus'] ?? null) {
                '00' => 'SUCCESS',
                '05' => 'EXPIRED',
                default => 'FAILED',
            };
            if ($payment->gateway_status === $status) return true; // Idempotent: no duplicate notification.

            $payment->update(['gateway_status' => $status, 'gateway_notified_at' => now(), 'gateway_metadata' => ['status_desc' => $payload['transactionStatusDesc'] ?? null]]);
            if ($payment->booking?->id_user) {
                $message = match ($status) {
                    'SUCCESS' => 'Pembayaran DANA diterima oleh DANA dan menunggu validasi Admin sesuai alur hunian.',
                    'EXPIRED' => 'Pembayaran DANA telah kedaluwarsa.',
                    default => 'Pembayaran DANA gagal atau dibatalkan.',
                };
                $notifications->buat($payment->booking->id_user, 'Status Pembayaran DANA', $message, 'pembayaran');
            }
            return true;
        });

        return $result
            ? response()->json(['responseCode' => '2005600', 'responseMessage' => 'Successful'])
            : response()->json(['responseCode' => '4045601', 'responseMessage' => 'Transaction not found'], 404);
    }

    private function start(Request $request, Pembayaran $payment, DanaService $dana, NotifikasiService $notifications, string $fallback)
    {
        abort_unless(config('payment.dana_enabled'), 404);
        abort_unless($this->payable($payment), 403);
        try {
            $result = $dana->createPayment($payment, $request->getSchemeAndHttpHost(), $request->ip());
        } catch (\Throwable $exception) {
            report($exception);
            $message = $dana->ready()
                ? 'Pembayaran DANA belum dapat diproses. Silakan coba lagi nanti.'
                : 'DANA belum dikonfigurasi. Integrasi DANA sedang menunggu konfigurasi Sandbox resmi.';

            return redirect($fallback)->withErrors(['dana' => $message]);
        }

        $notifications->untukAdmin('Pembayaran DANA Diproses', ($payment->booking?->nama_pemesan ?? 'Penghuni').' memulai pembayaran DANA sebesar Rp'.number_format((float) $payment->jumlah_bayar, 0, ',', '.').'.', 'pembayaran');
        if ($payment->booking?->id_user) {
            $notifications->buat($payment->booking->id_user, 'Pembayaran DANA Diproses', 'Pembayaran DANA sedang diproses. Selesaikan konfirmasi pada halaman resmi DANA.', 'pembayaran');
        }

        return redirect()->away($result['redirect_url']);
    }

    private function authorizeInitial(Request $request, Pembayaran $payment): void
    {
        $booking = $payment->booking;
        if ($request->user()) abort_unless($request->user()->role === 'User' && (int) $booking->id_user === (int) $request->user()->id, 403);
        else abort_unless((int) $request->session()->get('booking_check_id') === (int) $booking->id_booking, 403);
        abort_unless($booking->status_booking === 'Disetujui' && in_array($payment->jenis_pembayaran, ['DP', 'Sisa Sewa Pertama'], true), 403);
    }

    private function payable(Pembayaran $payment): bool
    {
        return ($payment->status_bayar === 'Pending' && ! filled($payment->metode_bayar) && ! $payment->tanggal_bayar) || $payment->status_bayar === 'Ditolak';
    }

    private function freshTimestamp(?string $timestamp): bool
    {
        try {
            return $timestamp && abs(Carbon::parse($timestamp)->diffInSeconds(now())) <= 300;
        } catch (\Throwable) {
            return false;
        }
    }
}
