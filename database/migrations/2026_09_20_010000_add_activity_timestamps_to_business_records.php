<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('booking', function (Blueprint $table) {
            if (! Schema::hasColumn('booking', 'aktivitas_dibuat_pada')) $table->dateTime('aktivitas_dibuat_pada')->nullable();
            if (! Schema::hasColumn('booking', 'aktivitas_status_pada')) $table->dateTime('aktivitas_status_pada')->nullable();
        });
        Schema::table('pembayaran', function (Blueprint $table) {
            if (! Schema::hasColumn('pembayaran', 'aktivitas_dibuat_pada')) $table->dateTime('aktivitas_dibuat_pada')->nullable();
            if (! Schema::hasColumn('pembayaran', 'aktivitas_dikirim_pada')) $table->dateTime('aktivitas_dikirim_pada')->nullable();
        });
        Schema::table('penghuni', function (Blueprint $table) {
            if (! Schema::hasColumn('penghuni', 'aktivitas_masuk_pada')) $table->dateTime('aktivitas_masuk_pada')->nullable();
        });
        Schema::table('invoice', function (Blueprint $table) {
            if (! Schema::hasColumn('invoice', 'aktivitas_dibuat_pada')) $table->dateTime('aktivitas_dibuat_pada')->nullable();
        });
    }

    public function down(): void
    {
        // Timestamp aktivitas dipertahankan demi histori; migration ini tidak destruktif.
    }
};
