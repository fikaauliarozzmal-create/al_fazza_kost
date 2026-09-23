<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('landing_hero_slides')) {
            return;
        }

        Schema::create('landing_hero_slides', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 150);
            $table->string('subjudul', 255)->nullable();
            $table->string('lokasi', 20);
            $table->string('foto')->nullable();
            $table->unsignedInteger('urutan')->default(1);
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_hero_slides');
    }
};