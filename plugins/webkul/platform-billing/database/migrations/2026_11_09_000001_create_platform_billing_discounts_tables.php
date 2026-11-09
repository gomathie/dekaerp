<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('platform_billing_discount_codes', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 100)->unique();
            $table->string('discount_type', 24);
            $table->unsignedBigInteger('discount_value');
            $table->string('duration_type', 24);
            $table->unsignedSmallInteger('duration_months')->nullable();
            $table->string('eligibility_type', 24);
            $table->unsignedInteger('max_redemptions')->nullable();
            $table->unsignedInteger('redemption_count')->default(0);
            $table->timestampTz('redemption_ends_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });

        Schema::create('platform_billing_discount_code_companies', function (Blueprint $table): void {
            $table->foreignId('discount_code_id')->constrained('platform_billing_discount_codes')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->primary(['discount_code_id', 'company_id'], 'platform_billing_discount_code_companies_pk');
            $table->index(['company_id', 'discount_code_id'], 'platform_billing_discount_code_companies_company_idx');
        });

        Schema::create('platform_billing_discount_redemptions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('discount_code_id')->constrained('platform_billing_discount_codes')->restrictOnDelete();
            $table->foreignId('company_id')->constrained('companies')->restrictOnDelete();
            $table->foreignId('redeemed_by')->constrained('users')->restrictOnDelete();
            $table->string('code_snapshot', 100);
            $table->string('discount_type', 24);
            $table->unsignedBigInteger('discount_value');
            $table->string('duration_type', 24);
            $table->unsignedSmallInteger('duration_months')->nullable();
            $table->timestampTz('redeemed_at');
            $table->timestampTz('duration_ends_at')->nullable();
            $table->timestamps();
            $table->unique(['discount_code_id', 'company_id'], 'platform_billing_discount_redemptions_code_company_unique');
            $table->index(['company_id', 'duration_ends_at'], 'platform_billing_discount_redemptions_company_duration_idx');
        });

        Schema::create('platform_billing_company_discount_slots', function (Blueprint $table): void {
            $table->foreignId('company_id')->primary()->constrained('companies')->cascadeOnDelete();
            $table->foreignId('redemption_id')->unique()->constrained('platform_billing_discount_redemptions')->cascadeOnDelete();
            $table->timestampTz('reserved_at');
        });

        DB::statement("ALTER TABLE platform_billing_discount_codes ADD CONSTRAINT platform_billing_discount_codes_type_check CHECK (discount_type IN ('percentage', 'fixed_usd', 'free_months'))");
        DB::statement("ALTER TABLE platform_billing_discount_codes ADD CONSTRAINT platform_billing_discount_codes_duration_check CHECK (duration_type IN ('one_invoice', 'months', 'forever'))");
        DB::statement("ALTER TABLE platform_billing_discount_codes ADD CONSTRAINT platform_billing_discount_codes_eligibility_check CHECK (eligibility_type IN ('any_company', 'company_list'))");
        DB::statement("ALTER TABLE platform_billing_discount_codes ADD CONSTRAINT platform_billing_discount_codes_duration_months_check CHECK ((duration_type = 'months' AND duration_months > 0) OR (duration_type <> 'months' AND duration_months IS NULL))");
        DB::statement("ALTER TABLE platform_billing_discount_codes ADD CONSTRAINT platform_billing_discount_codes_eligibility_list_check CHECK (eligibility_type = 'company_list' OR eligibility_type = 'any_company')");
        DB::statement("ALTER TABLE platform_billing_discount_codes ADD CONSTRAINT platform_billing_discount_codes_value_check CHECK (discount_value > 0)");
        DB::statement("ALTER TABLE platform_billing_discount_codes ADD CONSTRAINT platform_billing_discount_codes_percentage_check CHECK (discount_type <> 'percentage' OR discount_value <= 10000)");
        DB::statement('ALTER TABLE platform_billing_discount_codes ADD CONSTRAINT platform_billing_discount_codes_count_check CHECK (max_redemptions IS NULL OR redemption_count <= max_redemptions)');

        DB::unprepared(<<<'SQL'
            CREATE FUNCTION platform_billing_discount_redemptions_are_immutable() RETURNS trigger AS $$
            BEGIN
                RAISE EXCEPTION 'Discount redemptions are immutable.';
            END;
            $$ LANGUAGE plpgsql
        SQL);
        DB::unprepared(<<<'SQL'
            CREATE TRIGGER platform_billing_discount_redemptions_immutable
            BEFORE UPDATE OR DELETE ON platform_billing_discount_redemptions
            FOR EACH ROW EXECUTE FUNCTION platform_billing_discount_redemptions_are_immutable()
        SQL);
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS platform_billing_discount_redemptions_immutable ON platform_billing_discount_redemptions');
        DB::unprepared('DROP FUNCTION IF EXISTS platform_billing_discount_redemptions_are_immutable()');
        Schema::dropIfExists('platform_billing_company_discount_slots');
        Schema::dropIfExists('platform_billing_discount_redemptions');
        Schema::dropIfExists('platform_billing_discount_code_companies');
        Schema::dropIfExists('platform_billing_discount_codes');
    }
};
