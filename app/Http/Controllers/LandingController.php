<?php

namespace App\Http\Controllers;

use App\Models\Kamar;
use App\Models\LandingHeroSlide;
use App\Models\PengaturanWebsite;

class LandingController extends Controller
{
    public function index()
    {
        $kamar = Kamar::orderBy('nomor_kamar')->get();

        $heroSlides = LandingHeroSlide::query()
            ->where('aktif', true)
            ->orderBy('urutan')
            ->orderBy('id')
            ->limit(2)
            ->get();

        $pengaturan = PengaturanWebsite::query()
            ->orderBy('id')
            ->get();

        $user = auth()->user();

        $accountStatus = $user?->isAdmin()
            ? 'Admin'
            : ($user?->isPenghuniAktif()
                ? 'Penghuni Aktif'
                : 'Calon Penghuni');

        return view('landing.home', compact(
            'kamar',
            'heroSlides',
            'pengaturan',
            'accountStatus'
        ));
    }
}
