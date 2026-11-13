<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Webkul\PlatformBilling\Listeners\StartPlanAtOnboarding;
use Webkul\PlatformBilling\Models\Plan;
use Webkul\PlatformBilling\Models\Price;
use Webkul\PlatformBilling\Models\Subscription;
use Webkul\Security\Models\User;
use Webkul\Security\Services\CompanyAdminRoleProvisioner;
use Webkul\Security\Services\CompanyOnboarding;
use Webkul\Security\Settings\UserSettings;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

/**
 * CR-4 (comprehensive review plan): the plan is chosen on the create-company
 * form and starts in the same transaction as the company. Pinned: the plan's
 * seat limit applies at once, no plan means no subscription, and a test
 * company given a plan is refused and rolled back rather than half-created.
 */
beforeEach(function (): void {
    TestBootstrapHelper::ensureERPInstalled();
    app(CompanyAdminRoleProvisioner::class)->provision();
    settings(UserSettings::class)->enable_user_invitation = true;
    settings(UserSettings::class)->save();
    Mail::fake();
});

function onboardingSuperAdmin(): User
{
    $actor = User::query()->where('email', 'admin@example.com')->firstOrFail();
    Auth::shouldUse('web');
    Auth::guard('web')->login($actor);

    return $actor;
}

function onboardingPrice(): Price
{
    $plan = Plan::query()->create([
        'name'             => 'Onboarding plan',
        'code'             => 'onboarding-'.uniqid(),
        'included_plugins' => [],
        'seat_limit'       => 4,
    ]);

    return Price::query()->create([
        'plan_id'         => $plan->id,
        'currency'        => config('platform-billing.currency'),
        'interval_months' => 1,
        'amount_minor'    => 2500,
    ]);
}

it('starts the plan chosen on the form when the company is created', function () {
    $actor = onboardingSuperAdmin();
    $price = onboardingPrice();

    $company = app(CompanyOnboarding::class)->create(
        $actor,
        ['name' => 'Plan at creation', 'email' => 'plan@onboarding.test'],
        'owner@onboarding.test',
        options: ['onboarding_price_id' => $price->id],
    )['company'];

    $subscription = Subscription::withoutGlobalScopes()->where('company_id', $company->id)->sole();

    expect($subscription->status)->toBe('active')
        ->and((int) $subscription->price_id)->toBe((int) $price->id)
        ->and((int) Company::withoutGlobalScopes()->find($company->id)->seat_limit)->toBe(4);
});

it('creates no subscription when no plan is chosen', function () {
    $actor = onboardingSuperAdmin();

    $company = app(CompanyOnboarding::class)->create(
        $actor,
        ['name' => 'No plan yet', 'email' => 'none@onboarding.test'],
        'owner-none@onboarding.test',
    )['company'];

    expect(Subscription::withoutGlobalScopes()->where('company_id', $company->id)->exists())->toBeFalse();
});

it('refuses a plan on a test company and rolls the company back', function () {
    $actor = onboardingSuperAdmin();
    $price = onboardingPrice();

    expect(fn () => app(CompanyOnboarding::class)->create(
        $actor,
        ['name' => 'Test with plan', 'email' => 'test@onboarding.test', 'account_type' => 'test', 'test_days' => 14],
        'owner-test@onboarding.test',
        options: ['onboarding_price_id' => $price->id],
    ))->toThrow(ValidationException::class, StartPlanAtOnboarding::ONLY_COMMERCIAL);

    expect(Company::withoutGlobalScopes()->where('name', 'Test with plan')->exists())->toBeFalse();
});
