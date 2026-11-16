<?php

namespace Webkul\PlatformBilling\Jobs;

use DateTimeImmutable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;
use Webkul\PlatformBilling\Services\RecurringInvoiceGenerator;

/**
 * One subscription per job. There is no signed-in user, so the generator reads
 * the subscription and its company by key instead of the session.
 */
class GenerateSubscriptionInvoice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public int $subscriptionId,
        public string $asOf,
    ) {}

    public function backoff(): int
    {
        return 60;
    }

    public function handle(RecurringInvoiceGenerator $generator): void
    {
        try {
            $generator->generateDue($this->subscriptionId, new DateTimeImmutable($this->asOf));
        } catch (Throwable $exception) {
            report($exception);

            throw $exception;
        }
    }
}
