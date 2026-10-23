<?php

use Illuminate\Support\Facades\Mail;
use Webkul\Security\Mail\UserInvitationMail;
use Webkul\Security\Models\Invitation;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\User;
use Webkul\Security\Services\CompanyAdministratorInviter;
use Webkul\Security\Services\CompanyAdminRoleProvisioner;
use Webkul\Security\Services\UserInvitationService;
use Webkul\Security\Settings\UserSettings;

require_once __DIR__.'/MultiCompanyAdminTest.php';

/**
 * A new company should arrive with its own administrator invited, so a tenant
 * can sign in and manage their own people instead of DEKA staff creating every
 * user by hand (user, 2026-10-21).
 *
 * Asserted against CompanyAdministratorInviter, which is where the decision
 * lives. Driving it through the create page instead would mean filling the
 * whole inherited company form, and would still prove nothing extra: the page
 * only turns the reason returned here into a notification.
 */
beforeEach(function (): void {
    app(CompanyAdminRoleProvisioner::class)->provision();

    settings(UserSettings::class)->enable_user_invitation = true;
    settings(UserSettings::class)->save();

    Mail::fake();
});

it('invites the company email as Company Admin of that company', function () {
    $superAdmin = multiCompanyAdminTestSuperAdmin();
    multiCompanyAdminTestAuthenticate($superAdmin, []);

    $company = multiCompanyAdminTestCompany();
    $company->forceFill(['email' => 'admin@tenant-one.test'])->save();

    $reason = app(CompanyAdministratorInviter::class)->invite($company, $superAdmin);

    $invitation = Invitation::query()->where('email', 'admin@tenant-one.test')->firstOrFail();

    // The invitation must carry the new company and the Company Admin role, or
    // accepting it produces someone who cannot administer anybody.
    expect($reason)->toBe(CompanyAdministratorInviter::INVITED)
        ->and((int) $invitation->default_company_id)->toBe((int) $company->getKey())
        ->and(array_map('intval', $invitation->company_ids))->toBe([(int) $company->getKey()])
        ->and(array_map('intval', $invitation->role_ids))->toBe([CompanyAdministratorInviter::companyAdminRoleId()]);

    Mail::assertSent(UserInvitationMail::class);
});

it('never puts a password in the invitation email', function () {
    // The whole reason this is an invitation and not a created user.
    $superAdmin = multiCompanyAdminTestSuperAdmin();
    multiCompanyAdminTestAuthenticate($superAdmin, []);

    $company = multiCompanyAdminTestCompany();
    $company->forceFill(['email' => 'admin@tenant-nopass.test'])->save();

    app(CompanyAdministratorInviter::class)->invite($company, $superAdmin);

    $invitation = Invitation::query()->where('email', 'admin@tenant-nopass.test')->firstOrFail();

    // No user exists yet, so there is no password anywhere to leak.
    expect(User::query()->where('email', 'admin@tenant-nopass.test')->exists())->toBeFalse()
        ->and($invitation->getAttributes())->not->toHaveKey('password');
});

it('reports a missing company email instead of inviting nobody silently', function () {
    $superAdmin = multiCompanyAdminTestSuperAdmin();
    multiCompanyAdminTestAuthenticate($superAdmin, []);

    $company = multiCompanyAdminTestCompany();
    // Explicit, not trusted from the factory: a factory that filled email would
    // make this test assert the opposite of its name and still pass.
    $company->forceFill(['email' => null])->save();

    $reason = app(CompanyAdministratorInviter::class)->invite($company, $superAdmin);

    expect($reason)->toBe(CompanyAdministratorInviter::MISSING_EMAIL)
        ->and(Invitation::query()->where('default_company_id', $company->getKey())->exists())->toBeFalse();

    Mail::assertNotSent(UserInvitationMail::class);
});

it('reports a failure rather than throwing when the email already belongs to a user', function () {
    // The company is already saved when this runs, so a duplicate address must
    // not surface as a crash on the create page.
    $superAdmin = multiCompanyAdminTestSuperAdmin();
    multiCompanyAdminTestAuthenticate($superAdmin, []);

    $company = multiCompanyAdminTestCompany();
    $company->forceFill(['email' => $superAdmin->email])->save();

    $reason = app(CompanyAdministratorInviter::class)->invite($company, $superAdmin);

    expect($reason)->toBe(CompanyAdministratorInviter::INVITE_FAILED)
        ->and(Invitation::query()->where('default_company_id', $company->getKey())->exists())->toBeFalse();
});

it('keeps the invitation and its link usable when the email cannot be sent', function () {
    // The reason this matters: before acceptUrl() existed on the model, the
    // link lived only inside the email. A failed send left the invitation in
    // the database but unreachable, so the tenant could never be onboarded.
    Mail::shouldReceive('to')->andThrow(new RuntimeException('smtp down'));

    $superAdmin = multiCompanyAdminTestSuperAdmin();
    multiCompanyAdminTestAuthenticate($superAdmin, []);

    $company = multiCompanyAdminTestCompany();
    $company->forceFill(['email' => 'admin@tenant-nomail.test'])->save();

    $reason = app(CompanyAdministratorInviter::class)->invite($company, $superAdmin);

    $invitation = Invitation::query()->where('email', 'admin@tenant-nomail.test')->firstOrFail();

    expect($reason)->toBe(CompanyAdministratorInviter::MAIL_FAILED)
        // The row survives, so the link can still be handed over.
        ->and($invitation->acceptUrl())->toContain('signature=')
        ->and($invitation->acceptUrl())->toContain('expires=');
});

it('does nothing when invitations are switched off', function () {
    settings(UserSettings::class)->enable_user_invitation = false;
    settings(UserSettings::class)->save();

    $superAdmin = multiCompanyAdminTestSuperAdmin();
    multiCompanyAdminTestAuthenticate($superAdmin, []);

    $company = multiCompanyAdminTestCompany();
    $company->forceFill(['email' => 'admin@tenant-off.test'])->save();

    $reason = app(CompanyAdministratorInviter::class)->invite($company, $superAdmin);

    expect($reason)->toBe(CompanyAdministratorInviter::DISABLED)
        ->and(Invitation::query()->where('email', 'admin@tenant-off.test')->exists())->toBeFalse();

    Mail::assertNotSent(UserInvitationMail::class);
});

it('grants the administrator their own company only, and no way to escalate', function () {
    $superAdmin = multiCompanyAdminTestSuperAdmin();
    multiCompanyAdminTestAuthenticate($superAdmin, []);

    $company = multiCompanyAdminTestCompany();
    $other = multiCompanyAdminTestCompany();

    $invitation = app(UserInvitationService::class)->create(
        $superAdmin,
        'owner@tenant-two.test',
        (int) $company->getKey(),
        [CompanyAdministratorInviter::companyAdminRoleId()],
    );

    $user = app(UserInvitationService::class)->accept($invitation, 'Tenant Owner', 'correct-horse-battery');

    $allowed = $user->allowedCompanies()->pluck('companies.id')->map(fn ($id): int => (int) $id)->all();

    $granted = Role::query()
        ->whereRaw('LOWER(name) = ?', [mb_strtolower(Role::COMPANY_ADMIN)])
        ->firstOrFail()
        ->permissions()
        ->pluck('name')
        ->all();

    expect($allowed)->toBe([(int) $company->getKey()])
        ->and($allowed)->not->toContain((int) $other->getKey())
        ->and($user->hasRole(Role::COMPANY_ADMIN))->toBeTrue()
        ->and($granted)->toContain('create_security_user')
        // Asserted against the provisioned set, so widening the role fails here:
        // no bulk or permanent deletion of people, and no handing out roles.
        ->and($granted)->not->toContain('delete_any_security_user')
        ->and($granted)->not->toContain('force_delete_security_user')
        ->and($granted)->not->toContain('create_security_role')
        ->and($granted)->not->toContain('update_security_role');
});
