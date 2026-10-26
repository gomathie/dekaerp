<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\PermissionRegistrar;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\Permission;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Services\CompanyContext;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';

/**
 * SAAS-00 characterization: pins TODAY'S behaviour, it does not endorse it.
 *
 * A user's role is a property of the user, not of a company membership. Spatie
 * teams are disabled (`config/permission.php` `'teams' => false`), and neither
 * the `user_allowed_companies` membership row nor the `model_has_roles`
 * assignment row has a company column, so there is nowhere to record
 * "supervisor in A, ordinary in B". One permission therefore resolves in every
 * company the user belongs to.
 *
 * SAAS-01 (membership-role foundation) must make this test fail by design. When
 * it does, invert the expectation deliberately - do not weaken production code
 * to keep this green.
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

it('keeps one tenant role global to the user instead of scoping it per company membership', function () {
    $companyA = CompanyHelper::company(['name' => 'SaaS Characterization A', 'is_active' => true]);
    $companyB = CompanyHelper::company(['name' => 'SaaS Characterization B', 'is_active' => true]);

    $permission = Permission::query()->firstOrCreate([
        'name'       => 'view_any_security_user',
        'guard_name' => 'web',
    ]);

    // Negative control: a real permission this role does not hold. Without it,
    // a user that resolved every ability (a leaked bypass, a default role, a
    // Gate::before regression) would satisfy both company checks below and the
    // test would prove nothing.
    $withheld = Permission::query()->firstOrCreate([
        'name'       => 'delete_security_user',
        'guard_name' => 'web',
    ]);

    $role = Role::query()->create([
        'name'       => 'SaaS Characterization Supervisor '.fake()->unique()->numerify('####'),
        'guard_name' => 'web',
        'is_default' => false,
    ]);

    $role->syncPermissions([$permission]);

    $user = User::withoutEvents(fn (): User => User::factory()->create([
        'default_company_id'  => $companyA->getKey(),
        'resource_permission' => PermissionType::INDIVIDUAL,
        'is_active'           => true,
        'is_default'          => false,
    ]));

    $user->allowedCompanies()->sync([$companyA->getKey(), $companyB->getKey()]);
    $user->syncRoles([$role->getKey()]);
    $user->unsetRelation('roles')->unsetRelation('permissions');

    app(PermissionRegistrar::class)->forgetCachedPermissions();

    $roleTable = config('permission.table_names.model_has_roles');
    $rolePermissionTable = config('permission.table_names.role_has_permissions');

    $membershipCompanyIds = DB::table('user_allowed_companies')
        ->where('user_id', $user->getKey())
        ->orderBy('company_id')
        ->pluck('company_id')
        ->map(fn ($id): int => (int) $id)
        ->all();

    // Preconditions. Without all of these, "permitted in both companies" could
    // pass because nothing was assigned, or because a privileged role leaked in.
    expect($membershipCompanyIds)->toBe([$companyA->getKey(), $companyB->getKey()])
        ->and($user->fresh()->getRoleNames()->all())->toBe([$role->name])
        ->and(DB::table($rolePermissionTable)
            ->where('role_id', $role->getKey())
            ->where('permission_id', $permission->getKey())
            ->count())->toBe(1)
        ->and($role->fresh()->hasPermissionTo($permission->name))->toBeTrue();

    $activate = function (Company $company) use ($user): void {
        Auth::forgetGuards();
        Auth::shouldUse('web');
        Auth::guard('web')->login($user);
        CompanyHelper::setActive([$company->getKey()]);
        $user->unsetRelation('roles')->unsetRelation('permissions');
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    };

    $activate($companyA);

    $currentInA = app(CompanyContext::class)->currentId();
    $permittedInA = $user->hasPermissionTo($permission->name);
    $withheldInA = $user->hasPermissionTo($withheld->name);

    $activate($companyB);

    $currentInB = app(CompanyContext::class)->currentId();
    $permittedInB = $user->hasPermissionTo($permission->name);
    $withheldInB = $user->hasPermissionTo($withheld->name);
    $gateInB = Auth::user()->can($permission->name);

    // The active company really changed twice; without this, the second check
    // would be a repeat of the first rather than a second company. The withheld
    // permission stays denied in both, so the positive results above come from
    // the role assignment being global, not from the check being permissive.
    expect($currentInA)->toBe($companyA->getKey())
        ->and($currentInB)->toBe($companyB->getKey())
        ->and($permittedInA)->toBeTrue()
        ->and($permittedInB)->toBeTrue()
        ->and($withheldInA)->toBeFalse()
        ->and($withheldInB)->toBeFalse()
        ->and($gateInB)->toBeTrue();

    // Why it cannot be otherwise yet: exactly one role assignment exists per
    // user, and neither row that could carry a company dimension has one.
    expect(DB::table($roleTable)
        ->where('model_id', $user->getKey())
        ->where('model_type', $user->getMorphClass())
        ->count())->toBe(1)
        ->and(Schema::hasColumn($roleTable, 'company_id'))->toBeFalse()
        ->and(Schema::hasColumn('user_allowed_companies', 'role_id'))->toBeFalse();
});
