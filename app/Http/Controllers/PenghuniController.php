<?php

namespace App\Http\Controllers;

use App\Models\Penghuni;
use App\Models\Kamar;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PenghuniController extends Controller
{
       public function index()
    {
        abort_unless(in_array(auth()->user()?->role, ['Admin', 'Super Admin'], true), 403);

        $penghuni = Penghuni::with(['user', 'kamar'])
            ->orderBy('id_penghuni', 'desc')
            ->get();

        return view('penghuni.index', compact('penghuni'));
    }

    public function keluarkan($id)
    {
        abort_unless(in_array(auth()->user()?->role, ['Admin', 'Super Admin'], true), 403);

        DB::transaction(function () use ($id) {
            $penghuni = Penghuni::with(['user', 'kamar'])
                ->lockForUpdate()
                ->findOrFail($id);

            abort_unless(
                $penghuni->status_penghuni === 'Aktif',
                400,
                'Penghuni ini sudah tidak aktif.'
            );

            // Tandai penghuni sudah keluar
            $penghuni->update([
                'status_penghuni' => 'Keluar',
                'taanggal_keluar' => now()->toDateString(),
            ]);

            // Nonaktifkan akun agar tidak bisa login lagi
            if ($penghuni->user) {
                $penghuni->user->update([
                    'status_akun' => 'Tidak Aktif',
                ]);
            }

            // Kosongkan kamar
            if ($penghuni->id_kamar) {
                $kamar = Kamar::lockForUpdate()->find($penghuni->id_kamar);

                // Jangan menandai kamar tersedia apabila data aktif lain masih
                // menggunakan kamar tersebut.
                $adaPenghuniAktifLain = Penghuni::where('id_kamar', $penghuni->id_kamar)
                    ->where('status_penghuni', 'Aktif')
                    ->where('id_penghuni', '!=', $penghuni->id_penghuni)
                    ->lockForUpdate()
                    ->exists();

                if ($kamar && ! $adaPenghuniAktifLain) {
                    $kamar->update([
                        'status_kamar' => 'tersedia',
                    ]);
                }
            }
        });

        return redirect()
            ->route('penghuni.index')
            ->with('success', 'Penghuni berhasil dikeluarkan dari kost.');
    }

    public function aktifkanKembali($id)
    {
        abort_unless(in_array(auth()->user()?->role, ['Admin', 'Super Admin'], true), 403);

        try {
            DB::transaction(function () use ($id): void {
                $penghuni = Penghuni::with('user')
                    ->lockForUpdate()
                    ->findOrFail($id);

                if ($penghuni->status_penghuni !== 'Keluar') {
                    throw ValidationException::withMessages([
                        'penghuni' => 'Hanya penghuni dengan status Keluar yang dapat diaktifkan kembali.',
                    ]);
                }

                $kamar = Kamar::lockForUpdate()->find($penghuni->id_kamar);

                if (! $kamar) {
                    throw ValidationException::withMessages([
                        'penghuni' => 'Kamar asal penghuni tidak ditemukan sehingga aktivasi tidak dapat dilakukan.',
                    ]);
                }

                $adaPenghuniAktifLain = Penghuni::where('id_kamar', $penghuni->id_kamar)
                    ->where('status_penghuni', 'Aktif')
                    ->where('id_penghuni', '!=', $penghuni->id_penghuni)
                    ->lockForUpdate()
                    ->exists();

                if ($adaPenghuniAktifLain) {
                    throw ValidationException::withMessages([
                        'penghuni' => 'Penghuni tidak dapat diaktifkan kembali karena kamar asal sedang digunakan penghuni aktif lain.',
                    ]);
                }

                if ($kamar->status_kamar !== 'tersedia') {
                    throw ValidationException::withMessages([
                        'penghuni' => 'Penghuni tidak dapat diaktifkan kembali karena kamar asal tidak tersedia.',
                    ]);
                }

                // Ini membatalkan aksi pengeluaran yang keliru: record, tanggal
                // masuk, dan seluruh relasi dipertahankan; hanya tanggal keluar
                // dari aksi tersebut yang dikosongkan kembali.
                $penghuni->update([
                    'status_penghuni' => 'Aktif',
                    'taanggal_keluar' => null,
                ]);

                if ($penghuni->user) {
                    $penghuni->user->update([
                        'status_akun' => 'Aktif',
                    ]);
                }

                $kamar->update([
                    'status_kamar' => 'terisi',
                ]);
            });
        } catch (ValidationException $exception) {
            return redirect()
                ->route('penghuni.index')
                ->withErrors($exception->errors());
        }

        return redirect()
            ->route('penghuni.index')
            ->with('success', 'Penghuni berhasil diaktifkan kembali.');
    }
}
