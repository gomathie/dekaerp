<?php

namespace Webkul\PluginManager\Console\Commands;

use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Webkul\Security\PermissionRegistrar as SecurityPermissionRegistrar;

/**
 * Creates every missing Shield permission and gives it to the Admin role.
 *
 * Run on every deploy (Laravel Cloud deploy commands, after `migrate`).
 * Shield permissions were generated only when a plugin was installed, and
 * Shield's gate-based super admin is disabled in this fork, so the Admin role
 * sees a page only if it holds that page's permission row. A page or widget
 * added after install therefore had no row in production and answered 403
 * even for the Super Admin: the Sales, Purchases and Invoices dashboards
 * added on 2026-11-07 (found 2026-11-10).
 *
 * Idempotent. It only adds: it never removes a permission from any role,
 * and it touches no role other than Admin.
 *
 *     php artisan erp:sync-permissions
 */
class SyncAdminPermissions extends Command
{
    protected $signature = 'erp:sync-permissions';

    protected $description = 'Create missing Shield permissions and give them to the Admin role (run on deploy)';

    public function handle(): int
    {
        $exitCode = Artisan::call('shield:generate', [
            '--all'    => true,
            '--option' => 'permissions',
            '--panel'  => 'admin',
        ], $this->getOutput());

        if ($exitCode !== self::SUCCESS) {
            $this->error('Shield permission generation failed; the Admin role was not changed.');

            return self::FAILURE;
        }

        $roleId = DB::table('roles')
            ->where('guard_name', 'web')
            ->whereRaw('LOWER(name) = ?', [mb_strtolower(Utils::getPanelUserRoleName())])
            ->value('id');

        if ($roleId === null) {
            $this->error('No Admin role was found, so no permissions were given.');

            return self::FAILURE;
        }

        $held = DB::table('role_has_permissions')->where('role_id', $roleId)->pluck('permission_id')->all();

        $missing = Permission::query()
            ->where('guard_name', 'web')
            ->whereNotIn('id', $held)
            ->pluck('id');

        DB::table('role_has_permissions')->insertOrIgnore(
            $missing->map(fn (int $id): array => ['permission_id' => $id, 'role_id' => $roleId])->all()
        );

        // Two registrars keep separate caches (AGENTS.md): flush both.
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        if (app()->bound(SecurityPermissionRegistrar::class)) {
            app(SecurityPermissionRegistrar::class)->forgetCachedPermissions();
        }

        $this->components->info(sprintf('Admin role given %d missing permission(s).', $missing->count()));

        return self::SUCCESS;
    }
}
