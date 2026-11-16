<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Additive SAAS-10 invoices. One row per subscription period, snapshotted
     * at posting, and never updated: a later tax rate, issuer or discount edit
     * must not rewrite a document that was already issued (D-SAAS-05, D-SAAS-08).
     */
    public function up(): void
    {
        Schema::create('platform_billing_invoices', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->restrictOnDelete();
            $table->foreignId('subscription_id')->constrained('platform_billing_subscriptions')->restrictOnDelete();
            $table->timestampTz('period_start_at');
            $table->timestampTz('period_end_at');
            $table->string('number', 32);
            $table->unsignedSmallInteger('document_year');
            $table->unsignedInteger('sequence');
            $table->timestampTz('issued_at');
            $table->timestampTz('due_at');
            $table->timestampTz('grace_starts_at');
            $table->char('currency', 3);
            $table->string('issuer_name', 160);
            $table->string('tax_rate', 16);
            $table->boolean('tax_enabled');
            $table->unsignedBigInteger('subtotal_minor');
            $table->unsignedBigInteger('discount_minor');
            $table->unsignedBigInteger('tax_minor');
            $table->unsignedBigInteger('total_minor');
            $table->foreignId('discount_redemption_id')->nullable()->constrained('platform_billing_discount_redemptions')->restrictOnDelete();
            $table->string('discount_code', 100)->nullable();
            $table->string('discount_type', 24)->nullable();
            $table->unsignedBigInteger('discount_value')->nullable();
            $table->boolean('free_month_applied')->default(false);
            $table->string('status', 16);
            $table->timestamps();
            $table->unique(['subscription_id', 'period_start_at'], 'platform_billing_invoices_period_unique');
            $table->unique('number', 'platform_billing_invoices_number_unique');
            $table->index(['company_id', 'issued_at'], 'platform_billing_invoices_company_issued_idx');
        });

        Schema::create('platform_billing_invoice_lines', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('invoice_id')->constrained('platform_billing_invoices')->restrictOnDelete();
            $table->foreignId('company_id')->constrained('companies')->restrictOnDelete();
            $table->unsignedSmallInteger('position');
            $table->string('description');
            $table->unsignedBigInteger('amount_minor');
            $table->timestamps();
            $table->unique(['invoice_id', 'position'], 'platform_billing_invoice_lines_position_unique');
        });

        DB::statement("ALTER TABLE platform_billing_invoices ADD CONSTRAINT platform_billing_invoices_currency_check CHECK (currency = 'USD')");
        DB::statement("ALTER TABLE platform_billing_invoices ADD CONSTRAINT platform_billing_invoices_status_check CHECK (status = 'posted')");
        DB::statement('ALTER TABLE platform_billing_invoices ADD CONSTRAINT platform_billing_invoices_discount_check CHECK (discount_minor <= subtotal_minor)');
        DB::statement('ALTER TABLE platform_billing_invoices ADD CONSTRAINT platform_billing_invoices_total_check CHECK (total_minor = subtotal_minor - discount_minor + tax_minor)');

        // migrate:fresh drops tables without running down(), so a function left
        // by an earlier suite makes the next CREATE FUNCTION fail for every test.
        DB::unprepared('DROP FUNCTION IF EXISTS platform_billing_posted_invoices_are_immutable()');
        DB::unprepared(<<<'SQL'
            CREATE FUNCTION platform_billing_posted_invoices_are_immutable() RETURNS trigger AS $$
            BEGIN
                RAISE EXCEPTION 'Posted platform invoices are immutable.';
            END;
            $$ LANGUAGE plpgsql
        SQL);
        DB::unprepared(<<<'SQL'
            CREATE TRIGGER platform_billing_invoices_immutable
            BEFORE UPDATE OR DELETE ON platform_billing_invoices
            FOR EACH ROW EXECUTE FUNCTION platform_billing_posted_invoices_are_immutable()
        SQL);
        DB::unprepared(<<<'SQL'
            CREATE TRIGGER platform_billing_invoice_lines_immutable
            BEFORE UPDATE OR DELETE ON platform_billing_invoice_lines
            FOR EACH ROW EXECUTE FUNCTION platform_billing_posted_invoices_are_immutable()
        SQL);
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS platform_billing_invoice_lines_immutable ON platform_billing_invoice_lines');
        DB::unprepared('DROP TRIGGER IF EXISTS platform_billing_invoices_immutable ON platform_billing_invoices');
        DB::unprepared('DROP FUNCTION IF EXISTS platform_billing_posted_invoices_are_immutable()');
        Schema::dropIfExists('platform_billing_invoice_lines');
        Schema::dropIfExists('platform_billing_invoices');
    }
};
