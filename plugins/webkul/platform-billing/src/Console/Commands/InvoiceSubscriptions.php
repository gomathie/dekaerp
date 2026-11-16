<?php

namespace Webkul\PlatformBilling\Console\Commands;

use DateTimeImmutable;
use DateTimeZone;
use Illuminate\Console\Command;
use Webkul\PlatformBilling\Jobs\GenerateSubscriptionInvoice;
use Webkul\PlatformBilling\Services\RecurringInvoiceGenerator;

class InvoiceSubscriptions extends Command
{
    protected $signature = 'platform-billing:invoice-subscriptions
        {--dry-run : List due periods without posting or queueing}
        {--at= : Invoice as of this UTC instant, for a catch-up or a test}';

    protected $description = 'Queue one invoice job for each subscription whose billing period has ended';

    public function handle(RecurringInvoiceGenerator $generator): int
    {
        $at = $this->asOf();
        $due = $generator->dueCount($at);
        $ids = $generator->dueSubscriptionIds($at);

        if ($this->option('dry-run')) {
            foreach ($ids as $id) {
                $preview = $generator->preview($id, $at);
                if ($preview === null) {
                    continue;
                }

                $this->line(sprintf(
                    'subscription %d company %d period %s amount %d %s',
                    $preview['subscription_id'],
                    $preview['company_id'],
                    (string) $preview['period_start_at'],
                    $preview['amount_minor'],
                    $preview['currency'],
                ));
            }

            $this->info($due.' due; dry run posted nothing.');

            return self::SUCCESS;
        }

        foreach ($ids as $id) {
            GenerateSubscriptionInvoice::dispatch($id, $at->format(DATE_ATOM));
        }

        $this->info(count($ids).' subscription invoice jobs dispatched ('.$due.' due).');

        return self::SUCCESS;
    }

    protected function asOf(): DateTimeImmutable
    {
        $given = $this->option('at');

        if (is_string($given) && $given !== '') {
            return new DateTimeImmutable($given, new DateTimeZone('UTC'));
        }

        return new DateTimeImmutable('now', new DateTimeZone('UTC'));
    }
}
