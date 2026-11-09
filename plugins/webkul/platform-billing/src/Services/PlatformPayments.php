<?php

namespace Webkul\PlatformBilling\Services;

use DateTimeImmutable;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Webkul\PlatformBilling\Models\Allocation;
use Webkul\PlatformBilling\Models\Invoice;
use Webkul\PlatformBilling\Models\Payment;
use Webkul\PlatformBilling\Models\Reversal;
use Webkul\PlatformBilling\Models\Subscription;
use Webkul\Security\Models\User;
use Webkul\Security\Services\SecurityAuditLogger;
use Webkul\Support\Models\Company;

/**
 * Posts platform payments, allocations and credit refunds (D-SAAS-10).
 *
 * Gate::before lets a Super Admin skip policies, so this service checks the
 * actor itself. Balances are sums of posted rows, never a stored running total.
 */
class PlatformPayments
{
    public const ONLY_SUPER_ADMIN = 'Only a Super Admin may post platform payments.';

    public const AMOUNT_POSITIVE = 'A payment amount must be greater than zero.';

    public const REFERENCE_REQUIRED = 'A payment needs a reference.';

    public const REFERENCE_AMOUNT = 'This reference was already used for a different amount.';

    public const SAME_COMPANY = 'A payment can be allocated only to an invoice of the same company.';

    public const SAME_CURRENCY = 'A payment can be allocated only to an invoice in the same currency.';

    public const PAYMENT_LEFT = 'This allocation is more than the payment has left.';

    public const INVOICE_BALANCE = 'This allocation is more than the invoice balance.';

    public const ALLOCATION_POSITIVE = 'An allocation amount must be greater than zero.';

    public const REFUND_LIMIT = 'A refund cannot exceed the unapplied credit.';

    public const REASON_REQUIRED = 'A reversal needs a reason.';

    public const USD_ONLY = 'Platform payments are USD only.';

    public function post(
        User $actor,
        Company $company,
        int $amountMinor,
        string $reference,
        DateTimeImmutable $receivedAt,
    ): Payment {
        $this->assertSuperAdmin($actor);
        $reference = trim($reference);

        if ($reference === '') {
            throw ValidationException::withMessages(['reference' => self::REFERENCE_REQUIRED]);
        }

        if ($amountMinor <= 0) {
            throw ValidationException::withMessages(['amount' => self::AMOUNT_POSITIVE]);
        }

        return DB::transaction(function () use ($actor, $company, $amountMinor, $reference, $receivedAt): Payment {
            try {
                $payment = DB::transaction(function () use ($actor, $company, $amountMinor, $reference, $receivedAt): Payment {
                    return Payment::query()->create([
                        'company_id' => $company->getKey(),
                        'idempotency_key' => $reference,
                        'amount_minor' => $amountMinor,
                        'currency' => 'USD',
                        'received_at' => $receivedAt,
                        'posted_by' => $actor->getKey(),
                        'status' => 'posted',
                    ]);
                });
            } catch (UniqueConstraintViolationException) {
                $payment = $this->existingPayment((int) $company->getKey(), $reference);

                if ((int) $payment->amount_minor !== $amountMinor) {
                    throw ValidationException::withMessages(['reference' => self::REFERENCE_AMOUNT]);
                }

                return $payment;
            }

            app(SecurityAuditLogger::class)->record(
                'platform_billing.payment_posted',
                $payment,
                $actor,
                (int) $company->getKey(),
                after: ['amount_minor' => $amountMinor, 'reference' => $reference],
            );

            return $payment;
        });
    }

    /**
     * @param  array<int, array{invoice_id: int, amount_minor: int}>  $lines
     * @return array<int, Allocation>
     */
    public function allocate(User $actor, Payment $payment, array $lines, DateTimeImmutable $at): array
    {
        $this->assertSuperAdmin($actor);

        if ($lines === []) {
            throw ValidationException::withMessages(['allocation' => self::ALLOCATION_POSITIVE]);
        }

        return DB::transaction(function () use ($actor, $payment, $lines, $at): array {
            $this->lockPaymentRow((int) $payment->getKey());
            $lockedPayment = Payment::withoutGlobalScopes()->findOrFail($payment->getKey());
            $invoiceIds = collect($lines)
                ->map(fn (array $line): int => (int) $line['invoice_id'])
                ->unique()
                ->sort()
                ->values();

            // Lock invoices in id order after the payment. Two allocations that
            // share invoices cannot deadlock by taking those rows in opposite orders.
            $invoices = [];

            foreach ($invoiceIds as $invoiceId) {
                $this->lockInvoiceRow((int) $invoiceId);
                $invoice = Invoice::withoutGlobalScopes()->find($invoiceId);

                if (! $invoice instanceof Invoice) {
                    throw ValidationException::withMessages(['invoice' => self::SAME_COMPANY]);
                }

                if ((int) $invoice->company_id !== (int) $lockedPayment->company_id) {
                    throw ValidationException::withMessages(['invoice' => self::SAME_COMPANY]);
                }

                if ($invoice->currency !== $lockedPayment->currency) {
                    throw ValidationException::withMessages(['invoice' => self::SAME_CURRENCY]);
                }

                $invoices[$invoiceId] = $invoice;
            }

            $requested = 0;

            foreach ($lines as $line) {
                $amount = (int) $line['amount_minor'];

                if ($amount <= 0) {
                    throw ValidationException::withMessages(['allocation' => self::ALLOCATION_POSITIVE]);
                }

                $requested += $amount;
                $invoice = $invoices[(int) $line['invoice_id']];

                if ($amount > $this->invoiceBalanceMinor($invoice)) {
                    throw ValidationException::withMessages(['allocation' => self::INVOICE_BALANCE]);
                }
            }

            if ($requested > $this->unappliedMinor($lockedPayment)) {
                throw ValidationException::withMessages(['allocation' => self::PAYMENT_LEFT]);
            }

            $created = [];
            $settled = false;

            foreach ($lines as $line) {
                $invoice = $invoices[(int) $line['invoice_id']];
                $created[] = Allocation::query()->create([
                    'payment_id' => $lockedPayment->getKey(),
                    'invoice_id' => $invoice->getKey(),
                    'company_id' => $lockedPayment->company_id,
                    'amount_minor' => (int) $line['amount_minor'],
                    'posted_by' => $actor->getKey(),
                    'posted_at' => $at,
                ]);

                if ($this->invoiceBalanceMinor($invoice) === 0) {
                    $settled = true;
                }
            }

            if ($settled) {
                $this->clearOverdueIfSettled((int) $lockedPayment->company_id, $actor, $at);
            }

            app(SecurityAuditLogger::class)->record(
                'platform_billing.payment_allocated',
                $lockedPayment,
                $actor,
                (int) $lockedPayment->company_id,
                after: ['lines' => $lines],
            );

            return $created;
        });
    }

    public function refundCredit(
        User $actor,
        Payment $payment,
        int $amountMinor,
        string $reference,
        string $reason,
        DateTimeImmutable $at,
    ): Reversal {
        $this->assertSuperAdmin($actor);
        $reference = trim($reference);
        $reason = trim($reason);

        if ($reference === '') {
            throw ValidationException::withMessages(['reference' => self::REFERENCE_REQUIRED]);
        }

        if ($reason === '') {
            throw ValidationException::withMessages(['reason' => self::REASON_REQUIRED]);
        }

        if ($amountMinor <= 0) {
            throw ValidationException::withMessages(['amount' => self::AMOUNT_POSITIVE]);
        }

        return DB::transaction(function () use ($actor, $payment, $amountMinor, $reference, $reason, $at): Reversal {
            $this->lockPaymentRow((int) $payment->getKey());
            $locked = Payment::withoutGlobalScopes()->findOrFail($payment->getKey());
            $existing = $this->findReversal((int) $locked->company_id, $reference);

            if ($existing instanceof Reversal) {
                if ((int) $existing->amount_minor !== $amountMinor || (int) $existing->payment_id !== (int) $locked->getKey()) {
                    throw ValidationException::withMessages(['reference' => self::REFERENCE_AMOUNT]);
                }

                return $existing;
            }

            if ($amountMinor > $this->unappliedMinor($locked)) {
                throw ValidationException::withMessages(['amount' => self::REFUND_LIMIT]);
            }

            return $this->insertReversal($locked, $actor, $amountMinor, $reference, $reason, $at);
        });
    }

    /**
     * Undo every allocation and refund whatever credit remains, so the payment
     * nets to zero. The reversal row does not send money to a bank.
     */
    public function reversePayment(
        User $actor,
        Payment $payment,
        string $reference,
        string $reason,
        DateTimeImmutable $at,
    ): Reversal {
        $this->assertSuperAdmin($actor);
        $reference = trim($reference);
        $reason = trim($reason);

        if ($reference === '') {
            throw ValidationException::withMessages(['reference' => self::REFERENCE_REQUIRED]);
        }

        if ($reason === '') {
            throw ValidationException::withMessages(['reason' => self::REASON_REQUIRED]);
        }

        return DB::transaction(function () use ($actor, $payment, $reference, $reason, $at): Reversal {
            $this->lockPaymentRow((int) $payment->getKey());
            $locked = Payment::withoutGlobalScopes()->findOrFail($payment->getKey());
            $existing = $this->findReversal((int) $locked->company_id, $reference);

            if ($existing instanceof Reversal) {
                if ((int) $existing->payment_id !== (int) $locked->getKey()) {
                    throw ValidationException::withMessages(['reference' => self::REFERENCE_AMOUNT]);
                }

                return $existing;
            }

            $invoiceIds = Allocation::withoutGlobalScopes()
                ->where('payment_id', $locked->getKey())
                ->pluck('invoice_id')
                ->map(fn ($id): int => (int) $id)
                ->unique()
                ->sort()
                ->values();

            foreach ($invoiceIds as $invoiceId) {
                $this->lockInvoiceRow((int) $invoiceId);
                $invoice = Invoice::withoutGlobalScopes()->findOrFail($invoiceId);
                $applied = (int) Allocation::withoutGlobalScopes()
                    ->where('payment_id', $locked->getKey())
                    ->where('invoice_id', $invoice->getKey())
                    ->sum('amount_minor');

                if ($applied === 0) {
                    continue;
                }

                Allocation::query()->create([
                    'payment_id' => $locked->getKey(),
                    'invoice_id' => $invoice->getKey(),
                    'company_id' => $locked->company_id,
                    'amount_minor' => -$applied,
                    'posted_by' => $actor->getKey(),
                    'posted_at' => $at,
                ]);
            }

            $remaining = $this->unappliedMinor($locked);

            if ($remaining <= 0) {
                throw ValidationException::withMessages(['amount' => self::REFUND_LIMIT]);
            }

            return $this->insertReversal($locked, $actor, $remaining, $reference, $reason, $at);
        });
    }

    /**
     * @return array{unapplied_minor: int, open_minor: int, drift_minor: int}
     */
    public function reconcile(int $companyId): array
    {
        $unapplied = 0;
        $drift = 0;

        foreach (Payment::withoutGlobalScopes()->where('company_id', $companyId)->orderBy('id')->get() as $payment) {
            $left = $this->unappliedMinor($payment);
            $unapplied += $left;

            if ($left < 0) {
                $drift += -$left;
            }
        }

        $open = 0;

        foreach (Invoice::withoutGlobalScopes()->where('company_id', $companyId)->orderBy('id')->get() as $invoice) {
            $balance = $this->invoiceBalanceMinor($invoice);
            $open += $balance;

            if ($balance < 0) {
                $drift += -$balance;
            }
        }

        $payments = (int) Payment::withoutGlobalScopes()->where('company_id', $companyId)->sum('amount_minor');
        $allocated = (int) Allocation::withoutGlobalScopes()->where('company_id', $companyId)->sum('amount_minor');
        $reversed = (int) Reversal::withoutGlobalScopes()->where('company_id', $companyId)->sum('amount_minor');
        $identity = $payments - $allocated - $reversed;

        if ($identity !== $unapplied) {
            $drift += abs($identity - $unapplied);
        }

        return [
            'unapplied_minor' => $unapplied,
            'open_minor' => $open,
            'drift_minor' => $drift,
        ];
    }

    public function unappliedMinor(Payment $payment): int
    {
        $allocated = (int) Allocation::withoutGlobalScopes()->where('payment_id', $payment->getKey())->sum('amount_minor');
        $reversed = (int) Reversal::withoutGlobalScopes()->where('payment_id', $payment->getKey())->sum('amount_minor');

        return (int) $payment->amount_minor - $allocated - $reversed;
    }

    public function invoiceBalanceMinor(Invoice $invoice): int
    {
        $applied = (int) Allocation::withoutGlobalScopes()->where('invoice_id', $invoice->getKey())->sum('amount_minor');

        return (int) $invoice->total_minor - $applied;
    }

    protected function clearOverdueIfSettled(int $companyId, User $actor, DateTimeImmutable $at): void
    {
        $subscription = Subscription::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->where('status', 'active')
            ->whereNotNull('overdue_at')
            ->first();

        if ($subscription instanceof Subscription) {
            DB::connection()->getPdo()->query(
                'select id from platform_billing_subscriptions where id = '.(int) $subscription->getKey().' for update'
            );
            $subscription->refresh();
        }

        if (! $subscription instanceof Subscription) {
            return;
        }

        $stillOverdue = Invoice::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->where('grace_starts_at', '<=', $at)
            ->orderBy('id')
            ->get()
            ->contains(fn (Invoice $invoice): bool => $this->invoiceBalanceMinor($invoice) > 0);

        if ($stillOverdue) {
            return;
        }

        app(SubscriptionLifecycle::class)->clearOverdue($subscription, $actor);
    }

    protected function insertReversal(
        Payment $payment,
        User $actor,
        int $amountMinor,
        string $reference,
        string $reason,
        DateTimeImmutable $at,
    ): Reversal {
        if ($payment->currency !== 'USD') {
            throw ValidationException::withMessages(['currency' => self::USD_ONLY]);
        }

        try {
            $reversal = DB::transaction(function () use ($payment, $actor, $amountMinor, $reference, $reason, $at): Reversal {
                return Reversal::query()->create([
                    'company_id' => $payment->company_id,
                    'payment_id' => $payment->getKey(),
                    'idempotency_key' => $reference,
                    'amount_minor' => $amountMinor,
                    'currency' => 'USD',
                    'reason' => $reason,
                    'effective_at' => $at,
                    'posted_by' => $actor->getKey(),
                ]);
            });
        } catch (UniqueConstraintViolationException) {
            $reversal = $this->findReversal((int) $payment->company_id, $reference);

            if (! $reversal instanceof Reversal || (int) $reversal->amount_minor !== $amountMinor) {
                throw ValidationException::withMessages(['reference' => self::REFERENCE_AMOUNT]);
            }

            return $reversal;
        }

        app(SecurityAuditLogger::class)->record(
            'platform_billing.payment_reversed',
            $reversal,
            $actor,
            (int) $payment->company_id,
            after: ['amount_minor' => $amountMinor, 'reference' => $reference, 'reason' => $reason],
        );

        return $reversal;
    }

    protected function existingPayment(int $companyId, string $reference): Payment
    {
        $payment = Payment::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->where('idempotency_key', $reference)
            ->first();

        if (! $payment instanceof Payment) {
            throw ValidationException::withMessages(['reference' => self::REFERENCE_AMOUNT]);
        }

        return $payment;
    }

    protected function findReversal(int $companyId, string $reference): ?Reversal
    {
        $reversal = Reversal::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->where('idempotency_key', $reference)
            ->first();

        return $reversal instanceof Reversal ? $reversal : null;
    }

    /**
     * Hold this payment until the transaction commits. Another allocation of
     * the same payment waits on this row, so both cannot spend one remainder.
     * The id is an integer and is inlined.
     */
    protected function lockPaymentRow(int $paymentId): void
    {
        DB::connection()->getPdo()->query(
            'select id from platform_billing_payments where id = '.$paymentId.' for update'
        );
    }

    protected function lockInvoiceRow(int $invoiceId): void
    {
        DB::connection()->getPdo()->query(
            'select id from platform_billing_invoices where id = '.$invoiceId.' for update'
        );
    }

    protected function assertSuperAdmin(User $actor): void
    {
        // A credit-note grant does not reach this method (D-SAAS-10). A
        // Multi-Company Admin is refused here even when Gate::before would
        // have skipped a policy.
        if (! $actor->is_active || ! $actor->isSuperAdmin()) {
            throw new AuthorizationException(self::ONLY_SUPER_ADMIN);
        }
    }
}
