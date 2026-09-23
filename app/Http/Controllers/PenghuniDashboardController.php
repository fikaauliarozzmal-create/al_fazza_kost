<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Penghuni;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Services\ActivityService;

class PenghuniDashboardController extends Controller
{
    public function index(ActivityService $activityService)
    {
        $penghuni = $this->penghuniAktif(['kamar', 'user', 'booking']);
        $booking = null;

        if ($penghuni) {
            $pembayaran = Pembayaran::with('invoice')
                ->where('id_booking', $penghuni->id_booking)
                ->orderByDesc('id_pembayaran')
                ->get();
        } else {
            $booking = Booking::with(['kamar', 'pembayaran'])
                ->where('id_user', Auth::id())
                ->latest('id_booking')
                ->first();
            $pembayaran = $booking?->pembayaran ?? collect();
        }

        $pembayaranAwal = $pembayaran->whereIn('jenis_pembayaran', ['DP', 'Sisa Sewa Pertama']);
        $pembayaranBulanan = $pembayaran->where('jenis_pembayaran', 'Bulanan');
        $tagihanSaatIni = $pembayaranBulanan
            ->sortByDesc(fn ($item) => $item->periode_bayar ?? '')
            ->firstWhere('status_bayar', '!=', 'Valid')
            ?? $pembayaranBulanan->sortByDesc(fn ($item) => $item->periode_bayar ?? '')->first();
        $pembayaranTerakhir = $pembayaran->sortByDesc(fn ($item) => $item->tanggal_bayar?->getTimestamp() ?? 0)->first()
            ?? $pembayaran->first();
        $notifikasi = $this->notifikasi($tagihanSaatIni);
        $activities = $penghuni
            ? $activityService->penghuni($penghuni)
            : ($booking ? $activityService->calon($booking) : collect());

        return view('penghuni.dashboard', compact(
            'penghuni',
            'booking',
            'pembayaran',
            'pembayaranAwal',
            'pembayaranBulanan',
            'tagihanSaatIni',
            'pembayaranTerakhir',
            'notifikasi',
            'activities'
        ));
    }

    public function kamarSaya()
    {
        $penghuni = $this->penghuniAktif(['kamar', 'user', 'booking']);

        return view('penghuni.kamar', compact('penghuni'));
    }

    public function profil()
    {
        $penghuni = $this->penghuniAktif(['kamar', 'booking']);

        return view('penghuni.profil', compact('penghuni'));
    }

    public function updatePassword(Request $request)
    {
        $this->penghuniAktif();
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        Auth::user()->update(['password' => Hash::make($data['password'])]);

        return redirect()->route('penghuni.profil')->with('success', 'Password berhasil diperbarui.');
    }

    private function penghuniAktif(array $relations = []): ?Penghuni
    {
        abort_unless(Auth::user()?->role === 'User', 403);

        return Penghuni::with($relations)
            ->where('id_user', Auth::id())
            ->where('status_penghuni', 'Aktif')
            ->latest('id_penghuni')
            ->first();
    }

    private function notifikasi(?Pembayaran $pembayaran): ?array
    {
        if (! $pembayaran) return null;

        return match ($pembayaran->status_bayar) {
            'Pending' => $pembayaran->metode_bayar
                ? ['type' => 'warning', 'text' => 'Pembayaran kamu sedang menunggu validasi admin.']
                : ['type' => 'info', 'text' => 'Tagihan bulan ini belum dibayar.'],
            'Valid' => ['type' => 'success', 'text' => 'Pembayaran bulan ini sudah berhasil divalidasi.'],
            'Ditolak' => ['type' => 'danger', 'text' => 'Pembayaran ditolak. Silakan periksa kembali bukti pembayaran.'],
            default => null,
        };
    }
}
