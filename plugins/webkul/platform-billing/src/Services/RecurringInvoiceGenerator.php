<?php

namespace Webkul\PlatformBilling\Services;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Webkul\PlatformBilling\Enums\PlatformDocumentType;
use Webkul\PlatformBilling\Models\DiscountRedemption;
use Webkul\PlatformBilling\Models\Invoice;
use Webkul\PlatformBilling\Models\InvoiceLine;
use Webkul\PlatformBilling\Models\Subscription;
use Webkul\PlatformBilling\Support\Money;

/**
 * Posts one platform invoice per subscription period (D-SAAS-04, D-SAAS-05, D-SAAS-09).
 *
 * The period key is unique in the database. The generator inserts that row and
 * treats a unique conflict as "already posted"; it does not look first and then
 * insert. The subscription row is locked for the whole post so two workers
 * cannot both advance it.
 */
class RecurringInvoiceGenerator
{
    public const BATCH_LIMIT = 50;

    public const MAX_PERIODS = 24;

    /** @return array<int, int> */
    public function dueSubscriptionIds(DateTimeImmutable $at): array
    {
        return Subscription::withoutGlobalScopes()
            ->where('status', 'active')
            ->whereNotNull('next_invoice_at')
            ->where('next_invoice_at', '<=', $at)
            ->orderBy('next_invoice_at')
            ->orderBy('id')
            ->limit(self::BATCH_LIMIT)
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->all();
    }

    public function dueCount(DateTimeImmutable $at): int
    {
        return Subscription::withoutGlobalScopes()
            ->where('status', 'active')
            ->whereNotNull('next_invoice_at')
            ->where('next_invoice_at', '<=', $at)
            ->count();
    }

    /** @return array{subscription_id: int, company_id: int, period_start_at: ?string, amount_minor: int, currency: string}|null */
    public function preview(int $subscriptionId, DateTimeImmutable $at): ?array
    {
        $subscription = Subscription::withoutGlobalScopes()->find($subscriptionId);
        if (! $subscription instanceof Subscription || ! $this->isDue($subscription, $at)) {
            return null;
        }

        return [
            'subscription_id' => (int) $subscription->getKey(),
            'company_id' => (int) $subscription->company_id,
            'period_start_at' => $subscription->period_start_at?->format(DateTimeInterface::ATOM),
            'amount_minor' => (int) $subscription->amount_minor,
            'currency' => (string) $subscription->currency,
        ];
    }

    /**
     * Catch up elapsed periods one at a time. A late run must not skip a period,
     * and one subscription must not consume the whole batch, so the loop is capped.
     *
     * @return array<int, string>
     */
    public function generateDue(int $subscriptionId, DateTimeImmutable $at, int $maxPeriods = self::MAX_PERIODS): array
    {
        $outcomes = [];

        for ($period = 0; $period < $maxPeriods; $period++) {
            $outcome = $this->generatePeriod($subscriptionId, $at);
            $outcomes[] = $outcome;

            if (! in_array($outcome, ['created', 'already-invoiced'], true)) {
                break;
            }
        }

        $outcomes[] = $this->recordOverdueIfDue($subscriptionId, $at);

        Log::info('Platform billing invoice run.', [
            'subscription_id' => $subscriptionId,
            'outcomes' => $outcomes,
        ]);

        return $outcomes;
    }

    public function generatePeriod(int $subscriptionId, DateTimeImmutable $at): string
    {
        try {
            return DB::transaction(function () use ($subscriptionId, $at): string {
                $locked = Subscription::withoutGlobalScopes()->lockForUpdate()->find($subscriptionId);
                if (! $locked instanceof Subscription || ! $this->isDue($locked, $at)) {
                    return 'not-due';
                }

                $periodStart = $locked->period_start_at;
                $created = false;

                try {
                    DB::transaction(function () use ($locked): void {
                        $this->postInvoice($locked);
                    });
                    $created = true;
                } catch (UniqueConstraintViolationException) {
                    // The other worker committed this period. Fall through and
                    // advance only when this locked row is still on that period.
                }

                $invoiceExists = Invoice::withoutGlobalScopes()
                    ->where('subscription_id', $locked->getKey())
                    ->where('period_start_at', $periodStart)
                    ->exists();

                if (! $invoiceExists) {
                    throw ValidationException::withMessages([
                        'subscription' => 'The billing period could not be invoiced.',
                    ]);
                }

                $stillThisPeriod = $locked->period_start_at?->getTimestamp() === $periodStart?->getTimestamp()
                    && $locked->next_invoice_at !== null
                    && $locked->next_invoice_at->getTimestamp() <= $at->getTimestamp();

                if ($stillThisPeriod) {
                    // Advance only after the invoice row exists. A failure here
                    // rolls the invoice back with it, so a retry posts both.
                    app(SubscriptionLifecycle::class)->advanceFromScheduler($locked, $at);
                }

                return $created ? 'created' : 'already-invoiced';
            });
        } catch (ValidationException $exception) {
            if ($this->isReadOnlyRefusal($exception)) {
                return 'read-only-blocked';
            }

            throw $exception;
        }
    }

    /**
     * Grace starts on the invoice's own day-8 timestamp, not on the day the
     * sweep happens to notice. A later sweep must not move that timestamp:
     * writing the subscription again can also be refused once the company is
     * already read-only.
     */
    public function recordOverdueIfDue(int $subscriptionId, DateTimeImmutable $at): string
    {
        try {
            return DB::transaction(function () use ($subscriptionId, $at): string {
                $locked = Subscription::withoutGlobalScopes()->lockForUpdate()->find($subscriptionId);
                if (! $locked instanceof Subscription || $locked->status !== 'active' || $locked->overdue_at !== null) {
                    return 'overdue-unchanged';
                }

                $grace = Invoice::withoutGlobalScopes()
                    ->where('subscription_id', $locked->getKey())
                    ->where('grace_starts_at', '<=', $at)
                    ->orderBy('grace_starts_at')
                    ->value('grace_starts_at');

                if (! $grace instanceof DateTimeInterface) {
                    return 'not-overdue';
                }

                app(SubscriptionLifecycle::class)->recordOverdueFromScheduler(
                    $locked,
                    DateTimeImmutable::createFromInterface($grace),
                );

                return 'overdue-recorded';
            });
        } catch (ValidationException $exception) {
            if ($this->isReadOnlyRefusal($exception)) {
                return 'read-only-blocked';
            }

            throw $exception;
        }
    }

    protected function postInvoice(Subscription $subscription): Invoice
    {
        if ($subscription->currency !== 'USD') {
            throw ValidationException::withMessages([
                'currency' => 'Platform invoices are USD only.',
            ]);
        }

        // Date the document when the period fell due, not when a late job noticed
        // it. Otherwise a backlog would hand the tenant a fresh Net 7 on every
        // missed month and the day-8 grace would start late.
        $issuedAt = $subscription->next_invoice_at->setTimezone(new DateTimeZone('UTC'));
        $subtotal = Money::of((int) $subscription->amount_minor, 'USD');
        $redemption = $this->activeRedemption((int) $subscription->company_id);
        $ordinal = $redemption instanceof DiscountRedemption
            ? $this->invoiceOrdinal($redemption)
            : 1;
        $freeMonthApplies = $redemption instanceof DiscountRedemption
            && $this->freeMonthApplies($redemption, $issuedAt);
        $discount = $redemption instanceof DiscountRedemption
            ? app(DiscountCodeService::class)->calculateBeforeVat($redemption, $subtotal, $issuedAt, $ordinal, $freeMonthApplies)
            : Money::zero('USD');
        $taxable = $subtotal->subtract($discount);
        $taxEnabled = (bool) config('platform-billing.issuer.tax_enabled', true);
        $taxRate = (string) config('platform-billing.issuer.tax_rate', '0');
        $tax = $taxEnabled ? $taxable->percentage($taxRate) : Money::zero('USD');
        $total = $taxable->add($tax);
        $applied = $discount->minor > 0;
        $year = (int) $issuedAt->format('Y');
        $number = app(DocumentNumberGenerator::class)->next(PlatformDocumentType::Invoice, $year);
        $sequence = (int) substr($number, -6);

        $invoice = Invoice::query()->create([
            'company_id' => $subscription->company_id,
            'subscription_id' => $subscription->getKey(),
            'period_start_at' => $subscription->period_start_at,
            'period_end_at' => $subscription->period_end_at,
            'number' => $number,
            'document_year' => $year,
            'sequence' => $sequence,
            'issued_at' => $issuedAt,
            'due_at' => $issuedAt->modify('+7 days'),
            'grace_starts_at' => $issuedAt->modify('+8 days'),
            'currency' => 'USD',
            'issuer_name' => (string) config('platform-billing.issuer.name', 'DEKA'),
            'tax_rate' => $taxRate,
            'tax_enabled' => $taxEnabled,
            'subtotal_minor' => $subtotal->minor,
            'discount_minor' => $discount->minor,
            'tax_minor' => $tax->minor,
            'total_minor' => $total->minor,
            'discount_redemption_id' => $applied ? $redemption?->getKey() : null,
            'discount_code' => $applied ? $redemption?->code_snapshot : null,
            'discount_type' => $applied ? $redemption?->discount_type : null,
            'discount_value' => $applied ? $redemption?->discount_value : null,
            'free_month_applied' => $applied && $freeMonthApplies,
            'status' => 'posted',
        ]);

        InvoiceLine::query()->create([
            'invoice_id' => $invoice->getKey(),
            'company_id' => $subscription->company_id,
            'position' => 1,
            'description' => $subscription->plan_name.' ('.$subscription->interval_months.' months)',
            'amount_minor' => $subtotal->minor,
        ]);

        if ($applied && $redemption instanceof DiscountRedemption && $redemption->duration_type === 'one_invoice' && $ordinal === 1) {
            app(DiscountCodeService::class)->releaseOneInvoiceSlotAfterSnapshot($redemption);
        }

        return $invoice;
    }

    /**
     * D-SAAS-08 leaves the free-month calendar to the invoice run. Until that
     * is decided otherwise, the next `discount_value` invoices after redemption
     * are waived in full, and the code's own duration still limits them.
     */
    protected function freeMonthApplies(DiscountRedemption $redemption, DateTimeImmutable $invoiceAt): bool
    {
        if ($redemption->discount_type !== 'free_months') {
            return false;
        }

        if ($redemption->duration_ends_at && $invoiceAt >= $redemption->duration_ends_at) {
            return false;
        }

        $used = Invoice::withoutGlobalScopes()
            ->where('discount_redemption_id', $redemption->getKey())
            ->where('free_month_applied', true)
            ->count();

        return $used < (int) $redemption->discount_value;
    }

    protected function invoiceOrdinal(DiscountRedemption $redemption): int
    {
        return Invoice::withoutGlobalScopes()
            ->where('discount_redemption_id', $redemption->getKey())
            ->count() + 1;
    }

    protected function activeRedemption(int $companyId): ?DiscountRedemption
    {
        $redemptionId = DB::table('platform_billing_company_discount_slots')
            ->where('company_id', $companyId)
            ->value('redemption_id');

        if ($redemptionId === null) {
            return null;
        }

        $redemption = DiscountRedemption::withoutGlobalScopes()->find($redemptionId);

        return $redemption instanceof DiscountRedemption ? $redemption : null;
    }

    protected function isDue(Subscription $subscription, DateTimeImmutable $at): bool
    {
        return $subscription->status === 'active'
            && $subscription->period_start_at !== null
            && $subscription->period_end_at !== null
            && $subscription->next_invoice_at !== null
            && $subscription->next_invoice_at->getTimestamp() <= $at->getTimestamp();
    }

    protected function isReadOnlyRefusal(ValidationException $exception): bool
    {
        return in_array(
            CompanyReadOnlyWriteGuard::MESSAGE,
            $exception->errors()['company_id'] ?? [],
            true,
        );
    }
}
