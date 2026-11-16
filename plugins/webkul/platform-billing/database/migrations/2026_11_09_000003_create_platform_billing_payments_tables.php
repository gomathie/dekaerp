<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Additive SAAS-11 ledger (D-SAAS-10). Payments, allocations and refunds
     * are posted once. Balances are derived from these rows, so nothing here
     * stores a running total that a later edit could leave behind.
     */
    public function up(): void
    {
        Schema::create('platform_billing_payments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->restrictOnDelete();
            $table->string('idempotency_key', 190);
            $table->unsignedBigInteger('amount_minor');
            $table->char('currency', 3);
            $table->timestampTz('received_at');
            $table->foreignId('posted_by')->constrained('users')->restrictOnDelete();
            $table->string('status', 16);
            $table->timestamps();
            $table->unique(['company_id', 'idempotency_key'], 'platform_billing_payments_idempotency_unique');
            $table->index(['company_id', 'received_at'], 'platform_billing_payments_company_received_idx');
        });

        Schema::create('platform_billing_allocations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->restrictOnDelete();
            $table->foreignId('payment_id')->constrained('platform_billing_payments')->restrictOnDelete();
            $table->foreignId('invoice_id')->constrained('platform_billing_invoices')->restrictOnDelete();
            $table->bigInteger('amount_minor');
            $table->foreignId('posted_by')->constrained('users')->restrictOnDelete();
            $table->timestampTz('posted_at');
            $table->timestamps();
            $table->index(['payment_id', 'invoice_id'], 'platform_billing_allocations_payment_invoice_idx');
        });

        Schema::create('platform_billing_reversals', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->restrictOnDelete();
            $table->foreignId('payment_id')->constrained('platform_billing_payments')->restrictOnDelete();
            $table->string('idempotency_key', 190);
            $table->unsignedBigInteger('amount_minor');
            $table->char('currency', 3);
            $table->text('reason');
            $table->timestampTz('effective_at');
            $table->foreignId('posted_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
            $table->unique(['company_id', 'idempotency_key'], 'platform_billing_reversals_idempotency_unique');
        });

        DB::statement("ALTER TABLE platform_billing_payments ADD CONSTRAINT platform_billing_payments_currency_check CHECK (currency = 'USD')");
        DB::statement("ALTER TABLE platform_billing_payments ADD CONSTRAINT platform_billing_payments_status_check CHECK (status = 'posted')");
        DB::statement('ALTER TABLE platform_billing_payments ADD CONSTRAINT platform_billing_payments_amount_check CHECK (amount_minor > 0)');
        DB::statement('ALTER TABLE platform_billing_allocations ADD CONSTRAINT platform_billing_allocations_amount_check CHECK (amount_minor <> 0)');
        DB::statement("ALTER TABLE platform_billing_reversals ADD CONSTRAINT platform_billing_reversals_currency_check CHECK (currency = 'USD')");
        DB::statement('ALTER TABLE platform_billing_reversals ADD CONSTRAINT platform_billing_reversals_amount_check CHECK (amount_minor > 0)');

        // migrate:fresh drops tables without running down(), so a function left
        // by an earlier suite makes the next CREATE FUNCTION fail for every test.
        DB::unprepared('DROP FUNCTION IF EXISTS platform_billing_posted_payments_are_immutable()');
        DB::unprepared(<<<'SQL'
            CREATE FUNCTION platform_billing_posted_payments_are_immutable() RETURNS trigger AS $$
            BEGIN
                RAISE EXCEPTION 'Posted platform payments are immutable.';
            END;
            $$ LANGUAGE plpgsql
        SQL);
        foreach (['platform_billing_payments', 'platform_billing_allocations', 'platform_billing_reversals'] as $table) {
            DB::unprepared(sprintf(
                'CREATE TRIGGER %s_immutable BEFORE UPDATE OR DELETE ON %s FOR EACH ROW EXECUTE FUNCTION platform_billing_posted_payments_are_immutable()',
                $table,
                $table,
            ));
        }
    }

    public function down(): void
    {
        foreach (['platform_billing_reversals', 'platform_billing_allocations', 'platform_billing_payments'] as $table) {
            DB::unprepared(sprintf('DROP TRIGGER IF EXISTS %s_immutable ON %s', $table, $table));
        }
        DB::unprepared('DROP FUNCTION IF EXISTS platform_billing_posted_payments_are_immutable()');
        Schema::dropIfExists('platform_billing_reversals');
        Schema::dropIfExists('platform_billing_allocations');
        Schema::dropIfExists('platform_billing_payments');
    }
};
