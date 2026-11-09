<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * One extension per invoice and kind (D-SAAS-04). Days are added to the
     * read-only instant. The row is the audit, so it cannot be edited to
     * grant the same slot twice.
     */
    public function up(): void
    {
        Schema::create('platform_billing_overdue_extensions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->restrictOnDelete();
            $table->foreignId('invoice_id')->constrained('platform_billing_invoices')->restrictOnDelete();
            $table->string('kind', 32);
            $table->unsignedInteger('days');
            $table->text('reason')->nullable();
            $table->foreignId('granted_by')->constrained('users')->restrictOnDelete();
            $table->timestampTz('granted_at');
            $table->timestamps();
            $table->unique(['invoice_id', 'kind'], 'platform_billing_overdue_extensions_invoice_kind_unique');
        });

        DB::statement("ALTER TABLE platform_billing_overdue_extensions ADD CONSTRAINT platform_billing_overdue_extensions_kind_check CHECK (kind IN ('company_admin', 'super_admin'))");
        DB::statement('ALTER TABLE platform_billing_overdue_extensions ADD CONSTRAINT platform_billing_overdue_extensions_days_check CHECK (days > 0)');

        // migrate:fresh drops the table and leaves the function behind.
        DB::unprepared('DROP FUNCTION IF EXISTS platform_billing_overdue_extensions_are_immutable()');
        DB::unprepared(<<<'SQL'
            CREATE FUNCTION platform_billing_overdue_extensions_are_immutable() RETURNS trigger AS $$
            BEGIN
                RAISE EXCEPTION 'Posted overdue extensions are immutable.';
            END;
            $$ LANGUAGE plpgsql
        SQL);
        DB::unprepared('CREATE TRIGGER platform_billing_overdue_extensions_immutable BEFORE UPDATE OR DELETE ON platform_billing_overdue_extensions FOR EACH ROW EXECUTE FUNCTION platform_billing_overdue_extensions_are_immutable()');
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS platform_billing_overdue_extensions_immutable ON platform_billing_overdue_extensions');
        DB::unprepared('DROP FUNCTION IF EXISTS platform_billing_overdue_extensions_are_immutable()');
        Schema::dropIfExists('platform_billing_overdue_extensions');
    }
};
