<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Webkul\PlatformBilling\Models\Invoice;
use Webkul\PlatformBilling\Models\Payment;
use Webkul\PlatformBilling\Models\Plan;
use Webkul\PlatformBilling\Models\Price;
use Webkul\PlatformBilling\Services\PlatformPayments;
use Webkul\PlatformBilling\Services\RecurringInvoiceGenerator;
use Webkul\PlatformBilling\Services\SubscriptionDrafts;
use Webkul\PlatformBilling\Services\SubscriptionLifecycle;
use Webkul\Security\Models\User;
use Webkul\Security\Services\MultiCompanyAdminRoleProvisioner;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensureERPInstalled();
    config([
        'platform-billing.issuer.name' => 'DEKA',
        'platform-billing.issuer.tax_rate' => '15.0000',
        'platform-billing.issuer.tax_enabled' => true,
    ]);
});

function paymentFixture(): array
{
    $company = Company::factory()->create();
    $actor = User::query()->where('email', 'admin@example.com')->firstOrFail();
    $plan = Plan::query()->create([
        'name' => 'Payment plan',
        'code' => 'pay-'.uniqid(),
        'included_plugins' => [],
        'seat_limit' => 3,
    ]);
    $price = Price::query()->create([
        'plan_id' => $plan->id,
        'currency' => 'USD',
        'interval_months' => 1,
        'amount_minor' => 10000,
    ]);
    $draft = app(SubscriptionDrafts::class)->create($actor, $company, $price, new DateTimeImmutable('2026-01-01T00:00:00Z'));
    $active = app(SubscriptionLifecycle::class)->activate($draft, $actor, new DateTimeImmutable('2026-01-01T00:00:00Z'));
    app(RecurringInvoiceGenerator::class)->generateDue((int) $active->getKey(), new DateTimeImmutable('2026-02-01T00:00:00Z'));
    $invoice = Invoice::withoutGlobalScopes()->where('subscription_id', $active->getKey())->firstOrFail();

    return [$company, $actor, $active, $invoice];
}

it('returns the same payment when the reference is posted twice and refuses a different amount', function () {
    [$company, $actor] = paymentFixture();
    $service = app(PlatformPayments::class);
    $at = new DateTimeImmutable('2026-02-02T00:00:00Z');

    $first = $service->post($actor, $company, 5000, 'RCPT-1', $at);
    $second = $service->post($actor, $company, 5000, 'RCPT-1', $at);

    expect($second->is($first))->toBeTrue()
        ->and(Payment::withoutGlobalScopes()->where('company_id', $company->getKey())->count())->toBe(1)
        ->and(fn () => $service->post($actor, $company, 4000, 'RCPT-1', $at))
        ->toThrow(ValidationException::class, PlatformPayments::REFERENCE_AMOUNT);
});

it('refuses a multi-company admin even though credit notes can be delegated', function () {
    [$company] = paymentFixture();
    $actor = SecurityHelper::authenticateWithPermissions(['view_any_platform_billing_payment']);
    $actor->syncRoles([app(MultiCompanyAdminRoleProvisioner::class)->provision()->getKey()]);
    $actor->unsetRelation('roles');

    expect(fn () => app(PlatformPayments::class)->post($actor, $company, 100, 'RCPT-MCA', new DateTimeImmutable('2026-02-02T00:00:00Z')))
        ->toThrow(AuthorizationException::class, PlatformPayments::ONLY_SUPER_ADMIN)
        ->and(Gate::forUser($actor)->allows('create', Payment::class))->toBeFalse();
});

it('allocates one payment across invoices and rejects an amount past the invoice or the payment', function () {
    [$company, $actor, $active, $first] = paymentFixture();
    app(RecurringInvoiceGenerator::class)->generateDue((int) $active->getKey(), new DateTimeImmutable('2026-03-01T00:00:00Z'));
    $second = Invoice::withoutGlobalScopes()->where('subscription_id', $active->getKey())->orderBy('id')->get()->last();
    $service = app(PlatformPayments::class);
    $payment = $service->post($actor, $company, (int) $first->total_minor + 1000, 'RCPT-SPLIT', new DateTimeImmutable('2026-03-02T00:00:00Z'));

    $service->allocate($actor, $payment, [
        ['invoice_id' => (int) $second->getKey(), 'amount_minor' => 1000],
        ['invoice_id' => (int) $first->getKey(), 'amount_minor' => 4000],
    ], new DateTimeImmutable('2026-03-02T00:00:00Z'));

    expect($service->invoiceBalanceMinor($first->fresh()))->toBe((int) $first->total_minor - 4000)
        ->and($service->invoiceBalanceMinor($second->fresh()))->toBe((int) $second->total_minor - 1000)
        ->and($service->unappliedMinor($payment->fresh()))->toBe((int) $first->total_minor + 1000 - 5000)
        ->and(fn () => $service->allocate($actor, $payment, [
            ['invoice_id' => (int) $first->getKey(), 'amount_minor' => (int) $first->total_minor],
        ], new DateTimeImmutable('2026-03-02T00:00:00Z')))
        ->toThrow(ValidationException::class, PlatformPayments::INVOICE_BALANCE);

    $rest = $service->unappliedMinor($payment->fresh());

    expect(fn () => $service->allocate($actor, $payment, [
        ['invoice_id' => (int) $first->getKey(), 'amount_minor' => $rest + 1],
    ], new DateTimeImmutable('2026-03-02T00:00:00Z')))
        ->toThrow(ValidationException::class, PlatformPayments::PAYMENT_LEFT);
});

it('refuses an invoice from another company or another currency', function () {
    [$company, $actor, , $invoice] = paymentFixture();
    [$otherCompany] = paymentFixture();
    $otherInvoice = Invoice::withoutGlobalScopes()->where('company_id', $otherCompany->getKey())->firstOrFail();
    $service = app(PlatformPayments::class);
    $payment = $service->post($actor, $company, 1000, 'RCPT-CO', new DateTimeImmutable('2026-02-02T00:00:00Z'));

    expect(fn () => $service->allocate($actor, $payment, [
        ['invoice_id' => (int) $otherInvoice->getKey(), 'amount_minor' => 100],
    ], new DateTimeImmutable('2026-02-02T00:00:00Z')))
        ->toThrow(ValidationException::class, PlatformPayments::SAME_COMPANY);

    DB::statement('ALTER TABLE platform_billing_invoices DROP CONSTRAINT platform_billing_invoices_currency_check');
    DB::statement('ALTER TABLE platform_billing_invoices DISABLE TRIGGER platform_billing_invoices_immutable');

    try {
        DB::table('platform_billing_invoices')->where('id', $invoice->getKey())->update(['currency' => 'EUR']);

        expect(fn () => $service->allocate($actor, $payment->fresh(), [
            ['invoice_id' => (int) $invoice->getKey(), 'amount_minor' => 100],
        ], new DateTimeImmutable('2026-02-02T00:00:00Z')))
            ->toThrow(ValidationException::class, PlatformPayments::SAME_CURRENCY);
    } finally {
        DB::table('platform_billing_invoices')->where('id', $invoice->getKey())->update(['currency' => 'USD']);
        DB::statement('ALTER TABLE platform_billing_invoices ENABLE TRIGGER platform_billing_invoices_immutable');
        DB::statement("ALTER TABLE platform_billing_invoices ADD CONSTRAINT platform_billing_invoices_currency_check CHECK (currency = 'USD')");
    }
});

it('keeps posted payments immutable in the database', function () {
    [$company, $actor] = paymentFixture();
    $payment = app(PlatformPayments::class)->post($actor, $company, 1000, 'RCPT-IMM', new DateTimeImmutable('2026-02-02T00:00:00Z'));

    expect(fn () => DB::table('platform_billing_payments')->where('id', $payment->getKey())->update(['amount_minor' => 1]))
        ->toThrow(QueryException::class, 'Posted platform payments are immutable.');
});

it('holds the payment row lock so a second allocation waits its turn', function () {
    [$company, $actor, , $invoice] = paymentFixture();
    $service = app(PlatformPayments::class);
    $payment = $service->post($actor, $company, 5000, 'RCPT-LOCK', new DateTimeImmutable('2026-02-02T00:00:00Z'));
    $name = config('database.default');
    config(["database.connections.payment_lock" => config("database.connections.{$name}")]);
    DB::purge('payment_lock');

    DB::beginTransaction();
    $service->allocate($actor, $payment, [
        ['invoice_id' => (int) $invoice->getKey(), 'amount_minor' => 100],
    ], new DateTimeImmutable('2026-02-02T00:00:00Z'));

    $second = DB::connection('payment_lock');
    $second->statement("SET lock_timeout = '400ms'");

    expect(fn () => $second->select('select id from platform_billing_payments where id = ? for update', [$payment->getKey()]))
        ->toThrow(QueryException::class);

    DB::rollBack();
    $second->disconnect();
});

it('refunds only unapplied credit and a full reversal nets the payment to zero', function () {
    [$company, $actor, , $invoice] = paymentFixture();
    $service = app(PlatformPayments::class);
    $at = new DateTimeImmutable('2026-02-02T00:00:00Z');
    $payment = $service->post($actor, $company, 8000, 'RCPT-REV', $at);
    $service->allocate($actor, $payment, [
        ['invoice_id' => (int) $invoice->getKey(), 'amount_minor' => 3000],
    ], $at);

    expect(fn () => $service->refundCredit($actor, $payment, 6000, 'RF-TOO-MUCH', 'too much', $at))
        ->toThrow(ValidationException::class, PlatformPayments::REFUND_LIMIT);

    $refund = $service->refundCredit($actor, $payment, 2000, 'RF-1', 'customer overpaid', $at);
    $again = $service->refundCredit($actor, $payment, 2000, 'RF-1', 'customer overpaid', $at);

    expect($again->is($refund))->toBeTrue()
        ->and($service->unappliedMinor($payment->fresh()))->toBe(3000);

    $reversal = $service->reversePayment($actor, $payment, 'REV-1', 'posted in error', $at);

    expect($service->unappliedMinor($payment->fresh()))->toBe(0)
        ->and((int) $payment->fresh()->amount_minor)->toBe(
            (int) DB::table('platform_billing_allocations')->where('payment_id', $payment->getKey())->sum('amount_minor')
            + (int) DB::table('platform_billing_reversals')->where('payment_id', $payment->getKey())->sum('amount_minor')
        )
        ->and($reversal->reason)->toBe('posted in error')
        ->and($service->reconcile((int) $company->getKey())['drift_minor'])->toBe(0);
});

it('clears the subscription overdue state through the lifecycle when the overdue invoice is paid', function () {
    [$company, $actor, $active, $invoice] = paymentFixture();
    $lifecycle = app(SubscriptionLifecycle::class);
    $lifecycle->recordOverdue($active, $actor, new DateTimeImmutable('2026-02-09T00:00:00Z'));
    Auth::guard('web')->login($actor);
    $service = app(PlatformPayments::class);
    $payment = $service->post($actor, $company, (int) $invoice->total_minor, 'RCPT-PAID', new DateTimeImmutable('2026-02-20T00:00:00Z'));
    $service->allocate($actor, $payment, [
        ['invoice_id' => (int) $invoice->getKey(), 'amount_minor' => (int) $invoice->total_minor],
    ], new DateTimeImmutable('2026-02-20T00:00:00Z'));

    expect($active->fresh()->overdue_at)->toBeNull()
        ->and($lifecycle->isReadOnly($company, new DateTimeImmutable('2026-02-20T00:00:00Z')))->toBeFalse()
        ->and($service->reconcile((int) $company->getKey())['drift_minor'])->toBe(0);
});
