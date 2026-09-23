<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
         * Kolom pembayaran sudah berhasil dibuat pada
         * percobaan migration sebelumnya.
         *
         * Jadi jangan membuat ulang:
         * - jenis_pembayaran
         * - periode_bayar
         */

        /*
         * Perbaiki tipe id_booking pada penghuni.
         *
         * booking.id_booking = INT(11)
         * sehingga penghuni.id_booking harus INT(11)
         * agar foreign key kompatibel.
         */
        Schema::table('penghuni', function (Blueprint $table) {
            $table->integer('id_booking')
                ->nullable()
                ->change();
        });

        /*
         * Setelah tipe sudah sama, buat unique dan foreign key.
         */
        Schema::table('penghuni', function (Blueprint $table) {
            $table->unique(
                'id_booking',
                'penghuni_id_booking_unique'
            );

            $table->foreign(
                'id_booking',
                'penghuni_id_booking_foreign'
            )
                ->references('id_booking')
                ->on('booking')
                ->nullOnDelete();
        });

        /*
         * Username boleh NULL karena akun penghuni
         * akan menggunakan email + password.
         *
         * Username admin tetap bisa digunakan.
         */
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')
                ->nullable()
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('penghuni', function (Blueprint $table) {
            $table->dropForeign(
                'penghuni_id_booking_foreign'
            );

            $table->dropUnique(
                'penghuni_id_booking_unique'
            );

            $table->dropColumn('id_booking');
        });

        /*
         * jenis_pembayaran dan periode_bayar tidak dihapus
         * karena keduanya sudah ada sebelum migration ini
         * diselesaikan.
         */
    }
};