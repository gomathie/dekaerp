<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Accounting plan #7, A2 (2026-11-12): a per-company "lock entries up to" date.
 *
 * Append-only: every change is a new row, and the company's lock is its latest
 * row. The history is the audit trail of who closed or reopened a period, so
 * rows are never updated. A null lock_date means the company reopened every
 * period. A company without rows has no lock, so existing companies are
 * unaffected until someone sets one.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounts_lock_dates', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')
                ->comment('Company')
                ->constrained('companies')
                ->cascadeOnDelete();

            $table->date('lock_date')->nullable()->comment('Entries dated on or before this are locked');

            $table->date('previous_lock_date')->nullable()->comment('Lock date before this change');

            $table->foreignId('changed_by')
                ->nullable()
                ->comment('Changed By')
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(['company_id', 'id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts_lock_dates');
    }
};
