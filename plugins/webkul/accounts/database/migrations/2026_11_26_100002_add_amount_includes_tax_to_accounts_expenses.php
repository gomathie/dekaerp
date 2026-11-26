<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * "Amount includes tax" on an expense (user decision B, 2026-11-26).
 *
 * Nullable on purpose: null means "follow the tax's own setting", which is
 * what every expense saved before this column did, so existing rows keep
 * exactly the meaning they were posted with and need no backfill. New saves
 * store the switch as chosen. Rollback drops only this column.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('accounts_expenses', function (Blueprint $table) {
            $table->boolean('amount_includes_tax')
                ->nullable()
                ->after('tax_id')
                ->comment('Amount typed includes the tax; null = the tax\'s own setting');
        });
    }

    public function down(): void
    {
        Schema::table('accounts_expenses', function (Blueprint $table) {
            $table->dropColumn('amount_includes_tax');
        });
    }
};
