<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Bank reconciliation (accounting plan A3). A statement line needs its own
 * value date (a statement covers many days), and a matched line records the
 * payment it settled so the match can be shown and audited. Both nullable:
 * existing rows (none in production on 2026-11-13) stay valid.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('accounts_bank_statement_lines', function (Blueprint $table) {
            $table->date('date')->nullable()->after('statement_id')->comment('Value Date');
            $table->foreignId('payment_id')->nullable()->after('move_id')->comment('Matched Payment')->constrained('accounts_account_payments')->nullOnDelete();
            $table->index(['journal_id', 'is_reconciled']);
        });
    }

    public function down(): void
    {
        Schema::table('accounts_bank_statement_lines', function (Blueprint $table) {
            $table->dropIndex(['journal_id', 'is_reconciled']);
            $table->dropConstrainedForeignId('payment_id');
            $table->dropColumn('date');
        });
    }
};
