<?php

use Illuminate\Console\Scheduling\Schedule;
use Webkul\PlatformBilling\Models\Plan;
use Webkul\PlatformBilling\Models\Price;
use Webkul\PlatformBilling\Models\Subscription;
use Webkul\PlatformBilling\Services\SubscriptionDrafts;
use Webkul\PlatformBilling\Services\SubscriptionLifecycle;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensureERPInstalled();
});

/**
 * Billing health check (CR-10 item 3, 2026-11-13): it must report invoicing
 * that has stopped, pass when nothing is late, and write nothing.
 */
function billingHealthActiveSubscription(DateTimeImmutable $anchor): Subscription
{
    $company = Company::factory()->create();
    $actor = User::query()->where('email', 'admin@example.com')->firstOrFail();
    $plan = Plan::query()->create([
        'name'             => 'Health plan',
        'code'             => 'health-'.uniqid(),
        'included_plugins' => [],
        'seat_limit'       => 3,
    ]);
    $price = Price::query()->create([
        'plan_id'         => $plan->id,
        'currency'        => 'USD',
        'interval_months' => 1,
        'amount_minor'    => 10000,
    ]);

    $draft = app(SubscriptionDrafts::class)->create($actor, $company, $price, $anchor);

    return app(SubscriptionLifecycle::class)->activate($draft, $actor, $anchor);
}

it('passes when no invoice is late and balances reconcile', function () {
    Subscription::withoutGlobalScopes()->where('status', 'active')
        ->where('next_invoice_at', '<', now()->subDay())
        ->update(['next_invoice_at' => now()->addMonth()]);

    $this->artisan('platform-billing:health-check')
        ->expectsOutputToContain('Billing is healthy')
        ->assertSuccessful();
});

it('fails and names the subscription whose invoice is more than a day overdue', function () {
    $subscription = billingHealthActiveSubscription(new DateTimeImmutable('2026-01-01T00:00:00Z'));
    $before = $subscription->fresh()->next_invoice_at;

    $this->artisan('platform-billing:health-check')
        ->expectsOutputToContain('subscription '.$subscription->getKey().' was due')
        ->assertFailed();

    // Read-only: the check must not move the schedule it is reporting on.
    expect($subscription->fresh()->next_invoice_at->equalTo($before))->toBeTrue();
});

it('schedules the health check once a day after the invoice run', function () {
    $events = collect(app(Schedule::class)->events())
        ->filter(fn ($event): bool => str_contains((string) $event->command, 'platform-billing:health-check'));

    expect($events)->toHaveCount(1)
        ->and($events->first()->expression)->toBe('0 6 * * *');
});
