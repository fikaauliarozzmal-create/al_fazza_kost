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
        Schema::table('users', function (Blueprint $table) {
    $table->string('no_whatsapp')->nullable()->after('name');
    $table->string('username')->unique()->after('no_whatsapp');
    $table->enum('role', ['Admin', 'User', 'Super Admin'])->default('User')->after('password');
    $table->enum('status_akun', ['Aktif', 'Tidak Aktif'])->default('Tidak Aktif')->after('role');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
