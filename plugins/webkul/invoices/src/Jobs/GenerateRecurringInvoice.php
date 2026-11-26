<?php

namespace Webkul\Invoice\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;
use Webkul\Invoice\Models\RecurringInvoiceTemplate;
use Webkul\Invoice\Services\RecurringInvoiceService;
use Webkul\Support\Services\CompanyContext;

class GenerateRecurringInvoice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public int $templateId, public string $scheduledDate, public bool $manual = false) {}

    public function backoff(): int
    {
        return 60;
    }

    public function handle(RecurringInvoiceService $service, CompanyContext $companyContext): void
    {
        try {
            $template = RecurringInvoiceTemplate::withoutGlobalScopes()->find($this->templateId);

            if (! $template) {
                return;
            }

            $companyContext->runFor((int) $template->company_id, fn () => $service->generateDue($this->templateId, $this->manual, $this->scheduledDate));
        } catch (Throwable $exception) {
            report($exception);

            throw $exception;
        }
    }
}
