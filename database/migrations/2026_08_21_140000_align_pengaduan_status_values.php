<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Nilai lama dipertahankan terlebih dahulu. MySQL membedakan ENUM
        // tanpa memperhatikan kapitalisasi, sehingga nilai sementara digunakan
        // sebelum dipindahkan ke penulisan status yang baru.
        DB::statement("ALTER TABLE pengaduan MODIFY status ENUM('diproses', 'selesai', 'Baru', 'temp_diproses', 'temp_selesai') NOT NULL DEFAULT 'Baru'");

        DB::table('pengaduan')->where('status', 'diproses')->update(['status' => 'temp_diproses']);
        DB::table('pengaduan')->where('status', 'selesai')->update(['status' => 'temp_selesai']);

        DB::statement("ALTER TABLE pengaduan MODIFY status ENUM('Baru', 'Diproses', 'Selesai', 'temp_diproses', 'temp_selesai') NOT NULL DEFAULT 'Baru'");

        DB::table('pengaduan')->where('status', 'temp_diproses')->update(['status' => 'Diproses']);
        DB::table('pengaduan')->where('status', 'temp_selesai')->update(['status' => 'Selesai']);

        DB::statement("ALTER TABLE pengaduan MODIFY status ENUM('Baru', 'Diproses', 'Selesai') NOT NULL DEFAULT 'Baru'");
    }

    public function down(): void
    {
        // Status baru dipertahankan agar data keluhan yang telah dibuat aman.
    }
};
