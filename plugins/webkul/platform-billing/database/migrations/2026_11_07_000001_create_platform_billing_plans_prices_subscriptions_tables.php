<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Additive SAAS-09 storage. A subscription copies sold terms so later edits
     * to a catalog price or plugin list cannot silently rewrite a tenant deal.
     */
    public function up(): void
    {
        Schema::create('platform_billing_plans', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->jsonb('included_plugins');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('platform_billing_prices', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('plan_id')->constrained('platform_billing_plans')->restrictOnDelete();
            $table->char('currency', 3);
            $table->unsignedSmallInteger('interval_months');
            $table->unsignedBigInteger('amount_minor');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['plan_id', 'currency', 'interval_months']);
        });

        Schema::create('platform_billing_subscriptions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->restrictOnDelete();
            $table->foreignId('plan_id')->constrained('platform_billing_plans')->restrictOnDelete();
            $table->foreignId('price_id')->constrained('platform_billing_prices')->restrictOnDelete();
            $table->string('status', 32)->default('draft');
            $table->string('plan_name');
            $table->jsonb('included_plugins');
            $table->char('currency', 3);
            $table->unsignedSmallInteger('interval_months');
            $table->unsignedBigInteger('amount_minor');
            $table->string('timezone', 64);
            $table->timestampTz('anchor_at');
            $table->timestampTz('period_start_at')->nullable();
            $table->timestampTz('period_end_at')->nullable();
            $table->timestampTz('next_invoice_at')->nullable();
            $table->timestamps();
            $table->index(['company_id', 'status']);
            $table->index(['status', 'next_invoice_at']);
        });

        // PostgreSQL guards prices even when a write bypasses Eloquent.
        DB::statement('ALTER TABLE platform_billing_prices ADD CONSTRAINT platform_billing_prices_interval_check CHECK (interval_months IN (1, 3, 6, 12))');
        DB::statement('ALTER TABLE platform_billing_subscriptions ADD CONSTRAINT platform_billing_subscriptions_interval_check CHECK (interval_months IN (1, 3, 6, 12))');
    }

    public function down(): void
    {
        Schema::dropIfExists('platform_billing_subscriptions');
        Schema::dropIfExists('platform_billing_prices');
        Schema::dropIfExists('platform_billing_plans');
    }
};
