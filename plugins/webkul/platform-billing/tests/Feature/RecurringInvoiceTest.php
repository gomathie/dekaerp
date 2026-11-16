<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\QueryException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Webkul\PlatformBilling\Jobs\GenerateSubscriptionInvoice;
use Webkul\PlatformBilling\Models\Invoice;
use Webkul\PlatformBilling\Models\Plan;
use Webkul\PlatformBilling\Models\Price;
use Webkul\PlatformBilling\Services\DiscountCodeService;
use Webkul\PlatformBilling\Services\RecurringInvoiceGenerator;
use Webkul\PlatformBilling\Services\SubscriptionDrafts;
use Webkul\PlatformBilling\Services\SubscriptionLifecycle;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Services\CompanyContext;

require_once __DIR__.'/../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensureERPInstalled();
    config([
        'platform-billing.issuer.name'        => 'DEKA',
        'platform-billing.issuer.tax_rate'    => '15.0000',
        'platform-billing.issuer.tax_enabled' => true,
    ]);
});

function invoiceBillingFixture(int $amount = 10000): array
{
    $company = Company::factory()->create();
    $actor = User::query()->where('email', 'admin@example.com')->firstOrFail();
    $plan = Plan::query()->create([
        'name'             => 'Invoice plan',
        'code'             => 'invoice-'.uniqid(),
        'included_plugins' => [],
        'seat_limit'       => 3,
    ]);
    $price = Price::query()->create([
        'plan_id'         => $plan->id,
        'currency'        => 'USD',
        'interval_months' => 1,
        'amount_minor'    => $amount,
    ]);
    $draft = app(SubscriptionDrafts::class)->create(
        $actor,
        $company,
        $price,
        new DateTimeImmutable('2026-01-01T00:00:00Z'),
    );
    $active = app(SubscriptionLifecycle::class)->activate(
        $draft,
        $actor,
        new DateTimeImmutable('2026-01-01T00:00:00Z'),
    );

    return [$company, $actor, $plan, $price, $active];
}

function invoiceAt(string $instant = '2026-02-01T00:00:00Z'): DateTimeImmutable
{
    return new DateTimeImmutable($instant);
}

it('issues one invoice per billing period and keeps the number when the run is repeated', function () {
    [, , , , $active] = invoiceBillingFixture();
    $generator = app(RecurringInvoiceGenerator::class);
    $at = invoiceAt();

    $first = $generator->generateDue((int) $active->getKey(), $at);
    $second = $generator->generateDue((int) $active->getKey(), $at);
    $invoices = Invoice::withoutGlobalScopes()->where('subscription_id', $active->getKey())->orderBy('id')->get();
    $invoice = $invoices->first();
    $duplicate = $invoice->getRawOriginal();
    unset($duplicate['id']);
    $duplicate['number'] = 'DEKA-INV-226-000099';
    $duplicate['sequence'] = 99;

    expect($first)->toContain('created')
        ->and($second)->toContain('not-due')
        ->and($invoices)->toHaveCount(1)
        ->and($invoice->number)->toBe('DEKA-INV-226-000001')
        ->and($invoice->due_at->format('Y-m-d'))->toBe('2026-02-08')
        ->and($invoice->grace_starts_at->format('Y-m-d'))->toBe('2026-02-09')
        ->and($active->fresh()->period_start_at->format('Y-m-d'))->toBe('2026-02-01')
        ->and(fn () => DB::transaction(fn () => DB::table('platform_billing_invoices')->insert($duplicate)))
        ->toThrow(UniqueConstraintViolationException::class);
});

it('does not consume an invoice number when posting fails', function () {
    [, , , , $active] = invoiceBillingFixture();
    config(['platform-billing.issuer.tax_rate' => 'not-a-rate']);

    expect(fn () => app(RecurringInvoiceGenerator::class)->generateDue((int) $active->getKey(), invoiceAt()))
        ->toThrow(InvalidArgumentException::class);

    config(['platform-billing.issuer.tax_rate' => '15.0000']);
    app(RecurringInvoiceGenerator::class)->generateDue((int) $active->getKey(), invoiceAt());

    expect(Invoice::withoutGlobalScopes()->where('subscription_id', $active->getKey())->value('number'))
        ->toBe('DEKA-INV-226-000001')
        ->and($active->fresh()->period_start_at->format('Y-m-d'))->toBe('2026-02-01');
});

it('snapshots issuer, VAT and a pre-tax discount and ignores a later configuration change', function () {
    [$company, $actor, , , $active] = invoiceBillingFixture();
    config([
        'platform-billing.issuer.name'        => 'DEKA Labs',
        'platform-billing.issuer.tax_enabled' => true,
    ]);
    $code = app(DiscountCodeService::class)->create($actor, [
        'code'             => 'SNAP-'.strtoupper(fake()->bothify('????')),
        'discount_type'    => 'percentage',
        'discount_value'   => 1500,
        'duration_type'    => 'forever',
        'duration_months'  => null,
        'eligibility_type' => 'any_company',
        'company_ids'      => [],
    ]);
    app(DiscountCodeService::class)->redeem($code->code, $company, $actor, invoiceAt('2026-01-15T00:00:00Z'));

    app(RecurringInvoiceGenerator::class)->generateDue((int) $active->getKey(), invoiceAt());
    config([
        'platform-billing.issuer.name'        => 'Changed Issuer',
        'platform-billing.issuer.tax_rate'    => '0.0000',
        'platform-billing.issuer.tax_enabled' => false,
    ]);
    $invoice = Invoice::withoutGlobalScopes()->where('subscription_id', $active->getKey())->first();

    expect($invoice->issuer_name)->toBe('DEKA Labs')
        ->and($invoice->tax_rate)->toBe('15.0000')
        ->and($invoice->tax_enabled)->toBeTrue()
        ->and($invoice->subtotal_minor)->toBe(10000)
        ->and($invoice->discount_minor)->toBe(1500)
        ->and($invoice->tax_minor)->toBe(1275)
        ->and($invoice->total_minor)->toBe(9775)
        ->and($invoice->discount_code)->toBe($code->code)
        ->and($invoice->lines()->withoutGlobalScopes()->value('amount_minor'))->toBe(10000)
        ->and(fn () => DB::transaction(fn () => DB::table('platform_billing_invoices')->where('id', $invoice->id)->update(['total_minor' => 1])))
        ->toThrow(QueryException::class, 'Posted platform invoices are immutable.');

    $tenant = SecurityHelper::authenticateWithPermissions(['view_any_platform_billing_invoice']);
    expect(Gate::forUser($tenant)->allows('viewAny', Invoice::class))->toBeFalse();
});

it('applies a fixed discount before VAT', function () {
    [$company, $actor, , , $active] = invoiceBillingFixture();
    $code = app(DiscountCodeService::class)->create($actor, [
        'code'             => 'FIX-'.strtoupper(fake()->bothify('????')),
        'discount_type'    => 'fixed_usd',
        'discount_value'   => 2500,
        'duration_type'    => 'forever',
        'duration_months'  => null,
        'eligibility_type' => 'any_company',
        'company_ids'      => [],
    ]);
    app(DiscountCodeService::class)->redeem($code->code, $company, $actor);

    app(RecurringInvoiceGenerator::class)->generateDue((int) $active->getKey(), invoiceAt());
    $invoice = Invoice::withoutGlobalScopes()->where('subscription_id', $active->getKey())->first();

    expect($invoice->discount_minor)->toBe(2500)
        ->and($invoice->tax_minor)->toBe(1125)
        ->and($invoice->total_minor)->toBe(8625);
});

it('waives the next N invoices for a free-month redemption and then charges the full period', function () {
    [$company, $actor, , , $active] = invoiceBillingFixture();
    $code = app(DiscountCodeService::class)->create($actor, [
        'code'             => 'FREE-'.strtoupper(fake()->bothify('????')),
        'discount_type'    => 'free_months',
        'discount_value'   => 1,
        'duration_type'    => 'forever',
        'duration_months'  => null,
        'eligibility_type' => 'any_company',
        'company_ids'      => [],
    ]);
    app(DiscountCodeService::class)->redeem($code->code, $company, $actor, invoiceAt('2026-01-02T00:00:00Z'));
    $generator = app(RecurringInvoiceGenerator::class);

    $generator->generateDue((int) $active->getKey(), invoiceAt('2026-02-01T00:00:00Z'));
    $generator->generateDue((int) $active->getKey(), invoiceAt('2026-03-01T00:00:00Z'));
    $invoices = Invoice::withoutGlobalScopes()->where('subscription_id', $active->getKey())->orderBy('period_start_at')->get();

    expect($invoices)->toHaveCount(2)
        ->and($invoices[0]->free_month_applied)->toBeTrue()
        ->and($invoices[0]->discount_minor)->toBe(10000)
        ->and($invoices[0]->tax_minor)->toBe(0)
        ->and($invoices[0]->total_minor)->toBe(0)
        ->and($invoices[1]->free_month_applied)->toBeFalse()
        ->and($invoices[1]->discount_minor)->toBe(0)
        ->and($invoices[1]->total_minor)->toBe(11500);
});

it('releases a one-invoice discount after the invoice that uses it', function () {
    [$company, $actor, , , $active] = invoiceBillingFixture();
    $code = app(DiscountCodeService::class)->create($actor, [
        'code'             => 'ONCE-'.strtoupper(fake()->bothify('????')),
        'discount_type'    => 'percentage',
        'discount_value'   => 1000,
        'duration_type'    => 'one_invoice',
        'duration_months'  => null,
        'eligibility_type' => 'any_company',
        'company_ids'      => [],
    ]);
    app(DiscountCodeService::class)->redeem($code->code, $company, $actor);
    $generator = app(RecurringInvoiceGenerator::class);

    $generator->generateDue((int) $active->getKey(), invoiceAt('2026-02-01T00:00:00Z'));

    expect(DB::table('platform_billing_company_discount_slots')->where('company_id', $company->getKey())->exists())->toBeFalse();

    $generator->generateDue((int) $active->getKey(), invoiceAt('2026-03-01T00:00:00Z'));
    $invoices = Invoice::withoutGlobalScopes()->where('subscription_id', $active->getKey())->orderBy('period_start_at')->get();

    expect($invoices[0]->discount_minor)->toBe(1000)
        ->and($invoices[1]->discount_minor)->toBe(0)
        ->and($invoices[1]->discount_redemption_id)->toBeNull();
});

it('invoices the elapsed period at the old price when a plan change is scheduled', function () {
    [$company, $actor, $plan, , $active] = invoiceBillingFixture(1000);
    // A plan may have one USD price per interval, so the next period is a
    // different interval. The invoice still covers the elapsed month.
    $next = Price::query()->create([
        'plan_id'         => $plan->id,
        'currency'        => 'USD',
        'interval_months' => 3,
        'amount_minor'    => 2400,
    ]);
    app(SubscriptionLifecycle::class)->schedulePrice($active, $next, $actor);
    $next->update(['amount_minor' => 9900]);

    app(RecurringInvoiceGenerator::class)->generateDue((int) $active->getKey(), invoiceAt());
    $invoice = Invoice::withoutGlobalScopes()->where('subscription_id', $active->getKey())->first();

    expect($invoice->subtotal_minor)->toBe(1000)
        ->and($invoice->total_minor)->toBe(1150)
        ->and($active->fresh()->amount_minor)->toBe(2400)
        ->and($company->fresh()->seat_limit)->toBe(3);
});

it('catches up one period at a time and does not skip when the run is late', function () {
    [, , , , $active] = invoiceBillingFixture();

    app(RecurringInvoiceGenerator::class)->generateDue((int) $active->getKey(), invoiceAt('2026-05-01T00:00:00Z'));
    $numbers = Invoice::withoutGlobalScopes()->where('subscription_id', $active->getKey())->orderBy('sequence')->pluck('number')->all();

    $issued = Invoice::withoutGlobalScopes()->where('subscription_id', $active->getKey())->orderBy('sequence')->pluck('issued_at');

    expect($numbers)->toBe([
        'DEKA-INV-226-000001',
        'DEKA-INV-226-000002',
        'DEKA-INV-226-000003',
        'DEKA-INV-226-000004',
    ])->and($issued->map(fn ($at): string => $at->format('Y-m-d'))->all())->toBe([
        '2026-02-01',
        '2026-03-01',
        '2026-04-01',
        '2026-05-01',
    ])->and($active->fresh()->period_start_at->format('Y-m-d'))->toBe('2026-05-01');
});

it('stops after the catch-up cap while the subscription is still due', function () {
    [, , , , $active] = invoiceBillingFixture();

    app(RecurringInvoiceGenerator::class)->generateDue((int) $active->getKey(), invoiceAt('2026-05-01T00:00:00Z'), 2);

    expect(Invoice::withoutGlobalScopes()->where('subscription_id', $active->getKey())->count())->toBe(2)
        ->and($active->fresh()->next_invoice_at->format('Y-m-d'))->toBe('2026-04-01');
});

it('files the invoice under the subscription company when another company is selected', function () {
    [$company, , , , $active] = invoiceBillingFixture();
    $other = Company::factory()->create();
    session([CompanyContext::SESSION_KEY => [$other->getKey()]]);
    app()->forgetInstance(CompanyContext::class);

    (new GenerateSubscriptionInvoice((int) $active->getKey(), '2026-02-01T00:00:00Z'))
        ->handle(app(RecurringInvoiceGenerator::class));
    $invoice = Invoice::withoutGlobalScopes()->where('subscription_id', $active->getKey())->first();

    expect($invoice->company_id)->toBe($company->getKey())
        ->and($invoice->lines()->withoutGlobalScopes()->value('company_id'))->toBe($company->getKey());
});

it('marks the subscription overdue on day eight so the existing grace rule can turn it read-only', function () {
    [$company, , , , $active] = invoiceBillingFixture();
    $generator = app(RecurringInvoiceGenerator::class);
    $lifecycle = app(SubscriptionLifecycle::class);

    $generator->generateDue((int) $active->getKey(), invoiceAt('2026-02-01T00:00:00Z'));
    $generator->recordOverdueIfDue((int) $active->getKey(), invoiceAt('2026-03-01T00:00:00Z'));

    expect($active->fresh()->overdue_at->format('Y-m-d'))->toBe('2026-02-09')
        ->and($lifecycle->isReadOnly($company, invoiceAt('2026-02-18T00:00:00Z')))->toBeFalse()
        ->and($lifecycle->isReadOnly($company, invoiceAt('2026-02-19T00:00:00Z')))->toBeTrue();

    $generator->recordOverdueIfDue((int) $active->getKey(), invoiceAt('2026-04-01T00:00:00Z'));

    expect($active->fresh()->overdue_at->format('Y-m-d'))->toBe('2026-02-09');
});

it('posts the final invoice and expires a cancelled subscription because billing rows are exempt', function () {
    // D-SAAS-12 exempts Invoice and Subscription from the read-only guard, so
    // the job can close the period. This used to stay read-only-blocked, with
    // no invoice and the subscription still active.
    [, $actor, , , $active] = invoiceBillingFixture();
    app(SubscriptionLifecycle::class)->cancelAtPeriodEnd($active, $actor);

    $outcomes = app(RecurringInvoiceGenerator::class)->generateDue((int) $active->getKey(), invoiceAt());

    expect($outcomes)->toContain('created')
        ->and(Invoice::withoutGlobalScopes()->where('subscription_id', $active->getKey())->count())->toBe(1)
        ->and($active->fresh()->status)->toBe('expired')
        ->and($active->fresh()->cancel_at_period_end)->toBeTrue();
});

it('queues due subscriptions and leaves a dry run unposted', function () {
    [, , , , $active] = invoiceBillingFixture();
    Bus::fake();

    $this->artisan('platform-billing:invoice-subscriptions', [
        '--dry-run' => true,
        '--at'      => '2026-02-01T00:00:00Z',
    ])->assertSuccessful();

    expect(Invoice::withoutGlobalScopes()->where('subscription_id', $active->getKey())->count())->toBe(0);
    Bus::assertNothingDispatched();

    $this->artisan('platform-billing:invoice-subscriptions', [
        '--at' => '2026-02-01T00:00:00Z',
    ])->assertSuccessful();

    Bus::assertDispatched(GenerateSubscriptionInvoice::class, function (GenerateSubscriptionInvoice $job) use ($active): bool {
        return $job->subscriptionId === (int) $active->getKey()
            && $job->asOf === '2026-02-01T00:00:00+00:00';
    });

    // This assertion sits after the other tests, and each test builds its own
    // application. A provider flag that remembers only the first application
    // leaves this scheduler empty.
    $events = collect(app(Schedule::class)->events())
        ->filter(fn ($event): bool => str_contains((string) $event->command, 'platform-billing:invoice-subscriptions'));

    expect($events)->toHaveCount(1);
});
