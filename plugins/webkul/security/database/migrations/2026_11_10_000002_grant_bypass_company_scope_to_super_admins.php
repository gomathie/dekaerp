<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;
use Webkul\Security\Models\Role;
use Webkul\Security\PermissionRegistrar as SecurityPermissionRegistrar;

return new class extends Migration
{
    /**
     * Nothing created `bypass_company_scope` outside the test helper
     * (found 2026-11-10). Shield's gate-based super admin is disabled in this
     * fork, so the Super Admin role works only through permission rows it
     * holds. Without this one, CompanyContext treated a Super Admin as an
     * ordinary member: companies they were not a member of were missing from
     * the switcher, and the "All companies" mode never appeared.
     */
    public function up(): void
    {
        $permissionId = DB::table('permissions')
            ->where('name', 'bypass_company_scope')
            ->where('guard_name', 'web')
            ->value('id');

        if ($permissionId === null) {
            $permissionId = DB::table('permissions')->insertGetId([
                'name'       => 'bypass_company_scope',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $roleIds = DB::table('roles')
            ->where('guard_name', 'web')
            ->whereIn(DB::raw('LOWER(TRIM(name))'), Role::getSuperAdminRoleNames())
            ->pluck('id');

        foreach ($roleIds as $roleId) {
            DB::table('role_has_permissions')->insertOrIgnore([
                'permission_id' => $permissionId,
                'role_id'       => $roleId,
            ]);
        }

        $this->flushPermissionCaches();
    }

    /**
     * Removes only the Super Admin role grants. The row stays, because other
     * code and tests may have created or granted it independently.
     */
    public function down(): void
    {
        $permissionId = DB::table('permissions')
            ->where('name', 'bypass_company_scope')
            ->where('guard_name', 'web')
            ->value('id');

        if ($permissionId !== null) {
            $roleIds = DB::table('roles')
                ->where('guard_name', 'web')
                ->whereIn(DB::raw('LOWER(TRIM(name))'), Role::getSuperAdminRoleNames())
                ->pluck('id');

            DB::table('role_has_permissions')
                ->where('permission_id', $permissionId)
                ->whereIn('role_id', $roleIds)
                ->delete();
        }

        $this->flushPermissionCaches();
    }

    /**
     * Both registrars hold separate caches (AGENTS.md), so flush both.
     */
    protected function flushPermissionCaches(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        if (class_exists(SecurityPermissionRegistrar::class) && app()->bound(SecurityPermissionRegistrar::class)) {
            app(SecurityPermissionRegistrar::class)->forgetCachedPermissions();
        }
    }
};
