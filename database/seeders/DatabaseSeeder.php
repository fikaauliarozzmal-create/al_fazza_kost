<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(['username' => 'admin'], [
            'name' => 'Administrator Al Fazza', 'email' => 'admin@alfazzakost.local',
            'no_whatsapp' => '0000000000', 'password' => Hash::make('admin123'),
            'role' => 'Admin', 'status_akun' => 'Aktif',
        ]);
        User::updateOrCreate(['username' => 'penghuni'], [
            'name' => 'Penghuni Demo', 'email' => 'penghuni@alfazzakost.local',
            'no_whatsapp' => '081234567890', 'password' => Hash::make('penghuni123'),
            'role' => 'User', 'status_akun' => 'Aktif',
        ]);
    }
}
