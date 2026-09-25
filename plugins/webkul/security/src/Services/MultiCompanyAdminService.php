<?php

namespace Webkul\Security\Services;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Webkul\Security\Models\Permission;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Scopes\AllowedCompanyScope;
use Webkul\Support\Services\CompanyContext;

class MultiCompanyAdminService
{
    protected const DENIED_ABILITIES = [
        'bypass_company_scope',
        'bypass_ownership_scope',
        'create_role',
        'delete_any_role',
        'delete_role',
        'page_security_manage_activity',
        'page_security_manage_api_tokens',
        'page_security_manage_currency',
        'page_security_manage_users',
        'page_support_manage_branding',
        'update_role',
        'view_any_role',
        'view_role',
    ];

    /**
     * A Multi-Company Admin holds every ability except these (see the Gate::before
     * in SecurityServiceProvider), so this list is the security boundary and is
     * written to fail closed: it matches on pattern, not only on the names known
     * today, so a permission added by a future plugin is denied by default if it
     * touches roles, permissions, scope bypasses or the plugin manager.
     */
    public function deniesAbility(string $ability): bool
    {
        $ability = mb_strtolower(trim($ability));

        return in_array($ability, self::DENIED_ABILITIES, true)
            || str_starts_with($ability, 'page_security_')
            || str_starts_with($ability, 'force_delete_')
            // Granting roles or permissions is how a staff admin would escalate.
            || preg_match('/(^|_)(roles?|permissions?)$/', $ability) === 1
            // Any current or future scope bypass.
            || str_starts_with($ability, 'bypass_')
            || str_contains($ability, 'impersonate')
            || str_contains($ability, 'plugin_manager')
            || str_contains($ability, '_security_team');
    }

    public function scopeManageableUsers(Builder $query, User $actor): Builder
    {
        if ($actor->isSuperAdmin()) {
            return $query;
        }

        $assignedCompanyIds = $this->assignedCompanyIds($actor);
        $activeCompanyIds = array_values(array_intersect(
            app(CompanyContext::class)->activeIds(),
            $assignedCompanyIds,
        ));

        $query->whereDoesntHave('roles', function (Builder $roles): void {
            $roles->whereIn(DB::raw('LOWER(roles.name)'), Role::getSystemRoleNames());
        });

        // Users are not company-scoped rows - one user belongs to many companies
        // through user_allowed_companies - so CompanyScope does not reach them
        // and the boundary has to be written here. Until 2026-09-24 it was
        // written for Multi-Company Admins only, and everyone else got
        // ownership() alone: a customer's administrator whose resource
        // permission is "global" has no ownership restriction at all, so they
        // listed, opened and edited every user in the installation, including
        // other tenants'. See docs/company-admin-role-plan.md.
        if (! $actor->isMultiCompanyAdmin()) {
            // ownership() still applies on top, so "individual" and "group"
            // users see no more of their own company than they did before: this
            // only ever narrows what was visible.
            return $query
                ->ownership()
                ->where(function (Builder $scoped) use ($actor, $assignedCompanyIds, $activeCompanyIds): void {
                    // Their own row, so someone whose companies were removed does
                    // not vanish from their own Users page. It reveals no other
                    // tenant. Only reached where ownership() already allows it:
                    // for an "individual" permission, ownership matches on
                    // creator_id/user_id, not on the row's own id, so such a
                    // user sees what they created and nothing more - unchanged
                    // by this filter.
                    $scoped->whereKey($actor->getKey());

                    if ($assignedCompanyIds === [] || $activeCompanyIds === []) {
                        return;
                    }

                    $scoped->orWhere(function (Builder $sameCompany) use ($assignedCompanyIds, $activeCompanyIds): void {
                        $this->whereCompaniesContainedBy($sameCompany, $assignedCompanyIds, $activeCompanyIds);
                    });
                });
        }

        if ($assignedCompanyIds === [] || $activeCompanyIds === []) {
            return $query->whereRaw('1 = 0');
        }

        return $this->whereCompaniesContainedBy(
            $query->whereKeyNot($actor->getKey()),
            $assignedCompanyIds,
            $activeCompanyIds,
        );
    }

    /**
     * Users who share an active company with the actor and hold no company the
     * actor does not.
     *
     * The second half is what keeps the boundary honest: a user who also belongs
     * to a company the actor cannot see would, if listed, name that company on
     * the row.
     *
     * @param  array<int, int>  $assignedCompanyIds
     * @param  array<int, int>  $activeCompanyIds
     */
    protected function whereCompaniesContainedBy(Builder $query, array $assignedCompanyIds, array $activeCompanyIds): Builder
    {
        return $query
            ->whereHas('allowedCompanies', function (Builder $companies) use ($activeCompanyIds): void {
                $companies
                    ->withoutGlobalScope(AllowedCompanyScope::class)
                    ->whereIn('companies.id', $activeCompanyIds);
            })
            ->whereDoesntHave('allowedCompanies', function (Builder $companies) use ($assignedCompanyIds): void {
                $companies
                    ->withoutGlobalScope(AllowedCompanyScope::class)
                    ->whereNotIn('companies.id', $assignedCompanyIds);
            });
    }

    /**
     * Whether the actor may reach this user at all, by company.
     *
     * Deliberately the same query the list is built from, so the policy and the
     * Users page cannot drift apart - a record that is invisible in the list
     * must not be openable by its URL.
     */
    public function sharesAssignedCompanies(User $actor, User $target): bool
    {
        if ($actor->isSuperAdmin() || $actor->is($target)) {
            return true;
        }

        $assignedCompanyIds = $this->assignedCompanyIds($actor);
        $activeCompanyIds = array_values(array_intersect(
            app(CompanyContext::class)->activeIds(),
            $assignedCompanyIds,
        ));

        if ($assignedCompanyIds === [] || $activeCompanyIds === []) {
            return false;
        }

        return $this->whereCompaniesContainedBy(
            User::query()->whereKey($target->getKey()),
            $assignedCompanyIds,
            $activeCompanyIds,
        )->exists();
    }

    public function canManageUser(User $actor, User $target): bool
    {
        if ($actor->isSuperAdmin()) {
            return true;
        }

        if (! $actor->isMultiCompanyAdmin()) {
            return false;
        }

        return $this->scopeManageableUsers(
            User::query()->whereKey($target->getKey()),
            $actor,
        )->exists();
    }

    public function scopeAssignableCompanies(Builder $query, User $actor): Builder
    {
        $query->withoutGlobalScope(AllowedCompanyScope::class);

        if ($actor->isSuperAdmin()) {
            return $query;
        }

        $companyIds = $this->assignedCompanyIds($actor);

        return $companyIds === []
            ? $query->whereRaw('1 = 0')
            : $query->whereIn('companies.id', $companyIds);
    }

    public function scopeAssignableRoles(Builder $query, User $actor): Builder
    {
        if ($actor->isSuperAdmin()) {
            return $query;
        }

        $query->whereNotIn(DB::raw('LOWER(roles.name)'), Role::getSystemRoleNames());

        // A Multi-Company Admin holds everything the denylist allows, so the
        // subset rule below would be read from the handful of permissions the
        // role carries and leave them able to assign almost nothing. What they
        // may hand out is instead "any non-system role that grants nothing they
        // are themselves denied".
        if ($actor->isMultiCompanyAdmin()) {
            $deniedNames = Permission::query()
                ->pluck('name')
                ->filter(fn (string $permission): bool => $this->deniesAbility($permission))
                ->values()
                ->all();

            return $deniedNames === []
                ? $query
                : $query->whereDoesntHave(
                    'permissions',
                    fn (Builder $permissions) => $permissions->whereIn('permissions.name', $deniedNames),
                );
        }

        $permissionNames = $actor->getAllPermissions()
            ->pluck('name')
            ->filter(fn (string $permission): bool => ! $this->deniesAbility($permission))
            ->values()
            ->all();

        if ($permissionNames === []) {
            return $query->whereDoesntHave('permissions');
        }

        return $query->whereDoesntHave(
            'permissions',
            fn (Builder $permissions) => $permissions->whereNotIn('permissions.name', $permissionNames),
        );
    }

    public function assertUserAssignment(
        User $actor,
        ?User $target,
        array $roleIds,
        array $companyIds,
        ?int $defaultCompanyId,
    ): void {
        if (! $actor->is_active) {
            throw new AuthorizationException(__('Inactive users cannot administer other users.'));
        }

        $roleIds = $this->normalizeIds($roleIds);
        $companyIds = $this->normalizeIds($companyIds);

        if (
            $target
            && ! $actor->isSuperAdmin()
            && ($target->isSuperAdmin() || $target->isMultiCompanyAdmin())
        ) {
            throw ValidationException::withMessages([
                'roles' => __('security::filament/resources/user.form.validation.target-administration-forbidden'),
            ]);
        }

        if ($companyIds === []) {
            throw ValidationException::withMessages([
                'allowed_companies' => __('security::filament/resources/user.form.validation.company-required'),
            ]);
        }

        if (! $defaultCompanyId || ! in_array($defaultCompanyId, $companyIds, true)) {
            throw ValidationException::withMessages([
                'default_company_id' => __('security::filament/resources/user.form.sections.multi-company.default-company-not-allowed'),
            ]);
        }

        $existingCompanyIds = Company::query()
            ->withoutGlobalScope(AllowedCompanyScope::class)
            ->whereIn('id', $companyIds)
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->all();

        if (count($existingCompanyIds) !== count($companyIds)) {
            throw ValidationException::withMessages([
                'allowed_companies' => __('security::filament/resources/user.form.validation.company-invalid'),
            ]);
        }

        if (! $actor->isSuperAdmin()) {
            $unauthorizedCompanyIds = array_diff($companyIds, $this->assignedCompanyIds($actor));

            if ($unauthorizedCompanyIds !== []) {
                throw ValidationException::withMessages([
                    'allowed_companies' => __('security::filament/resources/user.form.validation.company-not-assigned'),
                ]);
            }
        }

        $existingRoleIds = Role::query()
            ->whereIn('id', $roleIds)
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->all();

        if ($roleIds === [] || count($existingRoleIds) !== count($roleIds)) {
            throw ValidationException::withMessages([
                'roles' => __('security::filament/resources/user.form.validation.role-invalid'),
            ]);
        }

        if (! $actor->isSuperAdmin()) {
            $assignableRoleIds = $this->scopeAssignableRoles(Role::query(), $actor)
                ->whereIn('id', $roleIds)
                ->pluck('id')
                ->map(fn ($id): int => (int) $id)
                ->all();

            if (count($assignableRoleIds) !== count($roleIds)) {
                throw ValidationException::withMessages([
                    'roles' => __('security::filament/resources/user.form.validation.role-not-assignable'),
                ]);
            }
        }

        if ($target && $actor->isMultiCompanyAdmin() && $actor->is($target)) {
            $currentRoleIds = $target->roles()->pluck('roles.id')->map(fn ($id): int => (int) $id)->sort()->values()->all();
            $currentCompanyIds = $this->assignedCompanyIds($target);

            sort($roleIds);
            sort($companyIds);
            sort($currentCompanyIds);

            if ($roleIds !== $currentRoleIds || $companyIds !== $currentCompanyIds) {
                throw ValidationException::withMessages([
                    'roles' => __('security::filament/resources/user.form.validation.self-administration-forbidden'),
                ]);
            }
        }
    }

    public function assignedCompanyIds(User $user): array
    {
        return DB::table('user_allowed_companies')
            ->where('user_id', $user->getKey())
            ->pluck('company_id')
            ->map(fn ($id): int => (int) $id)
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    public function isCompanyAssigned(User $user, int $companyId): bool
    {
        return in_array($companyId, $this->assignedCompanyIds($user), true);
    }

    /**
     * Whether these roles include Company Admin.
     *
     * Used to give such a user `global` resource permission on creation.
     * Ownership for the User model matches on creator_id and id, so an
     * `individual` permission would show a company administrator only themselves
     * and the users they personally created - not the colleagues who were
     * already there, which is the whole job. `global` is safe here because
     * scopeManageableUsers() and UserPolicy bound it to their own companies.
     *
     * @param  array<int, mixed>  $roleIds
     */
    public function includesCompanyAdminRole(array $roleIds): bool
    {
        $roleIds = $this->normalizeIds($roleIds);

        return $roleIds !== []
            && Role::query()
                ->whereIn('id', $roleIds)
                ->whereRaw('LOWER(name) = ?', [mb_strtolower(Role::COMPANY_ADMIN)])
                ->exists();
    }

    public function includesMultiCompanyAdminRole(array $roleIds): bool
    {
        $roleIds = $this->normalizeIds($roleIds);

        return $roleIds !== []
            && Role::query()
                ->whereIn('id', $roleIds)
                ->whereRaw('LOWER(name) = ?', [mb_strtolower(Role::MULTI_COMPANY_ADMIN)])
                ->exists();
    }

    public function userSnapshot(User $user): array
    {
        return [
            'role_ids'           => $user->roles()->pluck('roles.id')->map(fn ($id): int => (int) $id)->sort()->values()->all(),
            'company_ids'        => $this->assignedCompanyIds($user),
            'default_company_id' => $user->default_company_id,
            'is_active'          => (bool) $user->is_active,
        ];
    }

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
