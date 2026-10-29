<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Contact tags become per company (user decision, 2026-10-29;
 * docs/company-admin-role-plan.md §4k).
 *
 * Nullable on purpose: CompanyScope reads a null company as *shared*, and the
 * existing rows are filed by an explicit, reviewed backfill
 * (`products:company-backfill`), never by a migration that would run on test
 * and fresh databases.
 *
 * The global unique index on `name` is replaced by a per-company one. Left in
 * place it would stop a second company creating a tag named like the first
 * company's - and the partners tag API has no validation rule for it, so it
 * surfaced as a 500.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('partners_tags', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('id')->constrained('companies')->nullOnDelete();

            $table->dropUnique(['name']);
            $table->unique(['company_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::table('partners_tags', function (Blueprint $table) {
            $table->dropUnique(['company_id', 'name']);

            $table->unique('name');

            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });
    }
};
