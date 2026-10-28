<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * How many seats a company may consume (D-SAAS-03/D-SAAS-04).
     *
     * Five is the default for a company created from now on. **Existing
     * companies are explicitly left unlimited**, because a limit is a commercial
     * decision that has not been made per tenant yet: applying 5 retroactively
     * would stop every production company that already has five or more people
     * from inviting anyone on the day this deploys - including inviting the
     * administrator of a company that is mid-onboarding. That is a support
     * incident, not a migration.
     *
     * `null` means unlimited, which is an approved state rather than a missing
     * value. Setting a limit is deliberate and audited
     * (`SeatCapacityService::setLimit()`), and lowering one never deactivates
     * anybody (D-SAAS-04): the company simply cannot add members until usage
     * falls or the limit rises.
     */
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table): void {
            $table->unsignedInteger('seat_limit')
                ->nullable()
                ->default(5)
                ->after('is_active');
        });

        // The column default fills existing rows with 5, so it is overwritten
        // here rather than relied on. New rows keep the default.
        DB::table('companies')->update(['seat_limit' => null]);
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table): void {
            $table->dropColumn('seat_limit');
        });
    }
};
