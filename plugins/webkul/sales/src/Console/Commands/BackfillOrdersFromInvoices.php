<?php

namespace Webkul\Sale\Console\Commands;

use Illuminate\Console\Command;
use Throwable;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Models\Move;
use Webkul\Sale\Services\OrderFromInvoice;
use Webkul\Sale\Services\ProcurementRequester;
use Webkul\Sale\Support\SalesAccess;

/**
 * Gives confirmed customer invoices made without an order a sales order, as
 * "Create sales order" does for new ones (user, 2026-11-22: backfill).
 *
 * Past sales already happened, so their stock is left exactly as it is: the
 * delivery each order requests is cancelled straight away rather than
 * validated (validating would take stock out today for goods that left weeks
 * ago, or that the company already adjusted by hand).
 *
 * Skips invoices already linked to an order, so it can be run again safely.
 * Dry run by default; --apply writes.
 */
class BackfillOrdersFromInvoices extends Command
{
    protected $signature = 'sales:backfill-orders-from-invoices
                            {--company=* : Only these company ids}
                            {--apply : Write the orders (default is a dry run)}';

    protected $description = 'Create sales orders for confirmed customer invoices that have none (stock unchanged)';

    public function handle(OrderFromInvoice $service, ProcurementRequester $procurement): int
    {
        $query = Move::withoutGlobalScopes()
            ->where('move_type', MoveType::OUT_INVOICE)
            ->where('state', MoveState::POSTED)
            ->whereNotIn('id', fn ($q) => $q->select('move_id')->from('sales_order_invoices'))
            ->orderBy('id');

        if ($companies = array_filter((array) $this->option('company'))) {
            $query->whereIn('company_id', $companies);
        }

        $invoices = $query->get();

        if ($invoices->isEmpty()) {
            $this->info('No invoices without an order.');

            return self::SUCCESS;
        }

        $this->table(
            ['Invoice', 'Company', 'Date', 'Total'],
            $invoices->map(fn (Move $m): array => [$m->name, $m->company_id, $m->invoice_date?->toDateString(), $m->amount_total])->all(),
        );

        if (! $this->option('apply')) {
            $this->warn($invoices->count().' invoice(s) would get an order. Re-run with --apply to create them.');

            return self::SUCCESS;
        }

        $created = 0;

        foreach ($invoices as $invoice) {
            if (! SalesAccess::enabledForCompany((int) $invoice->company_id)) {
                $this->line("Skipped {$invoice->name}: Sales is not enabled for company {$invoice->company_id}.");

                continue;
            }

            try {
                $service->request($invoice, $invoice->creator_id, deliverNow: false);

                $order = $service->handleConfirmed($invoice);

                if ($order) {
                    $procurement->cancelOperations($order->refresh());
                    $created++;
                    $this->line("{$invoice->name} -> {$order->name}");
                }
            } catch (Throwable $exception) {
                report($exception);
                $this->error("Failed {$invoice->name}: {$exception->getMessage()}");
            }
        }

        $this->info("{$created} order(s) created.");

        return self::SUCCESS;
    }
}
