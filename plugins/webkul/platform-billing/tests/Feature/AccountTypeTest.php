<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;
use Webkul\PlatformBilling\Models\Invoice;
use Webkul\PlatformBilling\Models\Plan;
use Webkul\PlatformBilling\Models\Price;
use Webkul\PlatformBilling\Models\Subscription;
use Webkul\PlatformBilling\Services\CompanyAccountType;
use Webkul\PlatformBilling\Services\CompanyReadOnlyWriteGuard;
use Webkul\PlatformBilling\Services\SubscriptionLifecycle;
use Webkul\Security\Models\User;
use Webkul\Security\Services\MultiCompanyAdminRoleProvisioner;
use Webkul\Support\Models\Calendar;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensureERPInstalled();
});

function trialCompany(string $endsAt): Company
{
    return Company::factory()->create([
        'account_type' => 'test',
        'test_ends_at' => new DateTimeImmutable($endsAt),
    ]);
}

function trialPrice(): Price
{
    $plan = Plan::query()->create([
        'name' => 'Trial switch plan',
        'code' => 'trial-'.uniqid(),
        'included_plugins' => [],
        'seat_limit' => 3,
    ]);

    return Price::query()->create([
        'plan_id' => $plan->id,
        'currency' => 'USD',
        'interval_months' => 1,
        'amount_minor' => 1000,
    ]);
}

it('makes an expired test company read-only and leaves an open trial writable', function () {
    $company = trialCompany('2026-11-01T00:00:00Z');
    $lifecycle = app(SubscriptionLifecycle::class);

    expect($lifecycle->isReadOnly($company, new DateTimeImmutable('2026-10-31T00:00:00Z')))->toBeFalse()
        ->and($lifecycle->isReadOnly($company, new DateTimeImmutable('2026-11-01T00:00:00Z')))->toBeTrue();

    $tenant = SecurityHelper::authenticateWithPermissions([]);

    expect(fn () => Calendar::query()->create([
        'name' => 'expired trial write',
        'timezone' => 'UTC',
        'company_id' => $company->getKey(),
    ]))->toThrow(ValidationException::class, CompanyReadOnlyWriteGuard::MESSAGE)
        ->and(Company::withoutGlobalScopes()->find($company->getKey()))->not->toBeNull()
        ->and($tenant->isSuperAdmin())->toBeFalse();
});

it('lets installation staff add days and refuses a company administrator', function () {
    $company = trialCompany('2026-11-01T00:00:00Z');
    $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();
    $service = app(CompanyAccountType::class);
    $at = new DateTimeImmutable('2026-11-09T00:00:00Z');

    $extended = $service->addDays($admin, $company, 10, $at);

    expect($extended->test_ends_at->format('Y-m-d'))->toBe('2026-11-19');

    $mca = User::withoutEvents(fn (): User => User::factory()->create([
        'default_company_id' => $company->getKey(),
        'is_active' => true,
        'is_default' => false,
    ]));
    $mca->allowedCompanies()->sync([$company->getKey()]);
    $mca->syncRoles([app(MultiCompanyAdminRoleProvisioner::class)->provision()->getKey()]);
    $mca->unsetRelation('roles');

    expect($service->addDays($mca, $company, 5, $at)->fresh()->test_ends_at->format('Y-m-d'))->toBe('2026-11-24');

    $stranger = Company::factory()->create(['account_type' => 'test', 'test_ends_at' => $at->modify('+2 days')]);

    expect(fn () => $service->addDays($mca, $stranger, 1, $at))
        ->toThrow(AuthorizationException::class, CompanyAccountType::ONLY_STAFF);

    $clerk = SecurityHelper::authenticateWithPermissions(['create_security_company']);

    expect(fn () => $service->addDays($clerk, $company, 1, $at))
        ->toThrow(AuthorizationException::class, CompanyAccountType::ONLY_STAFF);
});

it('switches a test company to commercial at the next period and refuses the reverse', function () {
    $company = trialCompany('2026-12-01T00:00:00Z');
    $actor = User::query()->where('email', 'admin@example.com')->firstOrFail();
    $at = new DateTimeImmutable('2026-11-09T00:00:00Z');
    $switched = app(CompanyAccountType::class)->switchToCommercial($actor, $company, trialPrice(), $at);
    $subscription = Subscription::withoutGlobalScopes()->where('company_id', $company->getKey())->first();

    expect($switched->account_type)->toBe('commercial')
        ->and($switched->test_ends_at)->toBeNull()
        ->and(Invoice::withoutGlobalScopes()->where('company_id', $company->getKey())->count())->toBe(0)
        ->and($subscription->next_invoice_at > $at)->toBeTrue();

    expect(fn () => app(CompanyAccountType::class)->switchToCommercial($actor, $switched, trialPrice(), $at))
        ->toThrow(ValidationException::class, CompanyAccountType::ONLY_TEST_SWITCH);
});
