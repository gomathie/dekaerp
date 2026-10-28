<?php

namespace Webkul\Security\Services;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\User;
use Webkul\Support\Services\CompanyContext;

/**
 * Tenant roles, answered per company rather than per session.
 *
 * A tenant role lives on `company_membership_roles` (user + company + role), so
 * "administrator in A, ordinary in B" is expressible. The one rule that matters
 * here: when a caller knows which company a record belongs to, it must ask with
 * that company - `hasPermissionInCompany()` - never with whatever happens to be
 * active. `CompanyContext::currentId()` is "first active company, else default",
 * which is a display convention, not an authorization fact.
 *
 * Company ids are read straight from the pivot rather than through a scoped
 * Eloquent model. This table deliberately has no CompanyScope: a policy
 * evaluating a record in a company the actor is *not* currently switched into
 * still has to get a truthful answer, and a scoped read would return nothing -
 * the failure mode that looks like "no role assigned" and silently denies (or,
 * with a `??=` fallback, silently grants).
 */
class TenantRoleResolver
{
    /**
     * Memoised per request (the resolver is a scoped singleton): `Schema::hasTable`
     * is a metadata query, and this gate is consulted by every permission check
     * on a page that renders a table of records.
     */
    protected ?bool $tableExists = null;

    /**
     * Whether the membership store has been migrated yet.
     *
     * Guards boot-time calls on an installation that has not run the SAAS-01
     * migrations: without it, every permission check would fail with "relation
     * does not exist" instead of simply answering from the global store.
     */
    public function supported(): bool
    {
        return $this->tableExists ??= Schema::hasTable('company_membership_roles');
    }

    /**
     * The companies whose membership grants this ability.
     *
     * This is the input a list restriction needs: `whereIn('company_id', ...)`
     * per resource, so a user who is administrator in A and an ordinary member
     * in B does not see B's rows in a combined list.
     *
     * @return array<int, int>
     */
    public function companyIdsHolding(User $user, string $ability): array
    {
        return $this->abilityQuery($user, $ability)
            ->distinct()
            ->pluck('company_membership_roles.company_id')
            ->map(fn ($id): int => (int) $id)
            ->sort()
            ->values()
            ->all();
    }

    /**
     * Whether any company the user can act in grants this ability.
     *
     * With no company argument this is the navigation question ("should this
     * menu entry appear"), so it asks about the companies active in this
     * session and falls back to every membership outside a web request. Pass a
     * company whenever a record is involved.
     */
    public function holdsAbility(User $user, string $ability, ?int $companyId = null): bool
    {
        $companyIds = $companyId === null ? $this->resolvableCompanyIds($user) : [$companyId];

        if ($companyIds === []) {
            return false;
        }

        return $this->abilityQuery($user, $ability)
            ->whereIn('company_membership_roles.company_id', $companyIds)
            ->exists();
    }

    /**
     * @return array<int, int>
     */
    public function roleIdsFor(User $user, ?int $companyId = null): array
    {
        $query = DB::table('company_membership_roles')->where('user_id', $user->getKey());

        if ($companyId !== null) {
            $query->where('company_id', $companyId);
        }

        return $query
            ->pluck('role_id')
            ->map(fn ($id): int => (int) $id)
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    /**
     * @return array<int, string>
     */
    public function roleNamesFor(User $user, ?int $companyId = null): array
    {
        $roleIds = $this->roleIdsFor($user, $companyId);

        if ($roleIds === []) {
            return [];
        }

        return Role::query()
            ->whereIn('id', $roleIds)
            ->pluck('name')
            ->all();
    }

    /**
     * Every permission a tenant role grants, for the companies in scope.
     *
     * Mirrors `User::getAllPermissions()` for the membership store. The role
     * assignment screen needs it: an actor's assignable set is "roles that grant
     * nothing they lack", and after the migration a Company Admin's permissions
     * are here rather than on the user's global roles.
     *
     * @return array<int, string>
     */
    public function permissionNamesFor(User $user, ?int $companyId = null): array
    {
        $companyIds = $companyId === null ? $this->resolvableCompanyIds($user) : [$companyId];

        if ($companyIds === []) {
            return [];
        }

        return DB::table('company_membership_roles')
            ->join('role_has_permissions', 'role_has_permissions.role_id', '=', 'company_membership_roles.role_id')
            ->join('permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
            ->where('company_membership_roles.user_id', $user->getKey())
            ->whereIn('company_membership_roles.company_id', $companyIds)
            ->distinct()
            ->pluck('permissions.name')
            ->all();
    }

    public function hasRoleInCompany(User $user, string $roleName, int $companyId): bool
    {
        return $this->roleQuery($user, $companyId)
            ->whereRaw('LOWER(roles.name) = ?', [mb_strtolower(trim($roleName))])
            ->exists();
    }

    /**
     * Users holding a role in one company's membership store.
     *
     * @return array<int, int>
     */
    public function userIdsWithRoleInCompany(string $roleName, int $companyId): array
    {
        return DB::table('company_membership_roles')
            ->join('roles', 'roles.id', '=', 'company_membership_roles.role_id')
            ->where('company_membership_roles.company_id', $companyId)
            ->whereRaw('LOWER(roles.name) = ?', [mb_strtolower(trim($roleName))])
            ->distinct()
            ->pluck('company_membership_roles.user_id')
            ->map(fn ($id): int => (int) $id)
            ->all();
    }

    /**
     * "Holds this role somewhere", used only where no company is in scope -
     * navigation, guides, and the legacy global assignments the migration keeps
     * for a user who has no membership row.
     */
    public function hasRoleInAnyCompany(User $user, string $roleName): bool
    {
        return $this->roleQuery($user)
            ->whereRaw('LOWER(roles.name) = ?', [mb_strtolower(trim($roleName))])
            ->exists();
    }

    /**
     * The companies a bare `hasPermissionTo()` should consider: the session's
     * active companies when there are any, otherwise every membership (console
     * commands and queued jobs have no session, and there *only* the console and
     * test bootstrappers run without one).
     *
     * @return array<int, int>
     */
    public function resolvableCompanyIds(User $user): array
    {
        $membershipIds = DB::table('user_allowed_companies')
            ->where('user_id', $user->getKey())
            ->pluck('company_id')
            ->map(fn ($id): int => (int) $id)
            ->unique()
            ->values()
            ->all();

        if ($membershipIds === []) {
            return [];
        }

        $activeIds = array_values(array_intersect(
            app(CompanyContext::class)->activeIds(),
            $membershipIds,
        ));

        return $activeIds === [] ? $membershipIds : $activeIds;
    }

    protected function abilityQuery(User $user, string $ability): Builder
    {
        return DB::table('company_membership_roles')
            ->join('role_has_permissions', 'role_has_permissions.role_id', '=', 'company_membership_roles.role_id')
            ->join('permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
            ->where('company_membership_roles.user_id', $user->getKey())
            ->where('permissions.name', $ability);
    }

    protected function roleQuery(User $user, ?int $companyId = null): Builder
    {
        $query = DB::table('company_membership_roles')
            ->join('roles', 'roles.id', '=', 'company_membership_roles.role_id')
            ->where('company_membership_roles.user_id', $user->getKey());

        if ($companyId !== null) {
            $query->where('company_membership_roles.company_id', $companyId);
        }

        return $query;
    }
}
