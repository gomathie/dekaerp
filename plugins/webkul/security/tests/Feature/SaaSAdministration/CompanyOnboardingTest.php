<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Webkul\Security\Filament\Resources\CompanyResource\Pages\CreateCompany;
use Webkul\Security\Mail\UserInvitationMail;
use Webkul\Security\Models\Invitation;
use Webkul\Security\Models\User;
use Webkul\Security\Services\CompanyAdministratorInviter;
use Webkul\Security\Services\CompanyAdminRoleProvisioner;
use Webkul\Security\Services\CompanyOnboarding;
use Webkul\Security\Settings\UserSettings;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensureERPInstalled();
    app(CompanyAdminRoleProvisioner::class)->provision();
    settings(UserSettings::class)->enable_user_invitation = true;
    settings(UserSettings::class)->save();
    Mail::fake();
});

it('creates a company and its administrator invitation in one transaction without sending mail before commit', function () {
    $actor = User::query()->where('email', 'admin@example.com')->firstOrFail();

    $result = app(CompanyOnboarding::class)->create($actor, [
        'name'   => 'Atomic SaaS company',
        'email'  => 'atomic@example.test',
        'phone'  => '+260971234567',
        'mobile' => '+260961234567',
    ], 'owner@atomic.test');

    $company = $result['company'];
    $invitation = Invitation::query()->where('default_company_id', $company->id)->firstOrFail();

    expect($result['outcome'])->toBe(CompanyAdministratorInviter::INVITED)
        ->and(DB::table('user_allowed_companies')->where('company_id', $company->id)->where('user_id', $actor->id)->exists())->toBeTrue()
        ->and($invitation->email)->toBe('owner@atomic.test');
    Mail::assertSent(UserInvitationMail::class);
});

it('rolls back a company and partner when its administrator invitation is refused', function () {
    $actor = User::query()->where('email', 'admin@example.com')->firstOrFail();
    $name = 'Refused atomic company';

    expect(fn () => app(CompanyOnboarding::class)->create($actor, [
        'name'  => $name,
        'email' => 'refused-atomic@example.test',
    ], $actor->email))
        ->toThrow(ValidationException::class, 'Company onboarding could not create the administrator invitation.');

    expect(Company::withoutGlobalScopes()->where('name', $name)->exists())->toBeFalse();
});

it('refuses an unassigned actor copying a pending invitation link', function () {
    $actor = User::query()->where('email', 'admin@example.com')->firstOrFail();
    $company = Company::factory()->create();
    $invitation = Invitation::query()->create([
        'email'              => 'recovery@tenant.test',
        'default_company_id' => $company->id,
        'company_ids'        => [$company->id],
        'role_ids'           => [],
        'expires_at'         => now()->addDay(),
    ]);
    $tenant = SecurityHelper::authenticateWithPermissions(['view_support_company']);

    expect(fn () => app(CompanyAdministratorInviter::class)->pendingLink($company, $tenant, $invitation->id))
        ->toThrow(AuthorizationException::class, 'You may not copy invitation links for this company.')
        ->and(app(CompanyAdministratorInviter::class)->pendingLink($company, $actor, $invitation->id))
        ->toContain('signature=');
});

it('audits a mail failure after commit while keeping the invitation link recoverable', function () {
    $actor = User::query()->where('email', 'admin@example.com')->firstOrFail();
    $company = Company::factory()->create(['email' => 'failed-mail@tenant.test']);
    Mail::shouldReceive('to')->once()->andThrow(new RuntimeException('smtp down'));
    Log::spy();

    expect(DB::transaction(fn () => app(CompanyAdministratorInviter::class)->invite($company, $actor)))
        ->toBe(CompanyAdministratorInviter::INVITED);

    $invitation = Invitation::query()->where('email', 'failed-mail@tenant.test')->firstOrFail();
    expect(app(CompanyAdministratorInviter::class)->pendingLink($company, $actor, $invitation->id))->toContain('signature=');
    Log::shouldHaveReceived('notice')->withArgs(fn ($message, $context): bool => $context['action'] === 'security.user.invitation_mail_failed');
});

it('does not send an invitation until the outer application transaction commits', function () {
    $actor = User::query()->where('email', 'admin@example.com')->firstOrFail();
    $company = Company::factory()->create(['email' => 'deferred@tenant.test']);

    DB::transaction(function () use ($company, $actor): void {
        app(CompanyAdministratorInviter::class)->invite($company, $actor);
        Mail::assertNothingSent();
    });

    Mail::assertSent(UserInvitationMail::class);
});

it('creates the company and administrator invitation through the Filament page', function () {
    FilamentHelper::bootAdminPanel();
    $actor = User::query()->where('email', 'admin@example.com')->firstOrFail();
    Auth::shouldUse('web');
    Auth::guard('web')->login($actor);
    $currency = Currency::query()->where('active', true)->firstOrFail();

    Livewire::test(CreateCompany::class)
        ->fillForm([
            'name'                => 'Page Onboarding Company',
            'email'               => 'page-company@tenant.test',
            'currency_id'         => $currency->id,
            'phone'               => '+260971234567',
            'mobile'              => '+260961234567',
            'administrator_email' => 'page-owner@tenant.test',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $company = Company::withoutGlobalScopes()->where('name', 'Page Onboarding Company')->firstOrFail();
    expect(Invitation::query()->where('default_company_id', $company->id)->where('email', 'page-owner@tenant.test')->exists())->toBeTrue();
});
