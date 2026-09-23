<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            if (! Schema::hasColumn('pembayaran', 'gateway_provider')) {
                $table->string('gateway_provider', 30)->nullable()->after('alasan_penolakan');
                $table->string('gateway_partner_reference', 64)->nullable()->unique();
                $table->string('gateway_external_id', 36)->nullable()->unique();
                $table->string('gateway_reference', 64)->nullable()->index();
                $table->string('gateway_status', 40)->nullable()->index();
                $table->text('gateway_redirect_url')->nullable();
                $table->dateTime('gateway_expires_at')->nullable();
                $table->dateTime('gateway_notified_at')->nullable();
                $table->json('gateway_metadata')->nullable();
            }
        });
    }

    public function down(): void
    {
        // Gateway records are retained to preserve auditability of payments.
    }
};
