<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Test or commercial, chosen when the company is created (D-SAAS-11).
     *
     * Existing rows are commercial: a trial is opted into, and a production
     * database already has companies. A test row must name its end, so a
     * missing date cannot become an open-ended free account.
     */
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table): void {
            $table->string('account_type', 16)->default('commercial');
            $table->timestampTz('test_ends_at')->nullable();
        });

        DB::statement("ALTER TABLE companies ADD CONSTRAINT companies_account_type_check CHECK (account_type IN ('test', 'commercial'))");
        DB::statement("ALTER TABLE companies ADD CONSTRAINT companies_test_ends_check CHECK ((account_type = 'commercial' AND test_ends_at IS NULL) OR (account_type = 'test' AND test_ends_at IS NOT NULL))");
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE companies DROP CONSTRAINT IF EXISTS companies_test_ends_check');
        DB::statement('ALTER TABLE companies DROP CONSTRAINT IF EXISTS companies_account_type_check');

        Schema::table('companies', function (Blueprint $table): void {
            $table->dropColumn(['account_type', 'test_ends_at']);
        });
    }
};
