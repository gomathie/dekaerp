<?php

namespace Webkul\Security\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;
use Webkul\Security\Models\Permission;
use Webkul\Security\Models\Role;
use Webkul\Security\PermissionRegistrar as ForkPermissionRegistrar;

/**
 * Ready-made business roles (comprehensive review CR-5, 2026-11-13), so a
 * company's users can be given a sensible role instead of hand-ticked
 * permissions.
 *
 * Decision taken while the user was away (option A in the plan): these are
 * installation-wide roles, not per-company copies. Roles have no company
 * column and Spatie keeps (name, guard) unique, so per-company copies need a
 * schema change to a security boundary; that stays possible later (option B).
 *
 * Rules that keep this safe:
 *  - A template is created only when missing, with its permissions at that
 *    moment. Later runs never touch an existing role, so an edit made on the
 *    Roles screen survives every deploy. (Two narrow exceptions:
 *    stripAdminDenied() only removes what a Company Admin may never hold,
 *    and a new EXTRA_PERMISSIONS entry is added once by its own migration.)
 *  - Templates hold business permissions only. Security, roles, plugins,
 *    scope bypasses and impersonation never match a template's plugins, and
 *    are filtered out again by name as a backstop.
 *  - Who may assign one is unchanged: a Company Admin can hand out only a
 *    role whose permissions they hold themselves
 *    (MultiCompanyAdminService::scopeAssignableRoles), and module switches
 *    still decide what a company can open.
 */
class RoleTemplateProvisioner
{
    public const FULL = 'full';

    /** Everything except deleting, restoring and force-deleting. */
    public const STANDARD = 'standard';

    public const VIEW = 'view';

    /**
     * Role name => [permission plugin key => access]. The plugin key is the
     * one Shield builds names from (`<affix>_<plugin>_<model>`, `page_<plugin>_...`).
     *
     * @var array<string, array<string, string>>
     */
    public const TEMPLATES = [
        'Finance Manager' => ['account' => self::FULL, 'accounting' => self::FULL, 'invoice' => self::FULL],
        'Accountant'      => ['account' => self::STANDARD, 'accounting' => self::STANDARD, 'invoice' => self::STANDARD],
        'HR Manager'      => [
            'employee'   => self::FULL, 'time_off' => self::FULL, 'recruitment' => self::FULL,
            'attendance' => self::FULL, 'timesheet' => self::FULL,
        ],
        // Payroll is built on another branch; its permissions join this role
        // the first time it is created after that plugin is installed.
        'Payroll Officer' => [
            'payroll'    => self::STANDARD, 'employee' => self::VIEW, 'time_off' => self::VIEW,
            'attendance' => self::VIEW, 'timesheet' => self::VIEW,
        ],
        'Sales Manager' => [
            'sale'    => self::FULL, 'contact' => self::STANDARD, 'partner' => self::STANDARD,
            'product' => self::VIEW, 'invoice' => self::VIEW,
        ],
        'Inventory Manager' => ['inventory' => self::FULL, 'product' => self::STANDARD, 'purchase' => self::VIEW],
        'Logistics Manager' => ['logistics' => self::FULL, 'contact' => self::VIEW, 'partner' => self::VIEW],
        'Standard Employee' => ['project' => self::VIEW, 'timesheet' => self::STANDARD, 'time_off' => self::VIEW],
    ];

    /**
     * Single permissions a template holds beyond its plugin access levels.
     *
     * Sales Manager records payments (user, 2026-11-26): sales staff take
     * money at the counter, and Record payment needs the Invoices module's
     * payment permission, which "invoice => view" does not give.
     *
     * Given when a template is created, and covered by the administrators'
     * business role on every sync. An existing role gets a new extra once,
     * from a migration (addExtraPermissions()), never on every sync: a later
     * removal on the Roles screen must survive deploys like any other edit.
     *
     * @var array<string, list<string>>
     */
    public const EXTRA_PERMISSIONS = [
        'Sales Manager' => ['create_invoice_payment'],
    ];

    /** Never granted by a template, whatever a plugin key happens to match. */
    protected const FORBIDDEN_FRAGMENTS = ['_security_', '_role', '_plugin_', 'bypass_', 'impersonate'];

    protected const DESTRUCTIVE_AFFIXES = ['delete_', 'delete_any_', 'force_delete_', 'force_delete_any_', 'restore_', 'restore_any_'];

    protected const VIEW_AFFIXES = ['view_', 'view_any_', 'page_', 'widget_'];

    /**
     * Create every template that does not exist yet.
     *
     * @return list<string> names of the roles created by this call
     */
    public function provision(): array
    {
        $created = [];

        DB::transaction(function () use (&$created): void {
            foreach (self::TEMPLATES as $name => $plugins) {
                $exists = Role::query()
                    ->where('guard_name', 'web')
                    ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
                    ->exists();

                if ($exists) {
                    continue;
                }

                $role = Role::query()->create(['name' => $name, 'guard_name' => 'web']);
                $role->permissions()->sync($this->templatePermissionIds($name, $plugins)->all());
                $created[] = $name;
            }
        });

        $stripped = $this->stripAdminDenied();

        if ($created !== [] || $stripped > 0) {
            app(PermissionRegistrar::class)->forgetCachedPermissions();

            if (app()->bound(ForkPermissionRegistrar::class)) {
                app(ForkPermissionRegistrar::class)->forgetCachedPermissions();
            }
        }

        return $created;
    }

    /**
     * Remove from the templates every permission a Company Admin may never
     * hold or hand out (force delete, roles, security pages...).
     *
     * Why (CR-3 production check, 2026-11-14): "full" templates carried
     * force_delete_*, which MultiCompanyAdminService::deniesAbility() forbids
     * to Company Admins, so Finance, HR, Sales, Inventory and Logistics
     * Manager never appeared in their role list. This only ever removes
     * those abilities; any other edit made on the Roles screen is kept.
     *
     * @return int permissions removed
     */
    public function stripAdminDenied(): int
    {
        $templateIds = Role::query()
            ->where('guard_name', 'web')
            ->whereIn(DB::raw('LOWER(name)'), array_map('mb_strtolower', array_keys(self::TEMPLATES)))
            ->pluck('id');

        if ($templateIds->isEmpty()) {
            return 0;
        }

        $denied = Permission::query()
            ->get(['id', 'name'])
            ->filter(fn (Permission $permission): bool => app(MultiCompanyAdminService::class)->deniesAbility((string) $permission->name))
            ->pluck('id');

        if ($denied->isEmpty()) {
            return 0;
        }

        return DB::table('role_has_permissions')
            ->whereIn('role_id', $templateIds)
            ->whereIn('permission_id', $denied)
            ->delete();
    }

    /**
     * Give the company administrators' business role every permission a
     * template holds (user, 2026-11-14, after the CR-3 production run).
     *
     * Why: a Company Admin may assign only roles within their own
     * permissions (MultiCompanyAdminService::scopeAssignableRoles). The
     * business role every new administrator receives was narrower than every
     * template, so no administrator could hand out any of them, and their own
     * menu showed 5 of 19 granted modules. Only adds, never removes; the same
     * template filter keeps security, roles, plugins, bypasses and
     * impersonation out. Module switches still decide what a company opens.
     *
     * @return int number of permissions added
     */
    public function coverBusinessRole(?int $roleId): int
    {
        if (! $roleId || ! Role::query()->whereKey($roleId)->exists()) {
            return 0;
        }

        $ids = collect(self::TEMPLATES)
            ->flatMap(fn (array $plugins, string $name): Collection => $this->templatePermissionIds($name, $plugins))
            ->unique();

        $held = DB::table('role_has_permissions')->where('role_id', $roleId)->pluck('permission_id')->map(fn ($id): int => (int) $id);
        $missing = $ids->diff($held)->values();

        if ($missing->isEmpty()) {
            return 0;
        }

        DB::table('role_has_permissions')->insertOrIgnore(
            $missing->map(fn (int $id): array => ['permission_id' => $id, 'role_id' => $roleId])->all()
        );

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        if (app()->bound(ForkPermissionRegistrar::class)) {
            app(ForkPermissionRegistrar::class)->forgetCachedPermissions();
        }

        return $missing->count();
    }

    /**
     * @param  array<string, string>  $plugins
     * @return Collection<int, int>
     */
    public function permissionIdsFor(array $plugins): Collection
    {
        return Permission::query()
            ->where('guard_name', 'web')
            ->get(['id', 'name'])
            ->filter(fn (Permission $permission): bool => $this->grants($plugins, (string) $permission->name))
            ->map(fn (Permission $permission): int => (int) $permission->getKey())
            ->values();
    }

    /**
     * Everything a template holds: its plugin access levels plus its extras.
     *
     * @param  array<string, string>  $plugins
     * @return Collection<int, int>
     */
    public function templatePermissionIds(string $template, array $plugins): Collection
    {
        return $this->permissionIdsFor($plugins)
            ->merge($this->extraPermissionIdsFor($template))
            ->unique()
            ->values();
    }

    /**
     * A template's extra permissions that exist in this installation. They
     * pass the same filter as everything else a template holds.
     *
     * @return Collection<int, int>
     */
    public function extraPermissionIdsFor(string $template): Collection
    {
        $names = array_values(array_filter(
            self::EXTRA_PERMISSIONS[$template] ?? [],
            fn (string $name): bool => $this->isGrantable($name),
        ));

        if ($names === []) {
            return collect();
        }

        return Permission::query()
            ->where('guard_name', 'web')
            ->whereIn('name', $names)
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->values();
    }

    /**
     * Brings the existing role of that template up to its extra permissions,
     * adding only. Called once per new extra, from a migration, so that a
     * permission later removed on the Roles screen stays removed.
     *
     * @return int permissions added
     */
    public function addExtraPermissions(string $template): int
    {
        $roleId = Role::query()
            ->where('guard_name', 'web')
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($template)])
            ->value('id');

        $ids = $roleId ? $this->extraPermissionIdsFor($template) : collect();

        if ($ids->isEmpty()) {
            return 0;
        }

        $held = DB::table('role_has_permissions')->where('role_id', $roleId)->pluck('permission_id')->map(fn ($id): int => (int) $id);
        $missing = $ids->diff($held)->values();

        if ($missing->isEmpty()) {
            return 0;
        }

        DB::table('role_has_permissions')->insertOrIgnore(
            $missing->map(fn (int $id): array => ['permission_id' => $id, 'role_id' => (int) $roleId])->all()
        );

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        if (app()->bound(ForkPermissionRegistrar::class)) {
            app(ForkPermissionRegistrar::class)->forgetCachedPermissions();
        }

        return $missing->count();
    }

    /** @param  array<string, string>  $plugins */
    public function grants(array $plugins, string $permission): bool
    {
        if (! $this->isGrantable($permission)) {
            return false;
        }

        foreach ($plugins as $plugin => $access) {
            // "_account_" must not match "accounting", so the key is bounded by
            // underscores (or the end, for a few page names).
            if (! preg_match('/_'.preg_quote($plugin, '/').'(_|::|$)/', $permission)) {
                continue;
            }

            return match ($access) {
                self::FULL     => true,
                self::STANDARD => ! $this->startsWithAny($permission, self::DESTRUCTIVE_AFFIXES),
                default        => $this->startsWithAny($permission, self::VIEW_AFFIXES),
            };
        }

        return false;
    }

    protected function isGrantable(string $permission): bool
    {
        // One rule, one place: a template may hold nothing a Company Admin is
        // denied, or no Company Admin can assign it.
        if (app(MultiCompanyAdminService::class)->deniesAbility($permission)) {
            return false;
        }

        foreach (self::FORBIDDEN_FRAGMENTS as $fragment) {
            if (str_contains($permission, $fragment)) {
                return false;
            }
        }

        return true;
    }

    /** @param  list<string>  $prefixes */
    protected function startsWithAny(string $permission, array $prefixes): bool
    {
        foreach ($prefixes as $prefix) {
            if (str_starts_with($permission, $prefix)) {
                return true;
            }
        }

        return false;
    }
}
