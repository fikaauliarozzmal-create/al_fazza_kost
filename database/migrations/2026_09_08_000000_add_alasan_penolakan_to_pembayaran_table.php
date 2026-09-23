<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('pembayaran', 'alasan_penolakan')) {
            Schema::table('pembayaran', function (Blueprint $table) {
                $table->text('alasan_penolakan')->nullable()->after('status_bayar');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('pembayaran', 'alasan_penolakan')) {
            Schema::table('pembayaran', function (Blueprint $table) {
                $table->dropColumn('alasan_penolakan');
            });
        }
    }
};
