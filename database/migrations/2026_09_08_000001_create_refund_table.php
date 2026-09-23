<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('refund')) return;

        Schema::create('refund', function (Blueprint $table) {
            $table->increments('id_refund');
            $table->integer('id_pembayaran')->unique();
            $table->string('status_refund', 30)->default('Refund Belum Diproses');
            $table->decimal('nominal_refund', 15, 2);
            $table->date('tanggal_refund')->nullable();
            $table->text('catatan_refund')->nullable();
            $table->unsignedBigInteger('id_admin')->nullable();
        });

        // Backfill hanya untuk DP yang memang ditolak Admin pada booking yang
        // masih Disetujui; booking yang dibatalkan tidak masuk proses refund.
        DB::table('pembayaran')
            ->join('booking', 'booking.id_booking', '=', 'pembayaran.id_booking')
            ->where('pembayaran.jenis_pembayaran', 'DP')
            ->where('pembayaran.status_bayar', 'Ditolak')
            ->where('booking.status_booking', 'Disetujui')
            ->select('pembayaran.id_pembayaran', 'pembayaran.jumlah_bayar')
            ->orderBy('pembayaran.id_pembayaran')
            ->each(function (object $pembayaran) {
                DB::table('refund')->insert([
                    'id_pembayaran' => $pembayaran->id_pembayaran,
                    'status_refund' => 'Refund Belum Diproses',
                    'nominal_refund' => $pembayaran->jumlah_bayar,
                ]);
            });
    }

    public function down(): void { Schema::dropIfExists('refund'); }
};
