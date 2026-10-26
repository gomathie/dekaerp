<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Webkul\Security\Models\Invitation;
use Webkul\Security\Models\User;
use Webkul\Security\Services\CompanyAdminRoleProvisioner;
use Webkul\Security\Services\CompanyAdministratorInviter;
use Webkul\Security\Settings\UserSettings;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

/**
 * SAAS-00 characterization: pins TODAY'S behaviour, it does not endorse it.
 *
 * A new company reaches its administrator through exactly one code path - the
 * administration panel's create page, which calls `CompanyAdministratorInviter`
 * in `afterCreate()`. Creating a company any other way (model, factory, seeder,
 * a future API) produces a tenant with no administrator, no invitation and no
 * member, and nothing logs or reports that. ADM-03 records the same finding;
 * SAAS-07 owns the single onboarding coordinator that closes it, and must
 * invert this expectation rather than preserve it.
 */
beforeEach(function (): void {
    Auth::forgetGuards();
    app(CompanyAdminRoleProvisioner::class)->provision();

    settings(UserSettings::class)->enable_user_invitation = true;
    settings(UserSettings::class)->save();

    Mail::fake();
});

afterEach(function (): void {
    Auth::forgetGuards();
});

it('creates a company with no administrator, invitation or member outside the create page', function () {
    $superAdmin = User::query()->where('email', 'admin@example.com')->firstOrFail();

    Auth::forgetGuards();
    Auth::shouldUse('web');
    Auth::guard('web')->login($superAdmin);

    $company = Company::factory()->create([
        'name'      => 'SaaS Characterization Tenant',
        'email'     => 'admin@characterized-tenant.test',
        'is_active' => true,
    ]);

    // Preconditions: the company really exists, the actor is a real signed-in
    // administrator, and the invitation mechanism is switched on - so a zero
    // below means "nobody invoked it", not "invitations are disabled".
    expect(Company::query()->whereKey($company->getKey())->exists())->toBeTrue()
        ->and($superAdmin->exists)->toBeTrue()
        ->and(settings(UserSettings::class)->enable_user_invitation)->toBeTrue()
        ->and(CompanyAdministratorInviter::companyAdminRoleId())->not->toBeNull();

    expect(Invitation::query()->count())->toBe(0)
        ->and(DB::table('user_allowed_companies')->where('company_id', $company->getKey())->count())->toBe(0);

    // The same actor and the same company do produce an administrator when the
    // inviter is actually called, which is what the create page does. Without
    // this control the assertions above would pass even if invitations were
    // simply broken.
    $reason = app(CompanyAdministratorInviter::class)->invite($company, $superAdmin);

    $invitation = Invitation::query()->firstOrFail();

    expect($reason)->toBe(CompanyAdministratorInviter::INVITED)
        ->and((int) $invitation->default_company_id)->toBe((int) $company->getKey())
        ->and(DB::table('user_allowed_companies')->where('company_id', $company->getKey())->count())->toBe(0);
});
