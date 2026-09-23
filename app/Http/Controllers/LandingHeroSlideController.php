<?php

namespace App\Http\Controllers;

use App\Models\LandingHeroSlide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class LandingHeroSlideController extends Controller
{
    public function index()
    {
        $defaults = [
            [
                'lokasi' => 'kost1',
                'judul' => 'Al Fazza Kost 1',
                'subjudul' => 'Karangmanyar, Purbalingga',
                'urutan' => 1,
            ],
            [
                'lokasi' => 'kost2',
                'judul' => 'Al Fazza Kost 2',
                'subjudul' => 'Toyareka, Purbalingga',
                'urutan' => 2,
            ],
        ];

        foreach ($defaults as $default) {
            LandingHeroSlide::firstOrCreate(
                ['lokasi' => $default['lokasi']],
                [
                    'judul' => $default['judul'],
                    'subjudul' => $default['subjudul'],
                    'urutan' => $default['urutan'],
                    'aktif' => true,
                ]
            );
        }

        $slides = LandingHeroSlide::query()
            ->orderBy('urutan')
            ->orderBy('id')
            ->limit(2)
            ->get();

        return view('landing-hero.index', compact('slides'));
    }

    public function update(Request $request, LandingHeroSlide $landingHeroSlide)
    {
        abort_unless(in_array($landingHeroSlide->lokasi, ['kost1', 'kost2'], true), 404);

        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:150'],
            'subjudul' => ['nullable', 'string', 'max:255'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'aktif' => ['nullable', 'boolean'],
        ], [
            'judul.required' => 'Judul foto wajib diisi.',
            'judul.max' => 'Judul maksimal 150 karakter.',
            'subjudul.max' => 'Subjudul maksimal 255 karakter.',
            'foto.image' => 'File yang dipilih harus berupa gambar.',
            'foto.mimes' => 'Format foto harus JPG, JPEG, PNG, atau WEBP.',
            'foto.max' => 'Ukuran foto maksimal 2 MB.',
        ]);

        $newPhotoPath = null;
        $oldPhotoPath = $landingHeroSlide->foto;

        if ($request->hasFile('foto')) {
            $newPhotoPath = $request->file('foto')->store('landing/hero', 'public');

            if (! is_string($newPhotoPath) || $newPhotoPath === '') {
                return back()->withInput()->withErrors([
                    'foto' => 'Foto tidak dapat disimpan ke penyimpanan. Silakan coba lagi.',
                ]);
            }

            $validated['foto'] = $newPhotoPath;
        }

        $validated['aktif'] = $request->boolean('aktif');

        try {
            DB::transaction(function () use ($landingHeroSlide, $validated): void {
                $landingHeroSlide->fill($validated);

                if (! $landingHeroSlide->save()) {
                    throw new RuntimeException('Data foto tidak dapat disimpan.');
                }
            });
        } catch (Throwable $exception) {
            if ($newPhotoPath) {
                Storage::disk('public')->delete($newPhotoPath);
            }

            report($exception);

            return back()->withInput()->withErrors([
                'foto' => 'Foto gagal disimpan. Tidak ada perubahan pada foto yang tersimpan.',
            ]);
        }

        // File lama baru dihapus setelah record baru benar-benar tersimpan.
        if ($newPhotoPath && $oldPhotoPath && $oldPhotoPath !== $newPhotoPath) {
            Storage::disk('public')->delete($oldPhotoPath);
        }

        return redirect()
            ->route($request->boolean('from_website_settings') ? 'pengaturan-website.index' : 'landing-hero.index')
            ->with(
                'success',
                $landingHeroSlide->lokasi === 'kost1'
                    ? 'Foto berhasil diupload untuk Al Fazza Kost 1.'
                    : 'Foto berhasil diupload untuk Al Fazza Kost 2.'
            );
    }
}
