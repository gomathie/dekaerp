<?php

namespace Webkul\Invoice\Console\Commands;

use Illuminate\Console\Command;
use Webkul\Invoice\Jobs\GenerateRecurringInvoice;
use Webkul\Invoice\Models\RecurringInvoiceTemplate;
use Webkul\PluginManager\Support\PluginEntitlement;

class InvoiceRecurringTemplates extends Command
{
    protected $signature = 'invoices:generate-recurring';

    protected $description = 'Generate due recurring customer invoices.';

    public function handle(): int
    {
        // Every due template, in batches: a fixed limit left the rest a day
        // late each day once more were due. One job per template and period.
        $templates = RecurringInvoiceTemplate::withoutGlobalScopes()
            ->where('state', 'active')
            ->whereDate('next_invoice_date', '<=', today())
            ->select(['id', 'company_id', 'next_invoice_date'])
            ->lazyById(200);

        foreach ($templates as $template) {
            if (! PluginEntitlement::enabledForCompany('invoices', (int) $template->company_id)) {
                continue;
            }

            GenerateRecurringInvoice::dispatch((int) $template->getKey(), $template->next_invoice_date->toDateString());
        }

        return self::SUCCESS;
    }
}
