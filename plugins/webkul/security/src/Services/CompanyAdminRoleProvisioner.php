<?php

namespace Webkul\Security\Services;

use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;
use Webkul\Security\Models\Permission;
use Webkul\Security\Models\Role;
use Webkul\Security\PermissionRegistrar as ForkPermissionRegistrar;

/**
 * The "Company Admin" role: a customer's own administrator, for their company.
 *
 * Asked for so that onboarding a tenant does not mean hand-ticking permissions
 * every time (user, 2026-09-24). It is an ordinary role, not a second
 * super admin: it holds exactly the permissions below and gets no Gate::before
 * bypass. What keeps it inside one company is not this role at all, it is
 * MultiCompanyAdminService::scopeManageableUsers() and UserPolicy, which company
 * -scope every actor since 2026-09-24.
 *
 * ## What it deliberately does not include
 *
 * No business permissions - not shipments, invoices, products or anything a
 * plugin adds later. Two reasons: a role that listed every resource would grow
 * silently into a near-super-admin every time a plugin is installed, and what a
 * given customer's administrator should be able to *do* differs per customer.
 * Assign business roles alongside this one.
 *
 * No role or permission management, no plugin manager, no page_security_*, no
 * scope bypasses. Handing out roles is how an administrator escalates, and this
 * role is given to people outside DEKA ERP.
 *
 * @see docs/completed/company-admin-role-plan.md
 */
class CompanyAdminRoleProvisioner
{
    /**
     * Administration of their own company's people, and nothing else.
     */
    public const BASE_PERMISSIONS = [
        'view_any_security_user',
        'view_security_user',
        'create_security_user',
        'update_security_user',
        // Archiving a leaver, one at a time. No delete_any (bulk) and no
        // force_delete: a customer administrator should not be able to erase
        // user records outright.
        'delete_security_user',
        // So the company switcher and their own company page work. Which
        // companies they see is decided by user_allowed_companies, not here.
        'view_any_security_company',
        'view_security_company',
        // Their own company's document numbering (UF-029, user, 2026-11-25:
        // "Company Admin and up"). SequencePolicy still requires membership of
        // the sequence's company, and keeps shared sequences and bulk delete
        // with installation staff, so no delete_any here.
        'view_any_support_sequence',
        'view_support_sequence',
        'create_support_sequence',
        'update_support_sequence',
        'delete_support_sequence',
    ];

    public function provision(): Role
    {
        return DB::transaction(function (): Role {
            $role = Role::query()
                ->where('guard_name', 'web')
                ->whereRaw('LOWER(name) = ?', [mb_strtolower(Role::COMPANY_ADMIN)])
                ->first();

            $role ??= Role::query()->create([
                'name'       => Role::COMPANY_ADMIN,
                'guard_name' => 'web',
                'is_default' => false,
            ]);

            $permissions = collect(self::BASE_PERMISSIONS)
                ->map(fn (string $name): Permission => Permission::query()->firstOrCreate([
                    'name'       => $name,
                    'guard_name' => 'web',
                ]));

            // Synced, not merely added: the baseline must survive a permission
            // regeneration, and must not quietly accumulate abilities that a
            // later Shield run happens to attach.
            $role->permissions()->sync($permissions->map->getKey()->all());

            app(PermissionRegistrar::class)->forgetCachedPermissions();

            // There are two registrars in this fork and they hold separate
            // in-memory caches. Without this second flush the permissions just
            // created stay invisible to findByName() for the rest of the
            // request and every check against them returns false.
            app(ForkPermissionRegistrar::class)->forgetCachedPermissions();

            return $role->refresh();
        });
    }

    /**
     * Give an existing Company Admin role any baseline permission it lacks.
     *
     * Why (UF-029, 2026-11-25): provision() runs only from its 2026-09-24
     * migration and the seeder, so a permission added to BASE_PERMISSIONS
     * later never reached production's role. erp:sync-permissions calls this
     * on every deploy. It only adds, unlike provision(), so nothing a Super
     * Admin changed on the Roles screen is removed, and it creates no role.
     *
     * @return int permissions added
     */
    public function grantMissingBasePermissions(): int
    {
        $roleId = Role::query()
            ->where('guard_name', 'web')
            ->whereRaw('LOWER(name) = ?', [mb_strtolower(Role::COMPANY_ADMIN)])
            ->value('id');

        if ($roleId === null) {
            return 0;
        }

        $ids = Permission::query()
            ->where('guard_name', 'web')
            ->whereIn('name', self::BASE_PERMISSIONS)
            ->pluck('id');

        $held = DB::table('role_has_permissions')->where('role_id', $roleId)->pluck('permission_id')->map(fn ($id): int => (int) $id);
        $missing = $ids->map(fn ($id): int => (int) $id)->diff($held)->values();

        if ($missing->isEmpty()) {
            return 0;
        }

        DB::table('role_has_permissions')->insertOrIgnore(
            $missing->map(fn (int $id): array => ['permission_id' => $id, 'role_id' => $roleId])->all()
        );

        app(PermissionRegistrar::class)->forgetCachedPermissions();
        app(ForkPermissionRegistrar::class)->forgetCachedPermissions();

        return $missing->count();
    }
}
