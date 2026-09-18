<?php

namespace Webkul\Security\Services;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
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

    public function deniesAbility(string $ability): bool
    {
        $ability = mb_strtolower(trim($ability));

        return in_array($ability, self::DENIED_ABILITIES, true)
            || str_starts_with($ability, 'page_security_')
            || str_starts_with($ability, 'force_delete_')
            || str_contains($ability, '_plugin_manager_plugin')
            || str_contains($ability, '_security_team');
    }

    public function scopeManageableUsers(Builder $query, User $actor): Builder
    {
        if ($actor->isSuperAdmin()) {
            return $query;
        }

        if (! $actor->isMultiCompanyAdmin()) {
            return $query
                ->ownership()
                ->whereDoesntHave('roles', function (Builder $roles): void {
                    $roles->whereIn(DB::raw('LOWER(roles.name)'), Role::getSystemRoleNames());
                });
        }

        $assignedCompanyIds = $this->assignedCompanyIds($actor);
        $activeCompanyIds = array_values(array_intersect(
            app(CompanyContext::class)->activeIds(),
            $assignedCompanyIds,
        ));

        if ($assignedCompanyIds === [] || $activeCompanyIds === []) {
            return $query->whereRaw('1 = 0');
        }

        return $query
            ->whereKeyNot($actor->getKey())
            ->whereDoesntHave('roles', function (Builder $roles): void {
                $roles->whereIn(DB::raw('LOWER(roles.name)'), Role::getSystemRoleNames());
            })
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
