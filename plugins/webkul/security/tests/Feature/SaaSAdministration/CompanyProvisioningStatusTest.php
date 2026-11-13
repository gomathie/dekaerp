<?php

use Filament\Actions\Testing\TestAction;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Webkul\Security\Filament\Resources\CompanyResource\Pages\ListCompanies;
use Webkul\Security\Mail\UserInvitationMail;
use Webkul\Security\Models\Company as SecurityCompany;
use Webkul\Security\Models\Invitation;
use Webkul\Security\Services\CompanyAdministratorAccount;
use Webkul\Security\Services\CompanyAdministratorInviter;
use Webkul\Security\Services\CompanyAdminRoleProvisioner;
use Webkul\Security\Services\CompanyProvisioningStatus;
use Webkul\Security\Settings\UserSettings;

require_once __DIR__.'/../Authorization/MultiCompanyAdminTest.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';

/**
 * Company setup status (comprehensive review CR-7, 2026-11-13): staff must be
 * able to see which new companies still have nobody who can sign in, and send
 * the invitation again. The rule pinned here is the plan's acceptance: no
 * company shows Active while its administrator cannot log in.
 */
beforeEach(function (): void {
    app(CompanyAdminRoleProvisioner::class)->provision();

    settings(UserSettings::class)->enable_user_invitation = true;
    settings(UserSettings::class)->save();

    Mail::fake();
});

function provisioningStatusOf($company): string
{
    return app(CompanyProvisioningStatus::class)->status($company->fresh());
}

it('reports each setup stage from the invitation and administrator rows', function () {
    $superAdmin = multiCompanyAdminTestSuperAdmin();
    multiCompanyAdminTestAuthenticate($superAdmin, []);

    $company = multiCompanyAdminTestCompany();
    expect(provisioningStatusOf($company))->toBe(CompanyProvisioningStatus::NO_ADMINISTRATOR);

    app(CompanyAdministratorInviter::class)->invite($company, $superAdmin, 'owner@setup-status.test');
    expect(provisioningStatusOf($company))->toBe(CompanyProvisioningStatus::INVITED);

    Invitation::query()->where('default_company_id', $company->getKey())->update(['expires_at' => now()->subDay()]);
    expect(provisioningStatusOf($company))->toBe(CompanyProvisioningStatus::EXPIRED);

    app(CompanyAdministratorAccount::class)->setPassword($company, $superAdmin, 'admin2@setup-status.test', 'Correct-horse-42');
    expect(provisioningStatusOf($company))->toBe(CompanyProvisioningStatus::ACTIVE);
});

it('does not show Active while the only administrator is switched off', function () {
    $superAdmin = multiCompanyAdminTestSuperAdmin();
    multiCompanyAdminTestAuthenticate($superAdmin, []);

    $company = multiCompanyAdminTestCompany();
    app(CompanyAdministratorAccount::class)->setPassword($company, $superAdmin, 'off@setup-status.test', 'Correct-horse-42');

    app(CompanyAdministratorAccount::class)->administrators($company)
        ->each(fn ($user) => $user->forceFill(['is_active' => false])->save());

    expect(provisioningStatusOf($company))->toBe(CompanyProvisioningStatus::NO_ADMINISTRATOR);
});

it('reports an archived or switched-off company as closed', function () {
    $company = multiCompanyAdminTestCompany();
    $company->forceFill(['is_active' => false])->save();

    expect(provisioningStatusOf($company))->toBe(CompanyProvisioningStatus::CLOSED);
});

it('filters the companies list to invited companies and resends their invitation', function () {
    $superAdmin = multiCompanyAdminTestSuperAdmin();
    multiCompanyAdminTestAuthenticate($superAdmin, []);
    FilamentHelper::bootAdminPanel();

    $invited = multiCompanyAdminTestCompany('Invited Co');
    $active = multiCompanyAdminTestCompany('Active Co');
    app(CompanyAdministratorInviter::class)->invite($invited, $superAdmin, 'owner@invited-co.test');
    app(CompanyAdministratorAccount::class)->setPassword($active, $superAdmin, 'owner@active-co.test', 'Correct-horse-42');

    Invitation::query()->where('default_company_id', $invited->getKey())->update(['expires_at' => now()->addHour()]);
    Mail::fake();

    $invitedRecord = SecurityCompany::query()->findOrFail($invited->getKey());
    $activeRecord = SecurityCompany::query()->findOrFail($active->getKey());

    Livewire::test(ListCompanies::class)
        ->assertActionHidden(TestAction::make('resendAdministratorInvitation')->table($activeRecord))
        ->filterTable('provisioning_status', [CompanyProvisioningStatus::INVITED])
        ->assertCanSeeTableRecords([$invitedRecord])
        ->assertCanNotSeeTableRecords([$activeRecord])
        ->callAction(TestAction::make('resendAdministratorInvitation')->table($invitedRecord))
        ->assertNotified();

    // Renewed for the full window and mailed again: the reason staff resend.
    expect(Invitation::query()->where('default_company_id', $invited->getKey())->firstOrFail()->expires_at->isAfter(now()->addDays(6)))->toBeTrue();
    Mail::assertSent(UserInvitationMail::class);
});
