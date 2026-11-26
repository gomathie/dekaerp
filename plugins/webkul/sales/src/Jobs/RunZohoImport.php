<?php

namespace Webkul\Sale\Jobs;

use DateTimeInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Throwable;
use Webkul\Sale\Models\ZohoImportRun;
use Webkul\Sale\Services\ZohoImport\ZohoImportRunner;

/**
 * One short step of an approved Zoho Books import (or of a compare),
 * queued from the Super Admin page.
 *
 * Laravel Cloud's queue delivers at least once and gives a worker that is
 * scaled down 90 seconds before the job is retried (lead, 2026-11-26), so
 * the import is never one long job: each step works for at most
 * ZohoImportRunner::$stepSeconds and queues the next. The payload is the run
 * id and the step number only (the export stays in the run row; a queued
 * job must stay under 1 MiB). Safe to receive twice: see
 * ZohoImportRunner::execute().
 *
 * Retried, not failed, while another delivery holds the run: tries are
 * bounded by time instead (retryUntil), because each "busy" release counts
 * as an attempt.
 */
class RunZohoImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public int $timeout = 120;

    public function __construct(public int $runId, public int $step = 0) {}

    public function retryUntil(): DateTimeInterface
    {
        return now()->addHours(2);
    }

    public function handle(ZohoImportRunner $runner): void
    {
        $run = ZohoImportRun::query()->find($this->runId);

        if (! $run) {
            return;
        }

        if ($runner->execute($run, $this->step) === ZohoImportRunner::STEP_BUSY) {
            // Another delivery is working on this run, or a killed worker's
            // lock has not expired yet: try again after a while.
            $this->release(60);
        }
    }

    /**
     * The queue gave up on this step. The runner marks the run failed only
     * if nobody else is working on it and the step is still current.
     */
    public function failed(?Throwable $exception): void
    {
        app(ZohoImportRunner::class)->giveUp($this->runId, $this->step, $exception);
    }
}
