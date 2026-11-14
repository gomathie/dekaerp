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
 *    Roles screen survives every deploy.
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
                $role->permissions()->sync($this->permissionIdsFor($plugins)->all());
                $created[] = $name;
            }
        });

        if ($created !== []) {
            app(PermissionRegistrar::class)->forgetCachedPermissions();

            if (app()->bound(ForkPermissionRegistrar::class)) {
                app(ForkPermissionRegistrar::class)->forgetCachedPermissions();
            }
        }

        return $created;
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

    /** @param  array<string, string>  $plugins */
    public function grants(array $plugins, string $permission): bool
    {
        foreach (self::FORBIDDEN_FRAGMENTS as $fragment) {
            if (str_contains($permission, $fragment)) {
                return false;
            }
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
