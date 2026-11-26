<?php

namespace Webkul\Sale\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Throwable;
use Webkul\Sale\Models\ZohoImportRun;
use Webkul\Sale\Services\ZohoImport\ZohoImportRunner;

/**
 * Runs an approved Zoho Books import queued from the Super Admin page. A
 * full history takes minutes, longer than a web request may last.
 *
 * Tried once: the importer is idempotent, but a second automatic attempt
 * after a refusal would only refuse again. A Super Admin starts a new run
 * after fixing the cause, and it continues from where this one stopped.
 */
class RunZohoImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public int $tries = 1;

    public int $timeout = 3600;

    public function __construct(public int $runId) {}

    public function handle(ZohoImportRunner $runner): void
    {
        set_time_limit(0);

        $run = ZohoImportRun::query()->find($this->runId);

        if ($run) {
            $runner->execute($run);
        }
    }

    /**
     * The worker died or timed out: the run must not stay "running" forever,
     * and the uploaded export must not stay in the database.
     */
    public function failed(?Throwable $exception): void
    {
        $run = ZohoImportRun::query()->find($this->runId);

        if ($run && $run->isActive()) {
            app(ZohoImportRunner::class)->fail($run, $exception ?? new \RuntimeException('The import job stopped.'));
        }
    }
}
