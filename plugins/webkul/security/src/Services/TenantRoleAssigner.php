<?php

namespace Webkul\Security\Services;

use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\User;

/**
 * The one place a user's roles are written.
 *
 * Two stores, one meaning: platform roles go to `model_has_roles` (global, they
 * administer the installation), tenant roles go to `company_membership_roles`
 * (one row per company the user holds them in). Splitting the write here is what
 * keeps the Users page, the invitation flow and any future import agreeing;
 * before this, each path wrote `syncRoles()` and every role came out global.
 *
 * While `user_allowed_companies` is still the membership list, a tenant role
 * selected on the form is granted in *every* company the user is a member of.
 * That preserves the old behaviour exactly; narrowing it per company is the
 * company-administration screen, not this service.
 */
class TenantRoleAssigner
{
    /**
     * @param  array<int, mixed>  $roleIds  Every role the user should end up holding.
     * @param  array<int, mixed>  $companyIds  The companies the tenant roles apply to.
     */
    public function sync(User $user, array $roleIds, array $companyIds): void
    {
        $roleIds = $this->normalizeIds($roleIds);

        // A tenant role exists only inside a membership (the table's composite
        // foreign key enforces it), so a company the user does not belong to is
        // dropped here rather than failing the whole save.
        $companyIds = array_values(array_intersect(
            $this->normalizeIds($companyIds),
            app(MultiCompanyAdminService::class)->assignedCompanyIds($user),
        ));

        [$platformRoleIds, $tenantRoleIds] = $this->partition($roleIds);

        DB::transaction(function () use ($user, $platformRoleIds, $tenantRoleIds, $companyIds): void {
            $user->syncRoles($platformRoleIds);

            if ($tenantRoleIds === [] || $companyIds === []) {
                DB::table('company_membership_roles')
                    ->where('user_id', $user->getKey())
                    ->delete();

                return;
            }

            DB::table('company_membership_roles')
                ->where('user_id', $user->getKey())
                ->where(function ($query) use ($tenantRoleIds, $companyIds): void {
                    $query
                        ->whereNotIn('role_id', $tenantRoleIds)
                        ->orWhereNotIn('company_id', $companyIds);
                })
                ->delete();

            $now = now();

            foreach ($companyIds as $companyId) {
                foreach ($tenantRoleIds as $roleId) {
                    DB::table('company_membership_roles')->insertOrIgnore([
                        'user_id'    => $user->getKey(),
                        'company_id' => $companyId,
                        'role_id'    => $roleId,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        });

        $user->unsetRelation('roles');

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Splits role ids into installation-wide roles and tenant roles.
     *
     * @param  array<int, int>  $roleIds
     * @return array{0: array<int, int>, 1: array<int, int>}
     */
    public function partition(array $roleIds): array
    {
        if ($roleIds === []) {
            return [[], []];
        }

        $platformNames = Role::getSystemRoleNames();

        $roles = Role::query()
            ->whereIn('id', $roleIds)
            ->get(['id', 'name']);

        $platformRoleIds = [];
        $tenantRoleIds = [];

        foreach ($roles as $role) {
            $isPlatform = in_array(mb_strtolower(trim((string) $role->name)), $platformNames, true);

            if ($isPlatform) {
                $platformRoleIds[] = (int) $role->id;
            } else {
                $tenantRoleIds[] = (int) $role->id;
            }
        }

        return [$platformRoleIds, $tenantRoleIds];
    }

    /**
     * @return array<int, int>
     */
    protected function normalizeIds(array $ids): array
    {
        return collect($ids)
            ->filter(fn ($id): bool => is_numeric($id))
            ->map(fn ($id): int => (int) $id)
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->all();
    }
}
