<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices_recurring_periods', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('recurring_template_id')->constrained('invoices_recurring_templates')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->date('period_start');
            $table->date('period_end');
            $table->foreignId('move_id')->nullable()->constrained('accounts_account_moves')->nullOnDelete();
            $table->string('status', 20)->default('created');
            $table->text('failure_reason')->nullable();
            $table->timestamps();

            // Unique ownership of a scheduled period makes queue retries idempotent.
            $table->unique(['recurring_template_id', 'period_start'], 'invoices_recurring_period_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices_recurring_periods');
    }
};
