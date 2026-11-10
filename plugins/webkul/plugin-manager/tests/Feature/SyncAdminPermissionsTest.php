<?php

use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;
use Webkul\Security\Models\Role;

require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensureERPInstalled();
});

function adminRoleId(): int
{
    return (int) DB::table('roles')
        ->whereRaw('LOWER(name) = ?', [mb_strtolower(Utils::getPanelUserRoleName())])
        ->value('id');
}

/**
 * A page added after install had no permission row in production, so the
 * Admin role could not hold it and the page answered 403 even for the Super
 * Admin (Sales, Purchases and Invoices dashboards, found 2026-11-10).
 */
it('recreates a missing page permission and gives it to the Admin role', function () {
    // Any page permission this test install generated, so Shield will
    // generate it again; which plugins are installed varies by suite.
    $name = DB::table('permissions')->where('name', 'like', 'page\_%')->orderBy('name')->value('name');
    expect($name)->not->toBeNull();

    DB::table('permissions')->where('name', $name)->delete();
    app(PermissionRegistrar::class)->forgetCachedPermissions();

    $this->artisan('erp:sync-permissions')->assertSuccessful();

    $permissionId = DB::table('permissions')->where('name', $name)->value('id');

    expect($permissionId)->not->toBeNull()
        ->and(DB::table('role_has_permissions')->where('role_id', adminRoleId())->where('permission_id', $permissionId)->exists())->toBeTrue();
});

it('only adds permissions and leaves every other role unchanged', function () {
    $other = Role::query()->create(['name' => 'Sync Test Role', 'guard_name' => 'web']);
    $kept = DB::table('permissions')->value('id');
    DB::table('role_has_permissions')->insert(['permission_id' => $kept, 'role_id' => $other->getKey()]);

    $adminBefore = DB::table('role_has_permissions')->where('role_id', adminRoleId())->pluck('permission_id')->all();

    $this->artisan('erp:sync-permissions')->assertSuccessful();

    $adminAfter = DB::table('role_has_permissions')->where('role_id', adminRoleId())->pluck('permission_id')->all();

    expect(array_diff($adminBefore, $adminAfter))->toBe([])
        ->and(DB::table('role_has_permissions')->where('role_id', $other->getKey())->pluck('permission_id')->all())->toBe([$kept]);
});

/**
 * Nothing created `bypass_company_scope` outside the test helper, so in
 * production the Super Admin never saw the "All companies" mode or companies
 * they were not a member of (found 2026-11-10).
 */
it('gives the Admin role the company-scope bypass', function () {
    $permissionId = DB::table('permissions')->where('name', 'bypass_company_scope')->value('id');

    expect($permissionId)->not->toBeNull()
        ->and(DB::table('role_has_permissions')->where('role_id', adminRoleId())->where('permission_id', $permissionId)->exists())->toBeTrue();
});
