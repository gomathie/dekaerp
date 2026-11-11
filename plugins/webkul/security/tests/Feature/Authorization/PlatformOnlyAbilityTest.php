<?php

use Illuminate\Support\Facades\Auth;
use Spatie\Permission\PermissionRegistrar;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\Permission;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\User;
use Webkul\Security\Services\TenantRoleAssigner;
use Webkul\Support\Services\CompanyContext;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';

beforeEach(function (): void {
    Auth::forgetGuards();
    session()->forget(CompanyContext::SESSION_KEY);
    app()->forgetInstance(CompanyContext::class);
    app(PermissionRegistrar::class)->forgetCachedPermissions();
});

afterEach(function (): void {
    Auth::forgetGuards();
    session()->forget(CompanyContext::SESSION_KEY);
    app()->forgetInstance(CompanyContext::class);
    app(PermissionRegistrar::class)->forgetCachedPermissions();
});

/**
 * Once the `bypass_company_scope` row existed (2026-11-10), the tenant-role
 * lookup for it read the active companies, which asked for the same ability
 * first: every request from an ordinary user recursed until PHP crashed.
 * A tenant role granting it would also let a client see across companies.
 */
it('never grants a platform-only ability through a tenant role and resolves company context without recursion', function () {
    $company = CompanyHelper::company(['name' => 'Platform Only A', 'is_active' => true]);

    $bypass = Permission::query()->firstOrCreate(['name' => 'bypass_company_scope', 'guard_name' => 'web']);

    $role = Role::query()->create([
        'name'       => 'Tenant Role With Bypass '.fake()->unique()->numerify('####'),
        'guard_name' => 'web',
        'is_default' => false,
    ]);
    $role->syncPermissions([$bypass]);

    $user = User::withoutEvents(fn (): User => User::factory()->create([
        'default_company_id'  => $company->getKey(),
        'resource_permission' => PermissionType::GLOBAL,
        'is_active'           => true,
        'is_default'          => false,
    ]));

    $user->allowedCompanies()->sync([$company->getKey()]);
    app(TenantRoleAssigner::class)->sync($user, [$role->getKey()], [$company->getKey()]);
    $user->unsetRelation('roles')->unsetRelation('permissions');

    Auth::shouldUse('web');
    Auth::guard('web')->login($user);
    CompanyHelper::setActive([$company->getKey()]);
    app(PermissionRegistrar::class)->forgetCachedPermissions();

    expect($user->hasPermissionTo('bypass_company_scope'))->toBeFalse()
        ->and($user->hasPermissionInCompany('bypass_company_scope', $company->getKey()))->toBeFalse()
        ->and(app(CompanyContext::class)->seesAllCompanies())->toBeFalse()
        ->and(app(CompanyContext::class)->activeIds())->toBe([$company->getKey()]);
});
