<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Spatie\Permission\PermissionRegistrar;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Filament\Resources\UserResource;
use Webkul\Security\Models\User;
use Webkul\Security\Policies\UserPolicy;
use Webkul\Security\Services\MultiCompanyAdminRoleProvisioner;
use Webkul\Support\Services\CompanyContext;

require_once __DIR__.'/MultiCompanyAdminTest.php';

/**
 * The Users list and UserPolicy must not cross the company boundary.
 *
 * Until 2026-09-24 they did. `scopeManageableUsers()` company-scoped its query
 * for Multi-Company Admins only; every other actor got `ownership()` alone, and
 * `ownership()` places no restriction at all on a user whose resource permission
 * is `global`. A customer's administrator therefore listed, opened and edited
 * every user in the installation, including other tenants'.
 *
 * These tests use a plain role - not Multi-Company Admin, not super admin -
 * because that is the actor that was exposed. See
 * docs/company-admin-role-plan.md.
 */
beforeEach(function (): void {
    Auth::forgetGuards();
    session()->forget(CompanyContext::SESSION_KEY);
    app()->forgetInstance(CompanyContext::class);
    app(PermissionRegistrar::class)->forgetCachedPermissions();
    app(MultiCompanyAdminRoleProvisioner::class)->provision();
    URL::resolveMissingNamedRoutesUsing(fn (): string => '#');
});

/**
 * @return array<int, int>
 */
function visibleUserIds(): array
{
    return UserResource::getEloquentQuery()->pluck('id')->map(fn ($id): int => (int) $id)->all();
}

it('does not show another company’s users to a global company admin', function () {
    $mine = multiCompanyAdminTestCompany('Mine');
    $theirs = multiCompanyAdminTestCompany('Theirs');

    // The exact shape from the report: a custom role, one company, and a
    // resource permission of "global" - which is what removes every ownership
    // restriction.
    $actor = multiCompanyAdminTestUser($mine, null, [
        'resource_permission' => PermissionType::GLOBAL,
    ]);

    $colleague = multiCompanyAdminTestUser($mine);
    $stranger = multiCompanyAdminTestUser($theirs);

    multiCompanyAdminTestGrantPermissions($actor, ['view_any_security_user', 'view_security_user']);
    multiCompanyAdminTestAuthenticate($actor, [$mine->getKey()]);

    $visible = visibleUserIds();

    expect($visible)->toContain($colleague->getKey())
        ->and($visible)->toContain($actor->getKey())
        ->and($visible)->not->toContain($stranger->getKey());
});

it('refuses to open another company’s user by policy, not only in the list', function () {
    $mine = multiCompanyAdminTestCompany('Mine');
    $theirs = multiCompanyAdminTestCompany('Theirs');

    $actor = multiCompanyAdminTestUser($mine, null, [
        'resource_permission' => PermissionType::GLOBAL,
    ]);

    $stranger = multiCompanyAdminTestUser($theirs);

    multiCompanyAdminTestGrantPermissions($actor, [
        'view_any_security_user',
        'view_security_user',
        'update_security_user',
        'delete_security_user',
    ]);
    multiCompanyAdminTestAuthenticate($actor, [$mine->getKey()]);

    $policy = app(UserPolicy::class);

    // A record hidden from the list must not be reachable by its URL either.
    expect($policy->view($actor, $stranger))->toBeFalse()
        ->and($policy->update($actor, $stranger))->toBeFalse()
        ->and($policy->delete($actor, $stranger))->toBeFalse();
});

it('still lets a company admin manage their own company’s users', function () {
    $mine = multiCompanyAdminTestCompany('Mine');

    $actor = multiCompanyAdminTestUser($mine, null, [
        'resource_permission' => PermissionType::GLOBAL,
    ]);

    $colleague = multiCompanyAdminTestUser($mine);

    multiCompanyAdminTestGrantPermissions($actor, [
        'view_any_security_user',
        'view_security_user',
        'update_security_user',
    ]);
    multiCompanyAdminTestAuthenticate($actor, [$mine->getKey()]);

    $policy = app(UserPolicy::class);

    // The point of the fix is not to lock them out of their own company.
    expect($policy->view($actor, $colleague))->toBeTrue()
        ->and($policy->update($actor, $colleague))->toBeTrue();
});

it('sees users of every company it holds, and none beyond', function () {
    $first = multiCompanyAdminTestCompany('First');
    $second = multiCompanyAdminTestCompany('Second');
    $outside = multiCompanyAdminTestCompany('Outside');

    $actor = multiCompanyAdminTestUser([$first, $second], null, [
        'resource_permission' => PermissionType::GLOBAL,
    ]);

    $inFirst = multiCompanyAdminTestUser($first);
    $inSecond = multiCompanyAdminTestUser($second);
    $inOutside = multiCompanyAdminTestUser($outside);

    multiCompanyAdminTestGrantPermissions($actor, ['view_any_security_user']);
    multiCompanyAdminTestAuthenticate($actor, [$first->getKey(), $second->getKey()]);

    $visible = visibleUserIds();

    expect($visible)->toContain($inFirst->getKey())
        ->and($visible)->toContain($inSecond->getKey())
        ->and($visible)->not->toContain($inOutside->getKey());
});

it('hides a user who also belongs to a company the actor does not hold', function () {
    $mine = multiCompanyAdminTestCompany('Mine');
    $theirs = multiCompanyAdminTestCompany('Theirs');

    $actor = multiCompanyAdminTestUser($mine, null, [
        'resource_permission' => PermissionType::GLOBAL,
    ]);

    // Shared with another tenant: listing them would put that tenant's name on
    // the row, which is the leak in miniature.
    $shared = multiCompanyAdminTestUser([$mine, $theirs]);

    multiCompanyAdminTestGrantPermissions($actor, ['view_any_security_user', 'view_security_user']);
    multiCompanyAdminTestAuthenticate($actor, [$mine->getKey()]);

    expect(visibleUserIds())->not->toContain($shared->getKey())
        ->and(app(UserPolicy::class)->view($actor, $shared))->toBeFalse();
});

it('applies the company boundary whatever the resource permission', function () {
    $mine = multiCompanyAdminTestCompany('Mine');
    $theirs = multiCompanyAdminTestCompany('Theirs');

    $actor = multiCompanyAdminTestUser($mine, null, [
        'resource_permission' => PermissionType::INDIVIDUAL,
    ]);

    $stranger = multiCompanyAdminTestUser($theirs);

    multiCompanyAdminTestGrantPermissions($actor, ['view_any_security_user']);
    multiCompanyAdminTestAuthenticate($actor, [$mine->getKey()]);

    // The company filter stands on its own: it does not depend on the resource
    // permission to hold the boundary.
    //
    // What this test deliberately does NOT assert is that `individual` also
    // hides a same-company colleague. It does in the browser, but it cannot be
    // shown here: OwnershipScope::apply() returns early on
    // app()->runningInConsole(), and `artisan test` is the console. Unlike
    // AllowedCompanyScope, which exempts tests with `&& ! runningUnitTests()`,
    // OwnershipScope has no such exemption, so ownership() is inert under Pest
    // and every ownership rule in this application is untested. Recorded in
    // docs/company-admin-role-plan.md; not changed here, because switching it on
    // would alter the visible data of every existing test at once.
    expect(visibleUserIds())->not->toContain($stranger->getKey());
});

it('still shows a user their own row when they hold no company', function () {
    $mine = multiCompanyAdminTestCompany('Mine');

    $actor = multiCompanyAdminTestUser($mine, null, [
        'resource_permission' => PermissionType::GLOBAL,
    ]);

    // Companies removed after the fact - a broken state, but they should not
    // vanish from their own Users page over it.
    $actor->allowedCompanies()->sync([]);

    multiCompanyAdminTestGrantPermissions($actor, ['view_any_security_user']);
    multiCompanyAdminTestAuthenticate($actor, [$mine->getKey()]);

    expect(visibleUserIds())->toBe([$actor->getKey()]);
});

it('leaves the super admin seeing everyone', function () {
    $mine = multiCompanyAdminTestCompany('Mine');
    $theirs = multiCompanyAdminTestCompany('Theirs');

    $stranger = multiCompanyAdminTestUser($theirs);
    $colleague = multiCompanyAdminTestUser($mine);

    $superAdmin = multiCompanyAdminTestSuperAdmin();

    multiCompanyAdminTestAuthenticate($superAdmin, [$mine->getKey(), $theirs->getKey()]);

    $visible = visibleUserIds();

    expect($visible)->toContain($stranger->getKey())
        ->and($visible)->toContain($colleague->getKey());
});

it('leaves a multi-company admin scoped to its assigned companies', function () {
    $first = multiCompanyAdminTestCompany('First');
    $outside = multiCompanyAdminTestCompany('Outside');

    $administrator = multiCompanyAdminTestAdministrator($first);

    $inFirst = multiCompanyAdminTestUser($first);
    $inOutside = multiCompanyAdminTestUser($outside);

    multiCompanyAdminTestAuthenticate($administrator, [$first->getKey()]);

    $visible = visibleUserIds();

    // Unchanged by this fix: the branch that was already correct.
    expect($visible)->toContain($inFirst->getKey())
        ->and($visible)->not->toContain($inOutside->getKey())
        ->and($visible)->not->toContain($administrator->getKey());
});
