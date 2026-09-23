<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengaduan', function (Blueprint $table) {
            if (! Schema::hasColumn('pengaduan', 'kategori')) {
                $table->string('kategori', 100)->default('Lainnya')->after('tanggal');
            }

            if (! Schema::hasColumn('pengaduan', 'lampiran_foto')) {
                $table->string('lampiran_foto')->nullable()->after('tanggapan_admin');
            }

            if (! Schema::hasColumn('pengaduan', 'lampiran_video')) {
                $table->string('lampiran_video')->nullable()->after('lampiran_foto');
            }
        });
    }

    public function down(): void
    {
        // Kolom dipertahankan untuk melindungi data keluhan/lampiran yang telah masuk.
    }
};
