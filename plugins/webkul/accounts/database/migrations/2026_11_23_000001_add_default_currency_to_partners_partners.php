<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * A contact's default currency (TRACE365, user 2026-11-23): their invoices
 * are mostly in cedis, some customers and most suppliers in dollars. The
 * currency an invoice, bill or purchase order opens in is read through
 * Account\Support\DocumentCurrency. Nullable: no value keeps the old
 * behaviour, so existing contacts change nothing.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('partners_partners', 'default_currency_id')) {
            return;
        }

        Schema::table('partners_partners', function (Blueprint $table) {
            $table->foreignId('default_currency_id')
                ->nullable()
                ->comment('Default currency for this contact\'s invoices, bills and purchase orders')
                ->constrained('currencies')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('partners_partners', 'default_currency_id')) {
            return;
        }

        Schema::table('partners_partners', function (Blueprint $table) {
            $table->dropConstrainedForeignId('default_currency_id');
        });
    }
};
