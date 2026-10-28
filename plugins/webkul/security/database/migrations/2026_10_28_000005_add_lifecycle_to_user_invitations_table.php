<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Gives an invitation a lifecycle instead of deleting it on acceptance.
     *
     * Until now a row meant "pending" and acceptance deleted it, so the only
     * record that someone had been invited was gone - and a second invitation
     * for the same address was indistinguishable from the first. Seats cannot be
     * counted against a state that does not exist (D-SAAS-03 counts valid
     * pending invitations as reservations), and "revoked" is an action support
     * has to be able to take.
     *
     * `expires_at` is backfilled from `created_at` + the accept window the signed
     * URL already used (`Invitation::ACCEPT_WINDOW_DAYS`), so existing pending
     * invitations keep exactly the deadline they were sent with.
     */
    public function up(): void
    {
        Schema::table('user_invitations', function (Blueprint $table): void {
            $table->timestamp('expires_at')->nullable()->after('role_ids');
            $table->timestamp('accepted_at')->nullable()->after('expires_at');
            $table->timestamp('revoked_at')->nullable()->after('accepted_at');

            $table->foreignId('revoked_by_id')
                ->nullable()
                ->after('revoked_at')
                ->constrained('users')
                ->nullOnDelete();

            $table->index(['default_company_id', 'accepted_at', 'revoked_at'], 'user_invitations_pending_company_index');
            $table->index('email');
        });

        // Portable backfill: computed in PHP rather than with a raw interval
        // expression, because this migration has to run on the MySQL dialect as
        // well as on PostgreSQL.
        DB::table('user_invitations')
            ->whereNull('expires_at')
            ->orderBy('id')
            ->chunkById(200, function ($rows): void {
                foreach ($rows as $row) {
                    DB::table('user_invitations')
                        ->where('id', $row->id)
                        ->update([
                            'expires_at' => Carbon::parse($row->created_at)->addDays(7),
                        ]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('user_invitations', function (Blueprint $table): void {
            $table->dropIndex('user_invitations_pending_company_index');
            $table->dropIndex(['email']);
            $table->dropConstrainedForeignId('revoked_by_id');
            $table->dropColumn(['expires_at', 'accepted_at', 'revoked_at']);
        });
    }
};
