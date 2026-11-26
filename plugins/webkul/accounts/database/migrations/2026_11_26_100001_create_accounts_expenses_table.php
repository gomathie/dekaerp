<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Expenses (plan #10 Z1, user 2026-11-26): a cost paid straight from a bank
 * or cash journal, recorded in one screen. Each row owns exactly one journal
 * entry (move_id), posted in the journal the money left (Z2). The row holds
 * what the person typed; the entry is what the books show, and it is
 * rewritten from the row by Account\Services\ExpenseRecorder only.
 *
 * A new table, so existing data is untouched. Rolling back drops only this
 * table; the entries it posted stay in the ledger as ordinary entries.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounts_expenses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')
                ->comment('Company')
                ->constrained('companies')
                ->restrictOnDelete();

            $table->string('number')->comment('Expense number, per company');

            $table->date('date')->comment('Date paid');

            $table->foreignId('currency_id')
                ->comment('Currency paid in')
                ->constrained('currencies')
                ->restrictOnDelete();

            $table->decimal('amount', 15, 4)->comment('Amount typed, in the expense currency');

            $table->decimal('currency_rate', 20, 10)->default(1)->comment('Company to expense currency rate used');

            $table->decimal('amount_total', 15, 4)->default(0)->comment('Paid out, in the expense currency, tax included');

            $table->decimal('amount_tax', 15, 4)->default(0)->comment('Tax, in the expense currency');

            $table->decimal('amount_total_company', 15, 4)->default(0)->comment('Paid out, in the company currency');

            $table->foreignId('expense_account_id')
                ->comment('Expense account debited')
                ->constrained('accounts_accounts')
                ->restrictOnDelete();

            $table->foreignId('journal_id')
                ->comment('Bank or cash journal paid from')
                ->constrained('accounts_journals')
                ->restrictOnDelete();

            $table->foreignId('partner_id')
                ->nullable()
                ->comment('Vendor')
                ->constrained('partners_partners')
                ->nullOnDelete();

            $table->foreignId('tax_id')
                ->nullable()
                ->comment('Tax')
                ->constrained('accounts_taxes')
                ->restrictOnDelete();

            $table->string('reference')->nullable()->comment('Reference');

            $table->text('note')->nullable()->comment('Note');

            $table->string('receipt_path')->nullable()->comment('Receipt file, inside the company storage folder');

            $table->string('receipt_name')->nullable()->comment('Receipt file name as uploaded');

            $table->foreignId('move_id')
                ->nullable()
                ->comment('Journal entry posted for this expense')
                ->constrained('accounts_account_moves')
                ->restrictOnDelete();

            $table->foreignId('creator_id')
                ->nullable()
                ->comment('Created By')
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->softDeletes();

            $table->unique(['company_id', 'number']);

            // One expense per entry: the Z2 journal exception reads this link.
            $table->unique('move_id');

            $table->index(['company_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts_expenses');
    }
};
