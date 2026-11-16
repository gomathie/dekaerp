<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\PermissionRegistrar;
use Webkul\Security\PermissionRegistrar as ForkPermissionRegistrar;

/**
 * Makes each existing company's own administrator its "Company Admin"
 * (user, 2026-11-11).
 *
 * Companies onboarded before the company form could create an administrator
 * had their administrator given "Admin2" by hand, never "Company Admin". So
 * the company page listed nobody, and Show password could never apply.
 *
 * Conservative on purpose:
 * - only a company with no Company Admin yet;
 * - only an active, non-deleted member who holds "Admin2" in that company and
 *   no platform role;
 * - only when exactly one member qualifies, so it never guesses between two
 *   people. Ambiguous companies are logged for a person to decide.
 *
 * Passwords and existing roles are untouched. Rollback leaves the rows (see
 * down()).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('company_membership_roles')) {
            return;
        }

        $companyAdminRoleId = $this->roleId('company admin');
        $businessRoleId = $this->roleId('admin2');

        if (! $companyAdminRoleId || ! $businessRoleId) {
            return;
        }

        $platformUserIds = DB::table('model_has_roles')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('model_has_roles.model_type', 'like', '%User')
            ->whereIn(DB::raw('LOWER(roles.name)'), ['admin', 'super_admin', 'multi-company admin'])
            ->pluck('model_has_roles.model_id')
            ->all();

        $companyIds = DB::table('companies')->pluck('id');

        foreach ($companyIds as $companyId) {
            $hasCompanyAdmin = DB::table('company_membership_roles')
                ->where('company_id', $companyId)
                ->where('role_id', $companyAdminRoleId)
                ->exists();

            if ($hasCompanyAdmin) {
                continue;
            }

            $candidates = DB::table('company_membership_roles')
                ->join('users', 'users.id', '=', 'company_membership_roles.user_id')
                ->where('company_membership_roles.company_id', $companyId)
                ->where('company_membership_roles.role_id', $businessRoleId)
                ->whereNull('users.deleted_at')
                ->where('users.is_active', true)
                ->whereNotIn('users.id', $platformUserIds)
                ->pluck('users.id')
                ->unique()
                ->values();

            if ($candidates->count() !== 1) {
                if ($candidates->count() > 1) {
                    Log::notice('Company Admin backfill skipped: more than one candidate.', [
                        'company_id' => $companyId,
                        'user_ids'   => $candidates->all(),
                    ]);
                }

                continue;
            }

            DB::table('company_membership_roles')->insertOrIgnore([
                'user_id'    => $candidates->first(),
                'company_id' => $companyId,
                'role_id'    => $companyAdminRoleId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Log::notice('Company Admin backfill: member made Company Admin.', [
                'company_id' => $companyId,
                'user_id'    => $candidates->first(),
            ]);
        }

        $this->flushPermissionCaches();
    }

    /**
     * Left in place on rollback. These rows cannot be told apart from Company
     * Admin rows the company form creates later, and removing the wrong one
     * would lock a client's administrator out of their own users.
     */
    public function down(): void {}

    protected function roleId(string $lowerName): ?int
    {
        $id = DB::table('roles')
            ->where('guard_name', 'web')
            ->whereRaw('LOWER(name) = ?', [$lowerName])
            ->value('id');

        return $id ? (int) $id : null;
    }

    /** Both registrars hold separate caches (see AGENTS.md). */
    protected function flushPermissionCaches(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        app(ForkPermissionRegistrar::class)->forgetCachedPermissions();
    }
};
