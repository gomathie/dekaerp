<?php

namespace Webkul\Sale\Console\Commands;

use Illuminate\Console\Command;
use Throwable;
use Webkul\PluginManager\Package;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Models\Order;
use Webkul\Sale\Services\OrderCalculator;

/**
 * One-off repair for plan 10, item 1 (2026-11-25): orders whose deliveries
 * were cancelled by `sales:backfill-orders-from-invoices` before it
 * recomputed them kept the "pending" delivery status they were confirmed
 * with, so the Sales dashboard showed 14 orders "to deliver" when 4 were.
 *
 * A command rather than a data migration: it is a dry run by default that
 * lists every order it would change, it can be limited to one company, and
 * it can be run again at any time (an order already right is not written).
 * A migration would run unseen on every installation and could not show
 * what it changed first.
 *
 * Only the stored `delivery_status` is written, recomputed by the one rule
 * that sets it everywhere else (OrderCalculator::refreshDeliveryStatus())
 * from the order's actual deliveries. Deliveries, stock moves, amounts and
 * invoice status are not touched.
 */
class RefreshOrderDeliveryStatus extends Command
{
    protected $signature = 'sales:refresh-delivery-status
                            {--company=* : Only these company ids}
                            {--apply : Save the corrected statuses (default is a dry run)}';

    protected $description = 'Recompute the stored delivery status of confirmed sales orders from their deliveries (stock unchanged)';

    public function handle(OrderCalculator $calculator): int
    {
        // Without Inventory there are no deliveries to read, and the rule
        // would mark every order "nothing to deliver" by default.
        if (! Package::isPluginInstalled('inventories')) {
            $this->info('Inventory is not installed: there are no deliveries to read.');

            return self::SUCCESS;
        }

        $query = Order::withoutGlobalScopes()
            ->whereNull('deleted_at')
            ->where('state', OrderState::SALE)
            ->orderBy('id');

        if ($companies = array_filter((array) $this->option('company'))) {
            $query->whereIn('company_id', $companies);
        }

        $stale = [];

        $query->with(['operations', 'lines'])->chunkById(200, function ($orders) use ($calculator, &$stale): void {
            foreach ($orders as $order) {
                $stored = $order->delivery_status;

                $calculator->refreshDeliveryStatus($order);

                if ($order->isDirty('delivery_status')) {
                    $stale[] = [$order, $stored];
                }
            }
        });

        if ($stale === []) {
            $this->info('Every confirmed order already shows its real delivery status.');

            return self::SUCCESS;
        }

        $this->table(
            ['Order', 'Company', 'Stored', 'From its deliveries'],
            array_map(fn (array $row): array => [
                $row[0]->name,
                $row[0]->company_id,
                $row[1]?->value ?? '-',
                $row[0]->delivery_status?->value,
            ], $stale),
        );

        if (! $this->option('apply')) {
            $this->warn(count($stale).' order(s) would be corrected. Re-run with --apply to save them.');

            return self::SUCCESS;
        }

        $saved = 0;

        foreach ($stale as [$order]) {
            try {
                // save(), not a query update: a company that is read-only
                // (expired test, overdue) keeps refusing writes here too.
                $order->save();
                $saved++;
            } catch (Throwable $exception) {
                report($exception);
                $this->error("Failed {$order->name}: {$exception->getMessage()}");
            }
        }

        $this->info("{$saved} order(s) corrected.");

        return self::SUCCESS;
    }
}
