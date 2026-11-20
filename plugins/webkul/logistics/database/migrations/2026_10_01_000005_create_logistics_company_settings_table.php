<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // One row per company. Deliberately not a Spatie settings group:
        // CompanyAwareSettingsRepository falls back to the default company's values
        // for companies without their own row, which would silently switch
        // Logistics on for every company once the default company enables it.
        Schema::create('logistics_company_settings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')
                ->unique()
                ->constrained('companies')
                ->cascadeOnDelete();

            $table->boolean('is_enabled')->default(false)->index();
            $table->timestamp('enabled_at')->nullable();
            $table->timestamp('disabled_at')->nullable();

            $table->foreignId('enabled_by_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->boolean('require_pod_for_delivery')->default(true);
            $table->boolean('require_pod_photo')->default(false);
            $table->boolean('expense_approval_required')->default(true);
            $table->unsignedInteger('overdue_grace_minutes')->default(30);
            $table->unsignedInteger('free_waiting_minutes')->default(60);
            $table->unsignedInteger('stop_link_ttl_hours')->default(24);
            $table->string('capacity_check')->default('warn');

            $table->foreignId('default_service_type_id')
                ->nullable()
                ->constrained('logistics_service_types')
                ->nullOnDelete();

            $table->foreignId('invoice_journal_id')
                ->nullable()
                ->constrained('accounts_journals')
                ->nullOnDelete();

            $table->foreignId('bill_journal_id')
                ->nullable()
                ->constrained('accounts_journals')
                ->nullOnDelete();

            $table->foreignId('default_expense_account_id')
                ->nullable()
                ->constrained('accounts_accounts')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logistics_company_settings');
    }
};
