<?php

use Illuminate\Support\Facades\Mail;
use Webkul\Security\Models\Invitation;
use Webkul\Security\Models\Permission;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\User;
use Webkul\Security\Services\CompanyAdministratorAccount;
use Webkul\Security\Services\CompanyAdministratorInviter;
use Webkul\Security\Services\CompanyAdminRoleProvisioner;
use Webkul\Security\Services\TenantRoleResolver;
use Webkul\Security\Settings\UserSettings;

require_once __DIR__.'/MultiCompanyAdminTest.php';

/**
 * A new company administrator gets the configured business role alongside
 * "Company Admin" (user, 2026-11-11). "Company Admin" alone only manages
 * users, so administrators signed in to an almost empty panel. A platform
 * role is never handed out this way, whatever the setting says.
 */
beforeEach(function (): void {
    app(CompanyAdminRoleProvisioner::class)->provision();

    settings(UserSettings::class)->enable_user_invitation = true;
    settings(UserSettings::class)->save();

    Mail::fake();
});

function businessRoleTestUseRole(?Role $role): void
{
    settings(UserSettings::class)->company_admin_business_role_id = $role?->getKey();
    settings(UserSettings::class)->save();
}

it('gives a new company administrator the business role in that company', function () {
    $superAdmin = multiCompanyAdminTestSuperAdmin();
    multiCompanyAdminTestAuthenticate($superAdmin, []);
    $company = multiCompanyAdminTestCompany();

    $business = Role::query()->create(['name' => 'Tenant Operations', 'guard_name' => 'web']);
    businessRoleTestUseRole($business);

    expect(app(CompanyAdministratorAccount::class)->setPassword($company, $superAdmin, 'ops.admin@business-role.test', 'Correct-horse-42'))
        ->toBe(CompanyAdministratorAccount::CREATED);

    $user = User::query()->where('email', 'ops.admin@business-role.test')->firstOrFail();
    $resolver = app(TenantRoleResolver::class);

    expect($user->isCompanyAdmin((int) $company->getKey()))->toBeTrue()
        ->and($resolver->userIdsWithRoleInCompany('Tenant Operations', (int) $company->getKey()))->toContain($user->getKey());
});

it('puts the business role on the administrator invitation', function () {
    $superAdmin = multiCompanyAdminTestSuperAdmin();
    multiCompanyAdminTestAuthenticate($superAdmin, []);
    $company = multiCompanyAdminTestCompany();

    $business = Role::query()->create(['name' => 'Tenant Operations', 'guard_name' => 'web']);
    businessRoleTestUseRole($business);

    expect(app(CompanyAdministratorInviter::class)->invite($company, $superAdmin, 'invited.admin@business-role.test'))
        ->toBe(CompanyAdministratorInviter::INVITED);

    $invitation = Invitation::query()->where('email', 'invited.admin@business-role.test')->firstOrFail();

    expect(array_map('intval', $invitation->role_ids))
        ->toBe([CompanyAdministratorInviter::companyAdminRoleId(), (int) $business->getKey()]);
});

it('never hands a client administrator a platform role as the business role', function () {
    $platform = Role::query()->create(['name' => 'Platform Operations', 'guard_name' => 'web']);
    $platform->givePermissionTo(Permission::findOrCreate('bypass_company_scope', 'web'));

    businessRoleTestUseRole($platform);
    expect(CompanyAdministratorInviter::administratorRoleIds())->toBe([CompanyAdministratorInviter::companyAdminRoleId()]);

    $superAdminRole = Role::query()->whereRaw('LOWER(name) = ?', ['admin'])->firstOrFail();
    businessRoleTestUseRole($superAdminRole);
    expect(CompanyAdministratorInviter::administratorRoleIds())->toBe([CompanyAdministratorInviter::companyAdminRoleId()]);
});

it('gives only Company Admin when no business role is configured', function () {
    businessRoleTestUseRole(null);

    expect(CompanyAdministratorInviter::administratorRoleIds())->toBe([CompanyAdministratorInviter::companyAdminRoleId()]);
});
