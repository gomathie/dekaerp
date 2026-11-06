<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Webkul\PlatformBilling\Models\Plan;
use Webkul\PlatformBilling\Models\Price;
use Webkul\PlatformBilling\Models\Subscription;
use Webkul\PlatformBilling\Services\SubscriptionDrafts;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/SecurityHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensureERPInstalled();
});

it('keeps a company scoped immutable snapshot when a Super Admin drafts a subscription', function () {
    $company = Company::factory()->create();
    $actor = \Webkul\Security\Models\User::query()->where('email', 'admin@example.com')->firstOrFail();
    expect($actor->isSuperAdmin())->toBeTrue();

    $plan = Plan::query()->create(['name' => 'Growth', 'code' => 'growth', 'included_plugins' => ['products']]);
    $price = Price::query()->create(['plan_id' => $plan->id, 'currency' => 'USD', 'interval_months' => 3, 'amount_minor' => 12000]);
    $subscription = app(SubscriptionDrafts::class)->create($actor, $company, $price, new DateTimeImmutable('2026-01-31 12:00:00', new DateTimeZone('Africa/Nairobi')));

    $plan->update(['name' => 'Growth Plus', 'included_plugins' => ['products', 'sales']]);
    $price->update(['amount_minor' => 15000]);

    expect($subscription->fresh()->company_id)->toBe($company->id)
        ->and($subscription->fresh()->status)->toBe('draft')
        ->and($subscription->fresh()->plan_name)->toBe('Growth')
        ->and($subscription->fresh()->included_plugins)->toBe(['products'])
        ->and($subscription->fresh()->amount_minor)->toBe(12000)
        ->and($subscription->fresh()->timezone)->toBe('Africa/Nairobi')
        ->and($subscription->fresh()->next_invoice_at)->toBeNull();
});

it('refuses a tenant subscription mutation even with an unrelated permission', function () {
    $company = Company::factory()->create();
    $tenant = SecurityHelper::authenticateWithPermissions(['update_platform_billing_subscription']);
    $plan = Plan::query()->create(['name' => 'Basic', 'code' => 'basic', 'included_plugins' => []]);
    $price = Price::query()->create(['plan_id' => $plan->id, 'currency' => 'USD', 'interval_months' => 1, 'amount_minor' => 1000]);

    expect(Gate::forUser($tenant)->allows('create', Subscription::class))->toBeFalse()
        ->and(fn () => app(SubscriptionDrafts::class)->create($tenant, $company, $price, new DateTimeImmutable('2026-01-01')))
        ->toThrow(AuthorizationException::class);
});

it('enforces unique prices in the database', function () {
    $plan = Plan::query()->create(['name' => 'Basic', 'code' => 'basic', 'included_plugins' => []]);
    Price::query()->create(['plan_id' => $plan->id, 'currency' => 'USD', 'interval_months' => 1, 'amount_minor' => 1000]);

    expect(fn () => Price::query()->create(['plan_id' => $plan->id, 'currency' => 'USD', 'interval_months' => 1, 'amount_minor' => 1200]))
        ->toThrow(QueryException::class);
});

it('enforces supported intervals in the database', function () {
    $plan = Plan::query()->create(['name' => 'Basic', 'code' => 'basic', 'included_plugins' => []]);

    expect(fn () => DB::table('platform_billing_prices')->insert(['plan_id' => $plan->id, 'currency' => 'USD', 'interval_months' => 2, 'amount_minor' => 1000]))
        ->toThrow(QueryException::class);
});
