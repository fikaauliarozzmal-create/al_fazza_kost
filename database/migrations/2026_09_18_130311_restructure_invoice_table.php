<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('invoice', function (Blueprint $table) {
            // Kolom baru untuk menghubungkan invoice langsung dengan booking.
            $table->integer('id_booking')->nullable()->after('id_penghuni');

            // Menentukan jenis tagihan invoice.
            $table->string('jenis_invoice', 30)->default('Bulanan')->after('id_booking');

            // Periode hanya digunakan untuk invoice bulanan.
            $table->string('periode_bayar', 7)->nullable()->after('jenis_invoice');

            // Total pembayaran yang sudah tervalidasi untuk invoice ini.
            $table->unsignedInteger('total_terbayar')->default(0)->after('total_tagihan');

            // Pembayaran bisa belum ada ketika invoice pertama kali dibuat.
            $table->integer('id_pembayaran')->nullable()->change();
        });

        Schema::table('invoice', function (Blueprint $table) {
            $table->foreign('id_booking')
                ->references('id_booking')
                ->on('booking')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice', function (Blueprint $table) {
            $table->dropForeign(['id_booking']);

            $table->dropColumn([
                'id_booking',
                'jenis_invoice',
                'periode_bayar',
                'total_terbayar',
            ]);

            $table->integer('id_pembayaran')->nullable(false)->change();
        });
    }
};
