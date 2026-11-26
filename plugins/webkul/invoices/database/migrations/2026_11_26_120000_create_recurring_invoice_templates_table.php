<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices_recurring_templates', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('partner_id')->constrained('partners_partners')->restrictOnDelete();
            $table->foreignId('journal_id')->constrained('accounts_journals')->restrictOnDelete();
            $table->foreignId('currency_id')->constrained('currencies')->restrictOnDelete();
            $table->foreignId('invoice_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('invoice_payment_term_id')->nullable()->constrained('accounts_payment_terms')->nullOnDelete();
            $table->foreignId('fiscal_position_id')->nullable()->constrained('accounts_fiscal_positions')->nullOnDelete();
            $table->string('name', 255);
            $table->string('frequency', 20);
            $table->date('next_invoice_date');
            $table->date('ends_on')->nullable();
            $table->string('state', 20)->default('active');
            $table->boolean('auto_email')->default(false);
            $table->text('narration')->nullable();
            $table->timestamp('last_generated_at')->nullable();
            $table->text('last_failure_reason')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'state', 'next_invoice_date'], 'invoices_recurring_due_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices_recurring_templates');
    }
};
