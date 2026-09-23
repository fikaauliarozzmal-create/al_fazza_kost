<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            if (! Schema::hasColumn('pembayaran', 'id_admin_verifikasi')) {
                $table->foreignId('id_admin_verifikasi')->nullable()->after('alasan_penolakan')->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('pembayaran', 'tanggal_verifikasi')) {
                $table->dateTime('tanggal_verifikasi')->nullable()->after('id_admin_verifikasi');
            }
        });
    }

    public function down(): void
    {
        // Audit pembayaran dipertahankan untuk menjaga histori transaksi.
    }
};
