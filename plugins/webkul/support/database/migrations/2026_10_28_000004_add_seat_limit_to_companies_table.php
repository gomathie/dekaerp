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
     * **Every company starts unlimited (`null`), new ones included** (user
     * decision, 2026-10-29; this supersedes the earlier "5 for new companies").
     * A limit is a commercial decision, and there is no screen to set one until
     * SAAS-12/13. A default of 5 meant the next client onboarded would be
     * stopped at their sixth user with no way to raise it except code. Existing
     * companies were already left unlimited for the same reason: a retroactive 5
     * would have stopped every company that already had five people.
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
