<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('pengaturan_website', 'logo_path')) {
            Schema::table('pengaturan_website', function (Blueprint $table) {
                $table->string('logo_path')->nullable()->after('nama_kost');
            });
        }
    }

    public function down(): void
    {
        // Path logo dipertahankan agar perubahan branding tetap dapat diaudit.
    }
};
