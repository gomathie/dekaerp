<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Per-company POD capture options, and the fields they record (WP-5b follow-up).
 *
 * Added rather than folded into the WP-1 migrations: those have run on installed
 * databases, and editing them would leave any company that already installed
 * Logistics without these columns.
 *
 * Every option is off by default, so an existing company's proofs keep working
 * exactly as before until someone turns one on.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('logistics_company_settings', function (Blueprint $table) {
            // Record which driver was at the door, taken from the stop's trip.
            $table->boolean('capture_driver_on_pod')->default(false);

            // Ask the recipient for an identity reference. Off by default and
            // deliberately free text: what counts as ID differs by country, and
            // this must not become a national-ID field by assumption.
            $table->boolean('capture_recipient_id')->default(false);

            // Per-tenant stop-link rate limit, per token per minute. Null means
            // the application default.
            $table->unsignedSmallInteger('stop_link_rate_limit')->nullable();
        });

        Schema::table('logistics_delivery_proofs', function (Blueprint $table) {
            $table->string('recipient_id_reference')->nullable();

            $table->foreignId('driver_id')
                ->nullable()
                ->constrained('logistics_drivers')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('logistics_delivery_proofs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('driver_id');

            $table->dropColumn('recipient_id_reference');
        });

        Schema::table('logistics_company_settings', function (Blueprint $table) {
            $table->dropColumn([
                'capture_driver_on_pod',
                'capture_recipient_id',
                'stop_link_rate_limit',
            ]);
        });
    }
};
