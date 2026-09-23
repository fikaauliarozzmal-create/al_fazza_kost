<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class AccountProfileController extends Controller
{
    public function adminProfile()
    {
        abort_unless(request()->user()?->isAdmin(), 403);

        return view('admin.profil');
    }

    public function updateAdminPhoto(Request $request)
    {
        $user = $request->user();
        abort_unless($user?->isAdmin(), 403);

        return $this->updatePhoto($request, $user, 'admin.profil');
    }

    public function updatePenghuniPhoto(Request $request)
    {
        $user = $request->user();
        abort_unless($user?->isPenghuniAktif(), 403);

        return $this->updatePhoto($request, $user, 'penghuni.profil');
    }

    private function updatePhoto(Request $request, User $user, string $redirectRoute)
    {
        $validated = $request->validate([
            'profile_photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'profile_photo.required' => 'Pilih foto profil terlebih dahulu.',
            'profile_photo.image' => 'File foto profil harus berupa gambar.',
            'profile_photo.mimes' => 'Format foto profil harus JPG, JPEG, PNG, atau WEBP.',
            'profile_photo.max' => 'Ukuran foto profil maksimal 2 MB.',
        ]);

        // Simpan berkas baru terlebih dahulu. Dengan urutan ini foto yang lama
        // tidak pernah disentuh apabila upload atau penyimpanan berkas gagal.
        try {
            $newPath = $validated['profile_photo']->store(
                'profile-photos/' . ($user->getKey() ?: 'account'),
                'public'
            );
        } catch (Throwable $exception) {
            report($exception);

            return back()->withInput()->withErrors([
                'profile_photo' => 'Foto profil tidak dapat disimpan ke penyimpanan. Foto sebelumnya tetap digunakan.',
            ]);
        }

        if (! is_string($newPath) || $newPath === '' || ! Storage::disk('public')->exists($newPath)) {
            return back()->withInput()->withErrors([
                'profile_photo' => 'Foto profil tidak dapat disimpan ke penyimpanan. Foto sebelumnya tetap digunakan.',
            ]);
        }

        $oldPath = $user->profile_photo_path;

        try {
            DB::transaction(function () use ($user, $newPath): void {
                $user->profile_photo_path = $newPath;

                if (! $user->save()) {
                    throw new RuntimeException('Data foto profil tidak dapat disimpan.');
                }
            });
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($newPath);
            report($exception);

            return back()->withInput()->withErrors([
                'profile_photo' => 'Foto profil gagal disimpan. Foto sebelumnya tetap digunakan.',
            ]);
        }

        // Hapus hanya file profil yang dikelola aplikasi. Path lama dari data
        // historis di luar direktori ini tidak boleh terhapus secara sembarangan.
        if (User::isManagedProfilePhotoPath($oldPath) && $oldPath !== $newPath) {
            Storage::disk('public')->delete($oldPath);
        }

        return redirect()->route($redirectRoute)->with('success', 'Foto profil berhasil disimpan.');
    }
}
