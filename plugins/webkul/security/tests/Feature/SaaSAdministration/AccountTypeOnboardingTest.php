<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Webkul\Security\Filament\Resources\CompanyResource\Pages\CreateCompany;
use Webkul\Security\Models\User;
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
});

it('leaves a company commercial when installation staff do not choose a type', function () {
    $actor = User::query()->where('email', 'admin@example.com')->firstOrFail();

    $company = app(CompanyOnboarding::class)->create($actor, [
        'name'  => 'Default commercial company',
        'email' => 'commercial-default@example.test',
    ], 'owner@commercial-default.test')['company'];

    expect($company->account_type)->toBe('commercial')
        ->and($company->test_ends_at)->toBeNull();
});

it('refuses a company administrator choosing a test account and rolls the company back', function () {
    $clerk = SecurityHelper::authenticateWithPermissions(['create_security_company']);
    $name = 'Tenant chosen trial';

    expect(fn () => app(CompanyOnboarding::class)->create($clerk, [
        'name'         => $name,
        'email'        => 'tenant-trial@example.test',
        'account_type' => 'test',
        'test_days'    => 14,
    ], 'owner@tenant-trial.test'))
        ->toThrow(AuthorizationException::class, 'Only installation staff may choose the account type.');

    expect(Company::withoutGlobalScopes()->where('name', $name)->exists())->toBeFalse();
});

it('refuses a test company with no day count and does not keep the company', function () {
    $actor = User::query()->where('email', 'admin@example.com')->firstOrFail();
    $name = 'Trial without days';

    expect(fn () => app(CompanyOnboarding::class)->create($actor, [
        'name'         => $name,
        'email'        => 'trial-without-days@example.test',
        'account_type' => 'test',
    ], 'owner@trial-without-days.test'))
        ->toThrow(ValidationException::class, 'A test company needs a number of days.');

    expect(Company::withoutGlobalScopes()->where('name', $name)->exists())->toBeFalse();
});

it('shows the account type only to installation staff and stores the trial length', function () {
    FilamentHelper::bootAdminPanel();
    $actor = User::query()->where('email', 'admin@example.com')->firstOrFail();
    Auth::shouldUse('web');
    Auth::guard('web')->login($actor);
    $currency = Currency::query()->where('active', true)->firstOrFail();

    Livewire::test(CreateCompany::class)
        ->assertFormFieldExists('account_type')
        ->fillForm([
            'name'                => 'Page trial company',
            'email'               => 'page-trial@tenant.test',
            'currency_id'         => $currency->id,
            'phone'               => '+260971234567',
            'mobile'              => '+260961234567',
            'administrator_email' => 'page-trial-owner@tenant.test',
            'account_type'        => 'test',
            'test_days'           => 14,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $company = Company::withoutGlobalScopes()->where('name', 'Page trial company')->firstOrFail();

    expect($company->account_type)->toBe('test')
        ->and($company->test_ends_at)->not->toBeNull()
        ->and($company->test_ends_at->greaterThan(now()->addDays(13)))->toBeTrue();
});
