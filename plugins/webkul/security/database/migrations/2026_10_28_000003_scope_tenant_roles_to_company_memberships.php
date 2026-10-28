<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\User;

return new class extends Migration
{
    /**
     * Moves tenant roles off the global pivot and onto each membership.
     *
     * Until now every role was a property of the user: assign it once and it
     * applied in every company that user belonged to. That is what made "admin
     * in A, ordinary in B" impossible (see the SAAS-00 characterization test).
     *
     * The move is deliberately conservative:
     *
     *  - Only non-system roles move. Super Admin, Multi-Company Admin and the
     *    install administrator role stay in `model_has_roles`, because they
     *    administer the installation rather than a tenant.
     *  - Each moved role is granted in **every** company the user is a member
     *    of, which is precisely the access they had a moment before. Nobody
     *    gains or loses anything until an administrator deliberately narrows a
     *    membership.
     *  - A user holding a tenant role but no membership row keeps the global
     *    grant and is logged, rather than being silently stripped of access.
     *
     * `down()` writes the union back to `model_has_roles`, so a rollback
     * restores the pre-migration access set (a user who was admin in A and
     * ordinary in B comes back as admin everywhere, which is what the old
     * schema could express).
     */
    public function up(): void
    {
        if (! Schema::hasTable('company_membership_roles')) {
            return;
        }

        $morphClass = (new User)->getMorphClass();
        $platformNames = Role::getSystemRoleNames();

        $assignments = DB::table('model_has_roles')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('model_has_roles.model_type', $morphClass)
            ->get(['model_has_roles.role_id', 'model_has_roles.model_id', 'roles.name']);

        $now = now();
        $unscoped = 0;

        foreach ($assignments as $assignment) {
            if (in_array(mb_strtolower(trim($assignment->name)), $platformNames, true)) {
                continue;
            }

            $companyIds = DB::table('user_allowed_companies')
                ->where('user_id', $assignment->model_id)
                ->pluck('company_id')
                ->map(fn ($id): int => (int) $id)
                ->unique()
                ->values()
                ->all();

            if ($companyIds === []) {
                $unscoped++;

                continue;
            }

            foreach ($companyIds as $companyId) {
                DB::table('company_membership_roles')->insertOrIgnore([
                    'user_id'    => (int) $assignment->model_id,
                    'company_id' => $companyId,
                    'role_id'    => (int) $assignment->role_id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            DB::table('model_has_roles')
                ->where('role_id', $assignment->role_id)
                ->where('model_id', $assignment->model_id)
                ->where('model_type', $morphClass)
                ->delete();
        }

        if ($unscoped > 0) {
            Log::warning('Tenant roles left global because the user has no company membership.', [
                'assignments' => $unscoped,
            ]);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('company_membership_roles')) {
            return;
        }

        $morphClass = (new User)->getMorphClass();

        DB::table('company_membership_roles')
            ->get(['user_id', 'role_id'])
            ->each(function (object $row) use ($morphClass): void {
                DB::table('model_has_roles')->insertOrIgnore([
                    'role_id'    => (int) $row->role_id,
                    'model_id'   => (int) $row->user_id,
                    'model_type' => $morphClass,
                ]);
            });
    }
};
