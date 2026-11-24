<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * "Ask which currency each sale is in" (plan #11 MC-2, TRACE365, user
 * 2026-11-24).
 *
 * - accounts_company_settings: one row per company, off by default. Its own
 *   table, not CompanyAwareSettingsRepository, which falls back to the
 *   default company's values: one company switching it on would switch it
 *   on for every company without a row.
 * - accounts_account_moves.currency_rate_source: "manual" when the person
 *   creating the invoice typed or confirmed a rate other than the feed's.
 *   Move::computeInvoiceCurrencyRate() then keeps the stored rate instead of
 *   re-reading the rate table on every save. Null (every existing invoice)
 *   keeps today's behaviour.
 * - currency_rate_confirmed_by: who confirmed it, for the audit trail.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('accounts_company_settings')) {
            Schema::create('accounts_company_settings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->unique()->constrained('companies')->cascadeOnDelete();
                $table->boolean('ask_sale_currency')->default(false)->comment('Ask which currency each new sale is in');
                $table->timestamps();
            });
        }

        Schema::table('accounts_account_moves', function (Blueprint $table) {
            if (! Schema::hasColumn('accounts_account_moves', 'currency_rate_source')) {
                $table->string('currency_rate_source', 16)->nullable()->comment('feed or manual; manual keeps invoice_currency_rate on recompute');
            }

            if (! Schema::hasColumn('accounts_account_moves', 'currency_rate_confirmed_by')) {
                $table->foreignId('currency_rate_confirmed_by')->nullable()->constrained('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('accounts_account_moves', function (Blueprint $table) {
            if (Schema::hasColumn('accounts_account_moves', 'currency_rate_confirmed_by')) {
                $table->dropConstrainedForeignId('currency_rate_confirmed_by');
            }

            if (Schema::hasColumn('accounts_account_moves', 'currency_rate_source')) {
                $table->dropColumn('currency_rate_source');
            }
        });

        Schema::dropIfExists('accounts_company_settings');
    }
};
