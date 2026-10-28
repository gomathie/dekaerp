<?php

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\Permission;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\User;
use Webkul\Security\Services\TenantRoleAssigner;
use Webkul\Security\Services\TenantRoleResolver;
use Webkul\Support\Models\Company;
use Webkul\Support\Services\CompanyContext;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';

/**
 * SAAS-01: a tenant role belongs to a company membership.
 *
 * One user, administrator in A and ordinary member in B. That is the whole point
 * of the package: before it, a single role assignment followed the user into
 * every company they belonged to, which is what
 * GlobalTenantRoleCharacterizationTest pins.
 *
 * `hasPermissionTo()` deliberately still honours the global store (pre-migration
 * rows, and users with no membership row), so the per-company distinction is
 * proven through the company-scoped checks - which is what a policy must use
 * when it knows the record's company.
 *
 * Permission names are synthetic so no provisioned role can satisfy them by
 * accident: a positive result then has exactly one possible source.
 */
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

it('grants a tenant role in the company it was assigned to and denies it in another membership', function () {
    $companyA = CompanyHelper::company(['name' => 'SaaS Scoping A', 'is_active' => true]);
    $companyB = CompanyHelper::company(['name' => 'SaaS Scoping B', 'is_active' => true]);

    $suffix = fake()->unique()->numerify('####');

    $granted = Permission::query()->firstOrCreate([
        'name'       => 'saas_scoping_granted_'.$suffix,
        'guard_name' => 'web',
    ]);

    // Negative control: a real permission row no role holds, so "allowed in A"
    // cannot be explained by a leaked bypass or a permissive default.
    $withheld = Permission::query()->firstOrCreate([
        'name'       => 'saas_scoping_withheld_'.$suffix,
        'guard_name' => 'web',
    ]);

    $tenantRole = Role::query()->create([
        'name'       => 'SaaS Scoping Supervisor '.$suffix,
        'guard_name' => 'web',
        'is_default' => false,
    ]);

    $tenantRole->syncPermissions([$granted]);

    $companyAdminRole = Role::query()
        ->whereRaw('LOWER(name) = ?', [mb_strtolower(Role::COMPANY_ADMIN)])
        ->firstOrFail();

    $user = User::withoutEvents(fn (): User => User::factory()->create([
        'default_company_id'  => $companyA->getKey(),
        'resource_permission' => PermissionType::GLOBAL,
        'is_active'           => true,
        'is_default'          => false,
    ]));

    $user->allowedCompanies()->sync([$companyA->getKey(), $companyB->getKey()]);

    app(TenantRoleAssigner::class)->sync(
        $user,
        [$tenantRole->getKey(), $companyAdminRole->getKey()],
        [$companyA->getKey()],
    );

    $user->unsetRelation('roles')->unsetRelation('permissions');

    // Preconditions: the roles sit in exactly one membership, and the global
    // store stayed empty - otherwise the denial in B could come from anywhere.
    expect(DB::table('company_membership_roles')
        ->where('user_id', $user->getKey())
        ->where('company_id', $companyA->getKey())
        ->count())->toBe(2)
        ->and(DB::table('company_membership_roles')
            ->where('user_id', $user->getKey())
            ->where('company_id', $companyB->getKey())
            ->count())->toBe(0)
        ->and($user->fresh()->getRoleNames()->all())->toBe([])
        ->and($tenantRole->fresh()->hasPermissionTo($granted->name))->toBeTrue()
        ->and($companyAdminRole->fresh()->hasPermissionTo($withheld->name))->toBeFalse();

    $activate = function (Company $company) use ($user): void {
        Auth::forgetGuards();
        Auth::shouldUse('web');
        Auth::guard('web')->login($user);
        CompanyHelper::setActive([$company->getKey()]);
        $user->unsetRelation('roles')->unsetRelation('permissions');
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    };

    $activate($companyA);

    $permittedInA = $user->hasPermissionInCompany($granted->name, (int) $companyA->getKey());
    $withheldInA = $user->hasPermissionInCompany($withheld->name, (int) $companyA->getKey());
    $isAdminInA = $user->isCompanyAdmin((int) $companyA->getKey());
    $gateInA = Auth::user()->can($granted->name);

    $activate($companyB);

    $permittedInB = $user->hasPermissionInCompany($granted->name, (int) $companyB->getKey());
    $withheldInB = $user->hasPermissionInCompany($withheld->name, (int) $companyB->getKey());
    $isAdminInB = $user->isCompanyAdmin((int) $companyB->getKey());
    $gateInB = Auth::user()->can($granted->name);

    // A company named explicitly still answers truthfully while the session sits
    // in B - that is what a policy for a record in A depends on.
    $permittedInAAfterSwitch = $user->hasPermissionInCompany($granted->name, (int) $companyA->getKey());

    expect($permittedInA)->toBeTrue()
        ->and($withheldInA)->toBeFalse()
        ->and($isAdminInA)->toBeTrue()
        ->and($gateInA)->toBeTrue()
        ->and($permittedInB)->toBeFalse()
        ->and($withheldInB)->toBeFalse()
        ->and($isAdminInB)->toBeFalse()
        ->and($gateInB)->toBeFalse()
        ->and($permittedInAAfterSwitch)->toBeTrue();
});

it('follows the session for the bare navigation check', function () {
    $companyA = CompanyHelper::company(['name' => 'SaaS Navigation A', 'is_active' => true]);
    $companyB = CompanyHelper::company(['name' => 'SaaS Navigation B', 'is_active' => true]);

    $suffix = fake()->unique()->numerify('####');

    $permission = Permission::query()->firstOrCreate([
        'name'       => 'saas_navigation_'.$suffix,
        'guard_name' => 'web',
    ]);

    $role = Role::query()->create([
        'name'       => 'SaaS Navigation Role '.$suffix,
        'guard_name' => 'web',
        'is_default' => false,
    ]);

    $role->syncPermissions([$permission]);

    $user = User::withoutEvents(fn (): User => User::factory()->create([
        'default_company_id' => $companyA->getKey(),
        'is_active'          => true,
        'is_default'         => false,
    ]));

    $user->allowedCompanies()->sync([$companyA->getKey(), $companyB->getKey()]);
    app(TenantRoleAssigner::class)->sync($user, [$role->getKey()], [$companyA->getKey()]);
    $user->unsetRelation('roles')->unsetRelation('permissions');

    $activate = function (Company $company) use ($user): void {
        Auth::forgetGuards();
        Auth::shouldUse('web');
        Auth::guard('web')->login($user);
        CompanyHelper::setActive([$company->getKey()]);
        $user->unsetRelation('roles')->unsetRelation('permissions');
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    };

    $activate($companyA);
    $visibleInA = $user->hasPermissionTo($permission->name);

    $activate($companyB);
    $visibleInB = $user->hasPermissionTo($permission->name);

    // Navigation must not offer an area the user cannot act in, so the
    // session-scoped answer differs from the record-scoped one above.
    expect($visibleInA)->toBeTrue()
        ->and($visibleInB)->toBeFalse()
        ->and(app(TenantRoleResolver::class)->companyIdsHolding($user, $permission->name))
        ->toBe([(int) $companyA->getKey()]);
});

it('rejects a second membership row for the same user and company', function () {
    $company = CompanyHelper::company(['name' => 'SaaS Membership Unique', 'is_active' => true]);

    $user = User::withoutEvents(fn (): User => User::factory()->create([
        'default_company_id' => $company->getKey(),
        'is_active'          => true,
        'is_default'         => false,
    ]));

    DB::table('user_allowed_companies')->insert([
        'user_id'    => $user->getKey(),
        'company_id' => $company->getKey(),
    ]);

    expect(fn () => DB::table('user_allowed_companies')->insert([
        'user_id'    => $user->getKey(),
        'company_id' => $company->getKey(),
    ]))->toThrow(QueryException::class);
});
