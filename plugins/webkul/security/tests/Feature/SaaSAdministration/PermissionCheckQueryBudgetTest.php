<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\Permission;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\User;
use Webkul\Security\Services\TenantRoleAssigner;
use Webkul\Support\Services\CompanyContext;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';

/**
 * A budget for the cost of one permission check, measured rather than guessed.
 *
 * Review finding (2026-10-28): after SAAS-01 a *denied* check consults the global
 * store and then the membership store, so it costs more than it used to, and a
 * Filament page that renders a table of records asks the question once per row.
 * The instruction was to measure before adding caching - this is the
 * measurement, and it is a regression guard, not an optimisation.
 *
 * The assertion is a ceiling, not an exact count: a legitimate extra query (a
 * new guard, a policy that loads a relation) should not fail the suite, but a
 * change that turns one check into a loop should. If caching is added later,
 * these numbers go down and the ceiling stays where it is.
 *
 * The ceiling applies to a *warm* check, because that is what a table pays per
 * row. The first check in a request also pays four one-time queries, measured
 * 2026-10-29: the user's direct permissions (Spatie, once per model), the
 * membership-table probe (memoised in TenantRoleResolver), and CompanyContext's
 * allowed companies (two queries, memoised for the request). Measuring that
 * first check read 6 against this ceiling and failed on setup, not per-row cost.
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

/**
 * @return array{0: int, 1: bool, 2: string} query cost of this call, the result,
 *                                           and the SQL it ran (for failure output)
 */
function permissionCheckCost(User $user, string $ability): array
{
    // A single listener for the whole test, recording monotonically: registering
    // one per call would double count, and DB::listen cannot be removed.
    static $statements = [];
    static $registered = false;

    if (! $registered) {
        DB::listen(function ($query) use (&$statements): void {
            $statements[] = $query->sql;
        });

        $registered = true;
    }

    $before = count($statements);

    $result = $user->hasPermissionTo($ability);

    $ran = array_slice($statements, $before);

    return [count($ran), $result, implode("\n", $ran)];
}

it('measures what one permission check costs', function () {
    $company = CompanyHelper::company(['name' => 'Query Budget A', 'is_active' => true]);

    $granted = Permission::query()->firstOrCreate(['name' => 'view_any_query_budget', 'guard_name' => 'web']);
    $withheld = Permission::query()->firstOrCreate(['name' => 'delete_query_budget', 'guard_name' => 'web']);

    $role = Role::query()->create([
        'name'       => 'Query Budget Role '.fake()->unique()->numerify('####'),
        'guard_name' => 'web',
        'is_default' => false,
    ]);

    $role->syncPermissions([$granted]);

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

    // One cold check first, so the measured ones pay only per-check cost.
    [$coldCost, , $coldSql] = permissionCheckCost($user, $withheld->name);

    [$grantedCost, $grantedResult, $grantedSql] = permissionCheckCost($user, $granted->name);
    [$deniedCost, $deniedResult, $deniedSql] = permissionCheckCost($user, $withheld->name);

    // Preconditions: the two checks really do answer differently, so neither
    // number can come from a short-circuited path.
    expect($grantedResult)->toBeTrue()
        ->and($deniedResult)->toBeFalse();

    // Recorded here as well as asserted, so the number is in the failure output
    // when it changes.
    $cold = "(the first, cold check cost {$coldCost}:\n{$coldSql})";

    expect($grantedCost)->toBeLessThanOrEqual(4, "a granted check cost {$grantedCost} queries:\n{$grantedSql}\n{$cold}")
        ->and($deniedCost)->toBeLessThanOrEqual(4, "a denied check cost {$deniedCost} queries:\n{$deniedSql}\n{$cold}");
});

it('does not grow the cost of a check when the same question is asked twice', function () {
    $company = CompanyHelper::company(['name' => 'Query Budget Repeat', 'is_active' => true]);

    $withheld = Permission::query()->firstOrCreate(['name' => 'delete_query_budget_repeat', 'guard_name' => 'web']);

    $user = User::withoutEvents(fn (): User => User::factory()->create([
        'default_company_id'  => $company->getKey(),
        'resource_permission' => PermissionType::GLOBAL,
        'is_active'           => true,
        'is_default'          => false,
    ]));

    $user->allowedCompanies()->sync([$company->getKey()]);

    Auth::shouldUse('web');
    Auth::guard('web')->login($user);
    CompanyHelper::setActive([$company->getKey()]);
    app(PermissionRegistrar::class)->forgetCachedPermissions();

    [$firstCost] = permissionCheckCost($user, $withheld->name);
    [$secondCost] = permissionCheckCost($user, $withheld->name);

    // Equal, not zero: nothing is cached across calls today. If a cache is added
    // the second cost drops, which is the measurement the review asked for.
    expect($secondCost)->toBeLessThanOrEqual($firstCost)
        ->and($firstCost)->toBeLessThanOrEqual(4);
});
