<?php

namespace Webkul\PlatformBilling\Console\Commands;

use Illuminate\Console\Command;
use Webkul\PlatformBilling\Models\Payment;
use Webkul\PlatformBilling\Models\Subscription;
use Webkul\PlatformBilling\Services\PlatformPayments;

/**
 * Read-only billing health check (comprehensive review CR-10 item 3,
 * 2026-11-13).
 *
 * Two silent failures it catches:
 *  - money that does not add up: for every company with payments, the
 *    balances are recomputed from the immutable payment, allocation and
 *    reversal rows through PlatformPayments::reconcile() - the same rule the
 *    payment screens show, so this cannot disagree with them;
 *  - invoicing that stopped: an active subscription whose next invoice is
 *    more than a day overdue means the scheduler is not running or the
 *    invoice job keeps failing. Nobody notices that until a client is
 *    billed late or never.
 *
 * Exits non-zero when anything is found, so it can be scheduled or wired to
 * an alert. It writes nothing.
 *
 *     php artisan platform-billing:health-check
 */
class BillingHealthCheck extends Command
{
    /** How late an invoice may be before it counts as stalled. */
    public const LATE_AFTER_HOURS = 24;

    protected $signature = 'platform-billing:health-check';

    protected $description = 'Recompute billing balances and look for stalled invoicing (read-only)';

    public function handle(PlatformPayments $payments): int
    {
        $problems = [];

        $companyIds = Payment::withoutGlobalScopes()->distinct()->orderBy('company_id')->pluck('company_id');

        foreach ($companyIds as $companyId) {
            $drift = $payments->reconcile((int) $companyId)['drift_minor'];

            if ($drift !== 0) {
                $problems[] = ['balance drift', (int) $companyId, $drift.' minor units do not reconcile'];
            }
        }

        Subscription::withoutGlobalScopes()
            ->where('status', 'active')
            ->whereNotNull('next_invoice_at')
            ->where('next_invoice_at', '<', now()->subHours(self::LATE_AFTER_HOURS))
            ->orderBy('next_invoice_at')
            ->get()
            ->each(function (Subscription $subscription) use (&$problems): void {
                $problems[] = [
                    'invoicing stalled',
                    (int) $subscription->company_id,
                    'subscription '.$subscription->getKey().' was due '.$subscription->next_invoice_at->toIso8601String(),
                ];
            });

        if ($problems === []) {
            $this->components->info('Billing is healthy: balances reconcile and no invoice is overdue.');

            return self::SUCCESS;
        }

        $this->table(['Problem', 'Company', 'Detail'], $problems);
        $this->components->error(count($problems).' billing problem(s) found.');

        return self::FAILURE;
    }
}
