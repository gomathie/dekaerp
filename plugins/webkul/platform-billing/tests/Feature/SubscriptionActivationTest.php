<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Webkul\PlatformBilling\Models\Plan;
use Webkul\PlatformBilling\Models\Price;
use Webkul\PlatformBilling\Services\SubscriptionDrafts;
use Webkul\PlatformBilling\Services\SubscriptionLifecycle;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/SecurityHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensureERPInstalled();
});

function billingSetup(): array
{
    $company = Company::factory()->create();
    $actor = User::query()->where('email', 'admin@example.com')->firstOrFail();
    $plan = Plan::query()->create(['name' => 'Starter', 'code' => 'starter-'.uniqid(), 'included_plugins' => [], 'seat_limit' => 3]);
    $price = Price::query()->create(['plan_id' => $plan->id, 'currency' => 'USD', 'interval_months' => 1, 'amount_minor' => 1000]);
    $drafts = app(SubscriptionDrafts::class);
    $anchor = new DateTimeImmutable('2026-01-01 00:00:00', new DateTimeZone('UTC'));

    return [$company, $actor, $plan, $price, $drafts->create($actor, $company, $price, $anchor)];
}

it('allows drafts alongside an active subscription but refuses a second active row in PostgreSQL', function () {
    [$company, $actor, , $price, $draft] = billingSetup();
    app(SubscriptionLifecycle::class)->activate($draft, $actor);
    $second = app(SubscriptionDrafts::class)->create($actor, $company, $price, new DateTimeImmutable('2026-01-01'));

    expect($second->status)->toBe('draft')
        ->and(fn () => DB::table('platform_billing_subscriptions')->where('id', $second->id)->update(['status' => 'active']))
        ->toThrow(QueryException::class);
});

it('activates a draft and sets its snapshotted seat limit', function () {
    [$company, $actor, $plan, , $draft] = billingSetup();
    $plan->update(['seat_limit' => 9]);
    $active = app(SubscriptionLifecycle::class)->activate($draft, $actor, new DateTimeImmutable('2026-01-01'));

    expect($active->status)->toBe('active')
        ->and($active->period_end_at->format('Y-m-d'))->toBe('2026-02-01')
        ->and($company->fresh()->seat_limit)->toBe(3);
});

it('keeps current price until the next period and retains access after cancellation until period end', function () {
    [$company, $actor, $plan, , $draft] = billingSetup();
    $service = app(SubscriptionLifecycle::class);
    $active = $service->activate($draft, $actor, new DateTimeImmutable('2026-01-01'));
    $next = Price::query()->create(['plan_id' => $plan->id, 'currency' => 'USD', 'interval_months' => 3, 'amount_minor' => 2400]);
    $service->schedulePrice($active, $next, $actor);
    expect($active->fresh()->amount_minor)->toBe(1000);
    $service->advance($active, $actor, new DateTimeImmutable('2026-02-01'));
    expect($active->fresh()->amount_minor)->toBe(2400);
    $service->cancelAtPeriodEnd($active, $actor);
    expect($service->isReadOnly($company, new DateTimeImmutable('2026-03-01')))->toBeFalse();
    $service->advance($active, $actor, new DateTimeImmutable('2026-05-01'));
    expect($service->isReadOnly($company, new DateTimeImmutable('2026-05-01')))->toBeTrue();
});

it('refuses tenant transitions even when a policy permission is granted', function () {
    [, , , , $draft] = billingSetup();
    $tenant = SecurityHelper::authenticateWithPermissions(['update_platform_billing_subscription']);

    expect(fn () => app(SubscriptionLifecycle::class)->activate($draft, $tenant))
        ->toThrow(AuthorizationException::class, 'Only a Super Admin may change subscriptions.');
});

it('refuses an early period transition and permits Super Admin reactivation', function () {
    [, $actor, , , $draft] = billingSetup();
    $service = app(SubscriptionLifecycle::class);
    $active = $service->activate($draft, $actor, new DateTimeImmutable('2026-01-01'));
    expect(fn () => $service->advance($active, $actor, new DateTimeImmutable('2026-01-15')))
        ->toThrow(ValidationException::class, 'The current billing period has not ended.');
    $service->cancelAtPeriodEnd($active, $actor);
    expect($service->reactivate($active, $actor)->cancel_at_period_end)->toBeFalse();
});

it('makes overdue subscriptions read only only after the ten day grace period', function () {
    [$company, $actor, , , $draft] = billingSetup();
    $service = app(SubscriptionLifecycle::class);
    $active = $service->activate($draft, $actor, new DateTimeImmutable('2026-01-01'));
    $service->recordOverdue($active, $actor, new DateTimeImmutable('2026-01-10'));

    expect($service->isReadOnly($company, new DateTimeImmutable('2026-01-19')))->toBeFalse()
        ->and($service->isReadOnly($company, new DateTimeImmutable('2026-01-20')))->toBeTrue();
});
