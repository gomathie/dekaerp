<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * A rate the person confirmed on a quotation or sales order (plan #10
 * MC-2b, TRACE365, user 2026-11-24), as on invoices (accounts migration
 * 2026_11_24_000001):
 * - currency_rate_source: "manual" keeps sales_orders.currency_rate through
 *   every recompute (OrderCalculator); null follows the daily feed.
 * - currency_rate_confirmed_by: who confirmed it, for the audit trail.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            if (! Schema::hasColumn('sales_orders', 'currency_rate_source')) {
                $table->string('currency_rate_source', 16)->nullable()->comment('feed or manual; manual keeps currency_rate on recompute');
            }

            if (! Schema::hasColumn('sales_orders', 'currency_rate_confirmed_by')) {
                $table->foreignId('currency_rate_confirmed_by')->nullable()->constrained('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            if (Schema::hasColumn('sales_orders', 'currency_rate_confirmed_by')) {
                $table->dropConstrainedForeignId('currency_rate_confirmed_by');
            }

            if (Schema::hasColumn('sales_orders', 'currency_rate_source')) {
                $table->dropColumn('currency_rate_source');
            }
        });
    }
};
