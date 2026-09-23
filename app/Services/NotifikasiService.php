<?php

namespace App\Services;

use App\Models\Notifikasi;
use App\Models\User;

class NotifikasiService
{
    public function untukAdmin(string $judul, string $pesan, string $tipe): void
    {
        User::whereIn('role', ['Admin', 'Super Admin'])
            ->where('status_akun', 'Aktif')
            ->pluck('id')
            ->each(fn (int $id) => $this->buat($id, $judul, $pesan, $tipe));
    }

    public function buat(int $userId, string $judul, string $pesan, string $tipe): Notifikasi
    {
        return Notifikasi::create([
            'id_user' => $userId,
            'judul' => $judul,
            'pesan' => $pesan,
            'tipe' => $tipe,
        ]);
    }
}
