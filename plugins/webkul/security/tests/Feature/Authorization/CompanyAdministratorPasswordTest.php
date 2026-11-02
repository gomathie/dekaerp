<?php

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Filament\Resources\CompanyResource\Pages\EditCompany;
use Webkul\Security\Mail\UserInvitationMail;
use Webkul\Security\Models\Invitation;
use Webkul\Security\Models\User;
use Webkul\Security\Services\CompanyAdministratorAccount;
use Webkul\Security\Services\CompanyAdministratorInviter;
use Webkul\Security\Services\CompanyAdminRoleProvisioner;
use Webkul\Security\Services\SeatCapacityService;
use Webkul\Security\Settings\UserSettings;
use Webkul\Support\Models\Company;

require_once __DIR__.'/MultiCompanyAdminTest.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';

/**
 * Setting a company administrator's password from the company form
 * (user, 2026-11-03): clients phone DEKA to have it set or reset, and an
 * existing company had no way to get an administrator from this form at all.
 *
 * This supersedes "the company form never handles a password" (2026-10-23).
 * What must still hold is pinned here: the password is never emailed, the
 * form cannot reach a user who is not this company's administrator, and the
 * seat and assignment rules apply exactly as on the Users page.
 */
beforeEach(function (): void {
    app(CompanyAdminRoleProvisioner::class)->provision();

    settings(UserSettings::class)->enable_user_invitation = true;
    settings(UserSettings::class)->save();

    Mail::fake();
});

function companyAdministratorPasswordSet(Company $company, User $actor, string $email, string $password, bool $mustChange = true): string
{
    return app(CompanyAdministratorAccount::class)->setPassword($company, $actor, $email, $password, $mustChange);
}

it('creates the company administrator with the password it was given and emails nothing', function () {
    $superAdmin = multiCompanyAdminTestSuperAdmin();
    multiCompanyAdminTestAuthenticate($superAdmin, []);

    $company = multiCompanyAdminTestCompany();

    $reason = companyAdministratorPasswordSet($company, $superAdmin, 'Ama.Mensah@tenant-pw.test', 'Correct-horse-42');

    $user = User::query()->where('email', 'Ama.Mensah@tenant-pw.test')->firstOrFail();

    expect($reason)->toBe(CompanyAdministratorAccount::CREATED)
        ->and(Hash::check('Correct-horse-42', $user->password))->toBeTrue()
        ->and($user->isCompanyAdmin((int) $company->getKey()))->toBeTrue()
        ->and($user->allowedCompanies()->pluck('companies.id')->map(fn ($id): int => (int) $id)->all())
        ->toBe([(int) $company->getKey()])
        ->and((int) $user->default_company_id)->toBe((int) $company->getKey())
        ->and($user->resource_permission)->toBe(PermissionType::GLOBAL)
        ->and($user->must_change_password)->toBeTrue();

    // The whole condition on which the user agreed to a password here: the
    // person setting it hands it over, and no mailbox ever holds it.
    Mail::assertNothingSent();
});

it('resets the password of this company’s administrator and keeps them required to change it', function () {
    $superAdmin = multiCompanyAdminTestSuperAdmin();
    multiCompanyAdminTestAuthenticate($superAdmin, []);

    $company = multiCompanyAdminTestCompany();

    companyAdministratorPasswordSet($company, $superAdmin, 'reset@tenant-pw.test', 'First-password-1');

    // Matched case-insensitively: support types what the client says on the
    // phone, which is not always how the address was first written.
    $reason = companyAdministratorPasswordSet($company, $superAdmin, 'RESET@tenant-pw.test', 'Second-password-2');

    $user = User::query()->where('email', 'reset@tenant-pw.test')->firstOrFail();

    // The flag was already true before the reset. User's updating hook clears
    // it whenever the password changes and the flag is not dirty, so a single
    // save would quietly drop it - this is what the two saves prevent.
    expect($reason)->toBe(CompanyAdministratorAccount::PASSWORD_RESET)
        ->and(Hash::check('Second-password-2', $user->password))->toBeTrue()
        ->and($user->must_change_password)->toBeTrue()
        ->and(User::query()->whereRaw('LOWER(email) = ?', ['reset@tenant-pw.test'])->count())->toBe(1);
});

it('lets the reset leave the administrator free to keep the password when asked', function () {
    $superAdmin = multiCompanyAdminTestSuperAdmin();
    multiCompanyAdminTestAuthenticate($superAdmin, []);

    $company = multiCompanyAdminTestCompany();

    companyAdministratorPasswordSet($company, $superAdmin, 'keep@tenant-pw.test', 'First-password-1');
    companyAdministratorPasswordSet($company, $superAdmin, 'keep@tenant-pw.test', 'Second-password-2', mustChange: false);

    expect(User::query()->where('email', 'keep@tenant-pw.test')->firstOrFail()->must_change_password)->toBeFalse();
});

it('refuses to change the password of a user who is not this company’s administrator', function () {
    // The account-takeover guard. Without it, whoever administers this company
    // could type another tenant's user's address and sign in as them.
    $superAdmin = multiCompanyAdminTestSuperAdmin();
    multiCompanyAdminTestAuthenticate($superAdmin, []);

    $company = multiCompanyAdminTestCompany();
    $otherTenant = multiCompanyAdminTestCompany();
    $outsider = multiCompanyAdminTestUser($otherTenant);
    $hashBefore = $outsider->password;

    $reason = companyAdministratorPasswordSet($company, $superAdmin, $outsider->email, 'Takeover-attempt-9');

    expect($reason)->toBe(CompanyAdministratorAccount::NOT_ADMINISTRATOR)
        ->and($outsider->fresh()->password)->toBe($hashBefore)
        ->and($outsider->fresh()->isCompanyAdmin((int) $company->getKey()))->toBeFalse();
});

it('refuses a Multi-Company Admin acting on a company outside their assignment', function () {
    $assigned = multiCompanyAdminTestCompany();
    $notAssigned = multiCompanyAdminTestCompany();

    $staff = multiCompanyAdminTestAdministrator($assigned);
    multiCompanyAdminTestAuthenticate($staff, [(int) $assigned->getKey()]);

    $reason = companyAdministratorPasswordSet($notAssigned, $staff, 'owner@unassigned-pw.test', 'Correct-horse-42');

    expect($reason)->toBe(CompanyAdministratorAccount::NOT_ALLOWED)
        ->and(User::query()->where('email', 'owner@unassigned-pw.test')->exists())->toBeFalse();
});

it('lets a Multi-Company Admin set the administrator of a company they are assigned', function () {
    $assigned = multiCompanyAdminTestCompany();

    $staff = multiCompanyAdminTestAdministrator($assigned);
    multiCompanyAdminTestAuthenticate($staff, [(int) $assigned->getKey()]);

    expect(companyAdministratorPasswordSet($assigned, $staff, 'owner@assigned-pw.test', 'Correct-horse-42'))
        ->toBe(CompanyAdministratorAccount::CREATED);
});

it('refuses to create an administrator when the company has no free seat', function () {
    $superAdmin = multiCompanyAdminTestSuperAdmin();
    multiCompanyAdminTestAuthenticate($superAdmin, []);

    $company = multiCompanyAdminTestCompany();
    app(SeatCapacityService::class)->setLimit($company, 0, $superAdmin);

    $reason = companyAdministratorPasswordSet($company, $superAdmin, 'full@tenant-pw.test', 'Correct-horse-42');

    expect($reason)->toBe(CompanyAdministratorAccount::SEATS_EXHAUSTED)
        ->and(User::query()->where('email', 'full@tenant-pw.test')->exists())->toBeFalse();
});

it('revokes a pending invitation to the same address when it creates the account', function () {
    // Otherwise the invitation keeps a seat for someone who already has an
    // account, and its link fails on the unique email when they click it.
    $superAdmin = multiCompanyAdminTestSuperAdmin();
    multiCompanyAdminTestAuthenticate($superAdmin, []);

    $company = multiCompanyAdminTestCompany();
    app(CompanyAdministratorInviter::class)->invite($company, $superAdmin, 'invited@tenant-pw.test');

    app(SeatCapacityService::class)->setLimit($company, 1, $superAdmin);

    $reason = companyAdministratorPasswordSet($company, $superAdmin, 'invited@tenant-pw.test', 'Correct-horse-42');

    $invitation = Invitation::query()->where('email', 'invited@tenant-pw.test')->firstOrFail();

    // A limit of one passes only because the invitation's reservation was
    // released before the seat check, not counted next to the new account.
    expect($reason)->toBe(CompanyAdministratorAccount::CREATED)
        ->and($invitation->isPending())->toBeFalse();
});

it('rejects a password that breaks the password rules before writing anything', function () {
    $superAdmin = multiCompanyAdminTestSuperAdmin();
    multiCompanyAdminTestAuthenticate($superAdmin, []);

    $company = multiCompanyAdminTestCompany();

    expect(fn () => companyAdministratorPasswordSet($company, $superAdmin, 'short@tenant-pw.test', 'short'))
        ->toThrow(ValidationException::class);

    expect(User::query()->where('email', 'short@tenant-pw.test')->exists())->toBeFalse();
});

it('gives an existing company an administrator from the edit page and clears the fields', function () {
    FilamentHelper::bootAdminPanel();

    $superAdmin = multiCompanyAdminTestSuperAdmin();
    $company = multiCompanyAdminTestCompany();
    multiCompanyAdminTestAuthenticate($superAdmin, [(int) $company->getKey()]);

    Livewire::test(EditCompany::class, ['record' => $company->getRouteKey()])
        ->fillForm([
            'administrator_email'    => 'edit-page@tenant-pw.test',
            'administrator_password' => 'Correct-horse-42',
        ])
        ->call('save')
        ->assertHasNoFormErrors()
        // Not company columns, so nothing refills them: left in place, the
        // next save would set the password again.
        ->assertSet('data.administrator_email', null)
        ->assertSet('data.administrator_password', null);

    $user = User::query()->where('email', 'edit-page@tenant-pw.test')->firstOrFail();

    expect($user->isCompanyAdmin((int) $company->getKey()))->toBeTrue()
        ->and(Hash::check('Correct-horse-42', $user->password))->toBeTrue();

    Mail::assertNothingSent();
});

it('invites from the edit page when only an email is given', function () {
    FilamentHelper::bootAdminPanel();

    $superAdmin = multiCompanyAdminTestSuperAdmin();
    $company = multiCompanyAdminTestCompany();
    multiCompanyAdminTestAuthenticate($superAdmin, [(int) $company->getKey()]);

    Livewire::test(EditCompany::class, ['record' => $company->getRouteKey()])
        ->fillForm(['administrator_email' => 'edit-invite@tenant-pw.test'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(Invitation::query()->where('email', 'edit-invite@tenant-pw.test')->exists())->toBeTrue()
        ->and(User::query()->where('email', 'edit-invite@tenant-pw.test')->exists())->toBeFalse();

    Mail::assertSent(UserInvitationMail::class);
});

it('does nothing about an administrator when the edit page is saved with the section blank', function () {
    // Create falls back to the company's address; edit must not, or every
    // ordinary save of a company would send its mailbox an invitation.
    FilamentHelper::bootAdminPanel();

    $superAdmin = multiCompanyAdminTestSuperAdmin();
    $company = multiCompanyAdminTestCompany();
    $company->forceFill(['email' => 'mailbox@tenant-pw.test'])->save();
    multiCompanyAdminTestAuthenticate($superAdmin, [(int) $company->getKey()]);

    Livewire::test(EditCompany::class, ['record' => $company->getRouteKey()])
        ->fillForm(['name' => 'Renamed Only'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(Invitation::query()->where('default_company_id', $company->getKey())->exists())->toBeFalse();

    Mail::assertNothingSent();
});
