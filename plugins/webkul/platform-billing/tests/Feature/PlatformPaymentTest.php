<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Webkul\PlatformBilling\Models\Allocation;
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

    // The read-only guard looks at the authenticated user, not the service
    // argument. Posting is a signed-in Super Admin (D-SAAS-10).
    Auth::shouldUse('web');
    Auth::guard('web')->login($actor);

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
    $actor = User::withoutEvents(fn (): User => User::factory()->create([
        'default_company_id' => $company->getKey(),
        'is_active' => true,
        'is_default' => false,
    ]));
    $actor->allowedCompanies()->sync([$company->getKey()]);
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
        ['invoice_id' => (int) $second->getKey(), 'amount_minor' => $rest + 1],
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
    // The suite wraps each test in a transaction, so a second session cannot
    // see this test's rows and will not wait on them. Commit that wrapper.
    // The posted invoice number must not survive, or the next test starts at
    // 000002. Posted rows cannot be deleted until their immutability triggers
    // are off. The tearDown still expects a transaction it can roll back.
    while (DB::transactionLevel() > 0) {
        DB::commit();
    }

    $companyId = null;
    $counterBefore = DB::table('platform_billing_document_numbers')
        ->where('document_type', 'invoice')
        ->where('year', 2026)
        ->value('last_number');

    try {
        [$company, $actor, , $invoice] = paymentFixture();
        $companyId = (int) $company->getKey();
        $service = app(PlatformPayments::class);
        $payment = $service->post($actor, $company, 5000, 'RCPT-LOCK', new DateTimeImmutable('2026-02-02T00:00:00Z'));
        $name = config('database.default');
        $probe = config("database.connections.{$name}");
        unset($probe['name']);
        config(['database.connections.payment_lock' => $probe]);
        DB::purge('payment_lock');

        $blocked = false;
        Allocation::creating(function () use (&$blocked, $payment): void {
            if ($blocked) {
                return;
            }

            $second = DB::connection('payment_lock');
            $pdo = $second->getPdo();
            $pdo->exec("SET lock_timeout = '400ms'");

            try {
                $pdo->query('select id from platform_billing_payments where id = '.(int) $payment->getKey().' for update');
            } catch (PDOException $exception) {
                $blocked = str_contains($exception->getMessage(), 'lock timeout');
            } finally {
                $second->disconnect();
            }
        });

        $service->allocate($actor, $payment, [
            ['invoice_id' => (int) $invoice->getKey(), 'amount_minor' => 100],
        ], new DateTimeImmutable('2026-02-02T00:00:00Z'));

        expect($blocked)->toBeTrue();
    } finally {
        if ($companyId !== null) {
            foreach ([
                'platform_billing_allocations',
                'platform_billing_reversals',
                'platform_billing_payments',
                'platform_billing_invoice_lines',
                'platform_billing_invoices',
            ] as $table) {
                DB::statement('ALTER TABLE '.$table.' DISABLE TRIGGER '.$table.'_immutable');
            }

            DB::table('platform_billing_allocations')->where('company_id', $companyId)->delete();
            DB::table('platform_billing_reversals')->where('company_id', $companyId)->delete();
            DB::table('platform_billing_payments')->where('company_id', $companyId)->delete();
            DB::table('platform_billing_invoice_lines')->where('company_id', $companyId)->delete();
            DB::table('platform_billing_invoices')->where('company_id', $companyId)->delete();
            DB::table('platform_billing_subscriptions')->where('company_id', $companyId)->delete();

            foreach ([
                'platform_billing_allocations',
                'platform_billing_reversals',
                'platform_billing_payments',
                'platform_billing_invoice_lines',
                'platform_billing_invoices',
            ] as $table) {
                DB::statement('ALTER TABLE '.$table.' ENABLE TRIGGER '.$table.'_immutable');
            }
        }

        if ($counterBefore === null) {
            DB::table('platform_billing_document_numbers')
                ->where('document_type', 'invoice')
                ->where('year', 2026)
                ->delete();
        } else {
            DB::table('platform_billing_document_numbers')
                ->where('document_type', 'invoice')
                ->where('year', 2026)
                ->update(['last_number' => $counterBefore]);
        }

        if (DB::transactionLevel() === 0) {
            DB::beginTransaction();
        }
    }
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

it('starts a fresh overdue grace from a reversal that reopens an invoice past day 8', function () {
    [$company, $actor, $active, $invoice] = paymentFixture();
    $lifecycle = app(SubscriptionLifecycle::class);
    $service = app(PlatformPayments::class);
    $paidAt = new DateTimeImmutable('2026-02-20T00:00:00Z');
    $lifecycle->recordOverdue($active, $actor, new DateTimeImmutable('2026-02-09T00:00:00Z'));
    $payment = $service->post($actor, $company, (int) $invoice->total_minor, 'RCPT-REOPEN', $paidAt);
    $service->allocate($actor, $payment, [
        ['invoice_id' => (int) $invoice->getKey(), 'amount_minor' => (int) $invoice->total_minor],
    ], $paidAt);

    expect($active->fresh()->overdue_at)->toBeNull();

    $service->reversePayment($actor, $payment, 'REV-REOPEN', 'payment returned', $paidAt);
    $mark = $active->fresh()->overdue_at;

    expect($mark?->format('Y-m-d H:i:s'))->toBe('2026-02-20 00:00:00')
        ->and($lifecycle->isReadOnly($company->fresh(), $paidAt))->toBeFalse()
        ->and($lifecycle->isReadOnly($company->fresh(), $paidAt->modify('+10 days')))->toBeTrue();
});

it('does not mark overdue when a reversal reopens an invoice before day 8', function () {
    [$company, $actor, $active, $invoice] = paymentFixture();
    $service = app(PlatformPayments::class);
    $at = new DateTimeImmutable('2026-02-02T00:00:00Z');
    $payment = $service->post($actor, $company, (int) $invoice->total_minor, 'RCPT-EARLY', $at);
    $service->allocate($actor, $payment, [
        ['invoice_id' => (int) $invoice->getKey(), 'amount_minor' => (int) $invoice->total_minor],
    ], $at);
    $service->reversePayment($actor, $payment, 'REV-EARLY', 'posted too early', $at);

    expect($active->fresh()->overdue_at)->toBeNull();
});

it('does not restart an overdue grace that is already running', function () {
    [$company, $actor, $active, $invoice] = paymentFixture();
    $lifecycle = app(SubscriptionLifecycle::class);
    $service = app(PlatformPayments::class);
    $original = new DateTimeImmutable('2026-02-09T00:00:00Z');
    $lifecycle->recordOverdue($active, $actor, $original);
    $payment = $service->post($actor, $company, 1000, 'RCPT-PARTIAL', new DateTimeImmutable('2026-02-20T00:00:00Z'));
    $service->allocate($actor, $payment, [
        ['invoice_id' => (int) $invoice->getKey(), 'amount_minor' => 1000],
    ], new DateTimeImmutable('2026-02-20T00:00:00Z'));
    $service->reversePayment($actor, $payment, 'REV-PARTIAL', 'partial payment returned', new DateTimeImmutable('2026-02-21T00:00:00Z'));

    expect($active->fresh()->overdue_at?->format('Y-m-d'))->toBe('2026-02-09');
});

it('leaves the overdue mark alone when a refund does not reopen an invoice', function () {
    [$company, $actor, $active, $invoice] = paymentFixture();
    $lifecycle = app(SubscriptionLifecycle::class);
    $service = app(PlatformPayments::class);
    $paidAt = new DateTimeImmutable('2026-02-20T00:00:00Z');
    $lifecycle->recordOverdue($active, $actor, new DateTimeImmutable('2026-02-09T00:00:00Z'));
    $payment = $service->post($actor, $company, (int) $invoice->total_minor + 500, 'RCPT-CREDIT', $paidAt);
    $service->allocate($actor, $payment, [
        ['invoice_id' => (int) $invoice->getKey(), 'amount_minor' => (int) $invoice->total_minor],
    ], $paidAt);

    expect($active->fresh()->overdue_at)->toBeNull();

    $service->refundCredit($actor, $payment, 500, 'RF-CREDIT', 'refund unused credit', $paidAt);

    expect($active->fresh()->overdue_at)->toBeNull()
        ->and($service->invoiceBalanceMinor($invoice->fresh()))->toBe(0);
});
