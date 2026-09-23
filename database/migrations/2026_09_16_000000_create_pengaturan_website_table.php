<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('pengaturan_website')) {
            Schema::create('pengaturan_website', function (Blueprint $table) {
                $table->id();
                $table->string('lokasi', 20)->unique();
                $table->string('nama_kost', 100);
                $table->text('alamat')->nullable();
                $table->string('whatsapp', 30)->nullable();
                $table->text('google_maps_url')->nullable();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('pengaturan_website') && DB::table('pengaturan_website')->count() === 0) {
            DB::table('pengaturan_website')->insert([
                [
                    'lokasi' => 'kost1',
                    'nama_kost' => 'Al Fazza Kost 1',
                    'alamat' => 'Karangmanyar, Purbalingga',
                    'whatsapp' => null,
                    'google_maps_url' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'lokasi' => 'kost2',
                    'nama_kost' => 'Al Fazza Kost 2',
                    'alamat' => 'Toyareka, Purbalingga',
                    'whatsapp' => null,
                    'google_maps_url' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaturan_website');
    }
};