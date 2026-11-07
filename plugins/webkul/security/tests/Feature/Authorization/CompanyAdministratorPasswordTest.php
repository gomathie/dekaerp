<?php

use Filament\Actions\Testing\TestAction;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Filament\Resources\CompanyResource\Pages\EditCompany;
use Webkul\Security\Filament\Resources\CompanyResource\RelationManagers\UsersRelationManager;
use Webkul\Security\Mail\UserInvitationMail;
use Webkul\Security\Models\Company as SecurityCompany;
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
 * form cannot reach a user who is not a member of this company, and the seat
 * and assignment rules apply exactly as on the Users page. Widened on
 * 2026-11-05 (user) from the administrator to every member, through the
 * company page's Users list.
 */
beforeEach(function (): void {
    app(CompanyAdminRoleProvisioner::class)->provision();

    settings(UserSettings::class)->enable_user_invitation = true;
    settings(UserSettings::class)->save();

    Mail::fake();
});

/**
 * A company the edit page can save as it stands.
 *
 * Explicit phone numbers, not the factory's: faker's phoneNumber() sometimes
 * produces a format (an extension, "x890") that the form's tel() rule rejects,
 * which failed a page test on a random run for a reason unrelated to it.
 */
function companyAdministratorPasswordPageCompany(): Company
{
    $company = multiCompanyAdminTestCompany();
    $company->forceFill(['phone' => '+260971234567', 'mobile' => '+260961234567'])->save();

    return $company;
}

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
    Mail::assertSent(UserInvitationMail::class);
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

it('refuses to change the password of a user who is not a member of this company', function () {
    // The account-takeover guard. Without it, whoever administers this company
    // could type another tenant's user's address and sign in as them.
    $superAdmin = multiCompanyAdminTestSuperAdmin();
    multiCompanyAdminTestAuthenticate($superAdmin, []);

    $company = multiCompanyAdminTestCompany();
    $otherTenant = multiCompanyAdminTestCompany();
    $outsider = multiCompanyAdminTestUser($otherTenant);
    $hashBefore = $outsider->password;

    $reason = companyAdministratorPasswordSet($company, $superAdmin, $outsider->email, 'Takeover-attempt-9');

    expect($reason)->toBe(CompanyAdministratorAccount::NOT_MEMBER)
        ->and($outsider->fresh()->password)->toBe($hashBefore)
        ->and($outsider->fresh()->isCompanyAdmin((int) $company->getKey()))->toBeFalse();

    // The same refusal from the Users list, whose row action names the user
    // directly instead of by address.
    expect(app(CompanyAdministratorAccount::class)->setPasswordFor($company, $superAdmin, $outsider, 'Takeover-attempt-9'))
        ->toBe(CompanyAdministratorAccount::NOT_MEMBER)
        ->and($outsider->fresh()->password)->toBe($hashBefore);
});

/**
 * Every user of the company, not only its administrator (user, 2026-11-05):
 * when invitation emails do not arrive, support sets each person's password.
 */
it('sets the password of an ordinary member of the company', function () {
    $superAdmin = multiCompanyAdminTestSuperAdmin();
    multiCompanyAdminTestAuthenticate($superAdmin, []);

    $company = multiCompanyAdminTestCompany();
    $member = multiCompanyAdminTestUser($company);

    expect($member->isCompanyAdmin((int) $company->getKey()))->toBeFalse('the member must not be an administrator, or this proves nothing new');

    $reason = app(CompanyAdministratorAccount::class)->setPasswordFor($company, $superAdmin, $member, 'Member-password-7');

    expect($reason)->toBe(CompanyAdministratorAccount::PASSWORD_RESET)
        ->and(Hash::check('Member-password-7', $member->fresh()->password))->toBeTrue()
        ->and($member->fresh()->must_change_password)->toBeTrue();

    Mail::assertNothingSent();
});

it('refuses a Multi-Company Admin resetting a member who also belongs to a company outside their assignment', function () {
    // Being a member of an assigned company is not enough: the same password
    // opens the other company too, which this staff member may not administer.
    $assigned = multiCompanyAdminTestCompany();
    $notAssigned = multiCompanyAdminTestCompany();
    $sharedMember = multiCompanyAdminTestUser([$assigned, $notAssigned]);
    $hashBefore = $sharedMember->password;

    $staff = multiCompanyAdminTestAdministrator($assigned);
    multiCompanyAdminTestAuthenticate($staff, [(int) $assigned->getKey()]);

    expect(app(CompanyAdministratorAccount::class)->setPasswordFor($assigned, $staff, $sharedMember, 'Member-password-7'))
        ->toBe(CompanyAdministratorAccount::NOT_ALLOWED)
        ->and($sharedMember->fresh()->password)->toBe($hashBefore);
});

it('lists the company’s users with their logins and sets a password from the list', function () {
    FilamentHelper::bootAdminPanel();

    $superAdmin = multiCompanyAdminTestSuperAdmin();
    $company = SecurityCompany::query()->findOrFail(companyAdministratorPasswordPageCompany()->getKey());
    $member = multiCompanyAdminTestUser($company);
    $outsider = multiCompanyAdminTestUser(multiCompanyAdminTestCompany());
    multiCompanyAdminTestAuthenticate($superAdmin, [(int) $company->getKey()]);

    Livewire::test(UsersRelationManager::class, ['ownerRecord' => $company, 'pageClass' => EditCompany::class])
        ->assertCanSeeTableRecords([$member])
        ->assertCanNotSeeTableRecords([$outsider])
        ->callAction(TestAction::make('setPassword')->table($member), data: [
            'password'             => 'From-the-list-3',
            'must_change_password' => true,
        ])
        ->assertHasNoFormErrors();

    expect(Hash::check('From-the-list-3', $member->fresh()->password))->toBeTrue();

    Mail::assertNothingSent();
});

it('shows the Users list only to installation staff', function () {
    // A company's own administrator manages people from the Users page, under
    // its policy; this list is DEKA support's.
    $company = SecurityCompany::query()->findOrFail(multiCompanyAdminTestCompany()->getKey());
    $tenantUser = multiCompanyAdminTestUser($company);
    multiCompanyAdminTestAuthenticate($tenantUser, [(int) $company->getKey()]);

    expect(UsersRelationManager::canViewForRecord($company, EditCompany::class))->toBeFalse();

    multiCompanyAdminTestAuthenticate(multiCompanyAdminTestSuperAdmin(), [(int) $company->getKey()]);

    expect(UsersRelationManager::canViewForRecord($company, EditCompany::class))->toBeTrue();
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
    $company = companyAdministratorPasswordPageCompany();
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
    $company = companyAdministratorPasswordPageCompany();
    multiCompanyAdminTestAuthenticate($superAdmin, [(int) $company->getKey()]);

    Livewire::test(EditCompany::class, ['record' => $company->getRouteKey()])
        ->fillForm(['administrator_email' => 'edit-invite@tenant-pw.test'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(Invitation::query()->where('email', 'edit-invite@tenant-pw.test')->exists())->toBeTrue()
        ->and(User::query()->where('email', 'edit-invite@tenant-pw.test')->exists())->toBeFalse();

    Mail::assertNothingSent();
});

it('does nothing about an administrator when the edit page is saved with the section blank', function () {
    // Create falls back to the company's address; edit must not, or every
    // ordinary save of a company would send its mailbox an invitation.
    FilamentHelper::bootAdminPanel();

    $superAdmin = multiCompanyAdminTestSuperAdmin();
    $company = companyAdministratorPasswordPageCompany();
    $company->forceFill(['email' => 'mailbox@tenant-pw.test'])->save();
    multiCompanyAdminTestAuthenticate($superAdmin, [(int) $company->getKey()]);

    Livewire::test(EditCompany::class, ['record' => $company->getRouteKey()])
        ->fillForm(['name' => 'Renamed Only'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(Invitation::query()->where('default_company_id', $company->getKey())->exists())->toBeFalse();

    Mail::assertNothingSent();
});
