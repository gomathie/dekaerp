<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * How many seats a company may consume (D-SAAS-03/D-SAAS-04).
     *
     * `null` means unlimited, which is an approved state rather than a missing
     * value: an installation may sell an unlimited plan, and defaults of 0 would
     * silently block every company on upgrade.
     *
     * The default of 5 is a column default, so existing rows are backfilled by
     * the database in the same statement. It deliberately does **not** deactivate
     * or trim anyone: a company already over five members keeps every membership
     * it has, and is simply unable to add more until the limit is raised
     * (D-SAAS-04: lowering a limit never deactivates existing users).
     */
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table): void {
            $table->unsignedInteger('seat_limit')
                ->nullable()
                ->default(5)
                ->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table): void {
            $table->dropColumn('seat_limit');
        });
    }
};
