<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Webkul\Security\Models\User;
use Webkul\Security\Services\CompanyAdministratorAccount;
use Webkul\Security\Services\CompanyAdminPasswordVault;
use Webkul\Security\Services\CompanyAdminRoleProvisioner;
use Webkul\Security\Settings\UserSettings;

require_once __DIR__.'/../Authorization/MultiCompanyAdminTest.php';

/**
 * Company administrators' passwords are readable by a Super Admin (user,
 * 2026-11-10, chosen over show-once and reset-only after the risk was
 * explained). Pinned here: only Company Admins are stored, the copy follows
 * every change, only an active Super Admin who re-enters their own password
 * may read it, and the audit trail never carries the password.
 */
beforeEach(function (): void {
    app(CompanyAdminRoleProvisioner::class)->provision();

    settings(UserSettings::class)->enable_user_invitation = true;
    settings(UserSettings::class)->save();

    Mail::fake();
});

const VAULT_ACTOR_PASSWORD = 'Super-Admin-Pass-91';

function vaultSuperAdmin(): User
{
    $admin = multiCompanyAdminTestSuperAdmin();
    $admin->forceFill(['password' => Hash::make(VAULT_ACTOR_PASSWORD)])->saveQuietly();

    return $admin->fresh();
}

function vaultRevealFailure(Closure $call): string
{
    try {
        $call();
    } catch (ValidationException $exception) {
        return (string) collect($exception->errors())->flatten()->first();
    } catch (AuthorizationException $exception) {
        return $exception->getMessage();
    }

    return 'no refusal';
}

it('lets a super admin read back the password a company administrator was created with', function () {
    $superAdmin = vaultSuperAdmin();
    multiCompanyAdminTestAuthenticate($superAdmin, []);
    $company = multiCompanyAdminTestCompany();

    $reason = app(CompanyAdministratorAccount::class)
        ->setPassword($company, $superAdmin, 'kofi.admin@vault.test', 'Created-Pass-42');

    $admin = User::query()->where('email', 'kofi.admin@vault.test')->firstOrFail();

    Log::spy();

    $revealed = app(CompanyAdminPasswordVault::class)
        ->reveal($superAdmin, $admin, VAULT_ACTOR_PASSWORD, (int) $company->getKey());

    expect($reason)->toBe(CompanyAdministratorAccount::CREATED)
        ->and($revealed)->toBe('Created-Pass-42')
        ->and(DB::table('security_user_password_vault')->where('user_id', $admin->getKey())->value('encrypted_password'))
        ->not->toBe('Created-Pass-42');

    Log::shouldHaveReceived('notice')->withArgs(function (string $message, array $context): bool {
        return ($context['action'] ?? null) === 'security.user.password_revealed'
            && ! str_contains(json_encode($context), 'Created-Pass-42');
    })->once();
});

it('follows a company administrator who changes their own password', function () {
    $superAdmin = vaultSuperAdmin();
    multiCompanyAdminTestAuthenticate($superAdmin, []);
    $company = multiCompanyAdminTestCompany();

    app(CompanyAdministratorAccount::class)->setPassword($company, $superAdmin, 'ama.admin@vault.test', 'First-Pass-42');
    $admin = User::query()->where('email', 'ama.admin@vault.test')->firstOrFail();

    $admin->forceFill(['password' => 'Changed-By-Owner-77'])->save();

    expect(app(CompanyAdminPasswordVault::class)->reveal($superAdmin, $admin, VAULT_ACTOR_PASSWORD))
        ->toBe('Changed-By-Owner-77');
});

it('never stores the password of a user who is not a company administrator', function () {
    $company = multiCompanyAdminTestCompany();
    $user = multiCompanyAdminTestUser($company);

    $user->forceFill(['password' => 'Ordinary-User-42'])->save();

    expect(app(CompanyAdminPasswordVault::class)->has($user))->toBeFalse();
});

it('refuses anyone but an active super admin, and a wrong own password', function () {
    $superAdmin = vaultSuperAdmin();
    multiCompanyAdminTestAuthenticate($superAdmin, []);
    $company = multiCompanyAdminTestCompany();

    app(CompanyAdministratorAccount::class)->setPassword($company, $superAdmin, 'yaw.admin@vault.test', 'Guarded-Pass-42');
    $admin = User::query()->where('email', 'yaw.admin@vault.test')->firstOrFail();

    $multiCompanyAdmin = multiCompanyAdminTestAdministrator($company, attributes: ['password' => Hash::make(VAULT_ACTOR_PASSWORD)]);
    $vault = app(CompanyAdminPasswordVault::class);

    expect(vaultRevealFailure(fn () => $vault->reveal($multiCompanyAdmin, $admin, VAULT_ACTOR_PASSWORD)))
        ->toBe(CompanyAdminPasswordVault::ONLY_SUPER_ADMIN)
        ->and(vaultRevealFailure(fn () => $vault->reveal($admin, $admin, 'Guarded-Pass-42')))
        ->toBe(CompanyAdminPasswordVault::ONLY_SUPER_ADMIN)
        ->and(vaultRevealFailure(fn () => $vault->reveal($superAdmin, $admin, 'wrong-password')))
        ->toBe(CompanyAdminPasswordVault::WRONG_PASSWORD);
});
