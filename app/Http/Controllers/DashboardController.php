<?php

namespace App\Http\Controllers;

use App\Models\Kamar;
use App\Models\Booking;
use App\Models\Penghuni;
use App\Models\Pembayaran;
use App\Services\ActivityService;

class DashboardController extends Controller
{
    public function index(ActivityService $activityService)
    {
        $totalKamar = Kamar::count();

        $totalBooking = Booking::count();

        $totalPenghuni = Penghuni::where('status_penghuni', 'Aktif')->count();

        $totalPembayaran = Pembayaran::sum('jumlah_bayar');

        $activities = $activityService->admin();

$penghuniAktif = Penghuni::with(['user', 'kamar'])
    ->where('status_penghuni', 'Aktif')
    ->orderBy('id_penghuni', 'desc')
    ->get();

return view('dashboard.index', compact(
    'totalKamar',
    'totalBooking',
    'totalPenghuni',
    'totalPembayaran',
    'activities',
    'penghuniAktif'
));
    }
}
