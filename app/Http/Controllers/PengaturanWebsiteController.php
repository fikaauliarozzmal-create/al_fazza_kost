<?php

namespace App\Http\Controllers;

use App\Models\PengaturanWebsite;
use App\Models\LandingHeroSlide;
use App\View\Components\SiteLogo;
use Illuminate\Http\Request;

class PengaturanWebsiteController extends Controller
{
    public function index()
    {
        $pengaturan = PengaturanWebsite::orderBy('id')->get();
        $defaults = [
            'kost1' => ['judul' => 'Al Fazza Kost 1', 'subjudul' => 'Karangmanyar, Purbalingga', 'urutan' => 1],
            'kost2' => ['judul' => 'Al Fazza Kost 2', 'subjudul' => 'Toyareka, Purbalingga', 'urutan' => 2],
        ];

        foreach ($defaults as $lokasi => $default) {
            LandingHeroSlide::firstOrCreate(['lokasi' => $lokasi], $default + ['aktif' => true]);
        }

        $heroSlides = LandingHeroSlide::query()
            ->whereIn('lokasi', array_keys($defaults))
            ->orderBy('urutan')
            ->orderBy('id')
            ->get()
            ->keyBy('lokasi');

        return view('pengaturan-website.index', [
            'pengaturan' => $pengaturan->keyBy('lokasi'),
            'heroSlides' => $heroSlides,
            'logoCustomSupported' => SiteLogo::supportsCustomLogo(),
        ]);
    }

    public function updateLogo(Request $request)
    {
        $validated = $request->validate([
            'logo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if (! SiteLogo::supportsCustomLogo()) {
            return back()->withErrors(['logo' => 'Penyimpanan logo custom belum tersedia pada database. Logo default tetap digunakan.']);
        }

        $pengaturan = PengaturanWebsite::orderBy('id')->first();

        if (! $pengaturan) {
            return back()->withErrors(['logo' => 'Data pengaturan website belum tersedia.']);
        }

        $pengaturan->update([
            'logo_path' => $validated['logo']->store('logo-website', 'public'),
        ]);

        return redirect()->route('pengaturan-website.index')->with('success', 'Logo utama website berhasil diperbarui.');
    }

    public function update(Request $request, PengaturanWebsite $pengaturanWebsite)
    {
        $validated = $request->validate([
            'nama_kost' => ['required', 'string', 'max:100'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'alamat' => ['nullable', 'string'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'google_maps_url' => ['nullable', 'url', 'max:2000'],
        ], [
            'nama_kost.required' => 'Nama kost wajib diisi.',
            'nama_kost.max' => 'Nama kost maksimal 100 karakter.',
            'google_maps_url.url' => 'Link Google Maps harus berupa URL yang valid.',
            'google_maps_url.max' => 'Link Google Maps terlalu panjang.',
        ]);

        if ($request->hasFile('logo')) {
            if (! SiteLogo::supportsCustomLogo()) {
                return back()
                    ->withInput()
                    ->withErrors(['logo' => 'Penyimpanan logo custom belum tersedia pada database. Logo default tetap digunakan.']);
            }

            $validated['logo_path'] = $request->file('logo')->store('logo-website', 'public');
        }

        unset($validated['logo']);
        $pengaturanWebsite->update($validated);

        return redirect()
            ->route('pengaturan-website.index')
            ->with('success', 'Pengaturan website berhasil diperbarui.');
    }
}
