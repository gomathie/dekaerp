<?php

namespace Webkul\Sale\Listeners;

use Webkul\Inventory\Events\OperationCanceled;
use Webkul\Inventory\Events\OperationDone;
use Webkul\PluginManager\Package;
use Webkul\Sale\Facades\SaleOrder as SaleOrderFacade;
use Webkul\Sale\Models\Order;

class ComputeSaleOrderListener
{
    /**
     * A delivery done or cancelled changes the order's delivery status.
     * Cancelled was not heard (plan 10, 2026-11-25): a delivery cancelled by
     * hand, through the API or by the November backfill left its order
     * "pending", and the Sales dashboard counted it "to deliver".
     */
    public function handle(OperationDone|OperationCanceled $event): void
    {
        if (! Package::isPluginInstalled('sales')) {
            return;
        }

        if (! $event->operation->sale_order_id) {
            return;
        }

        // Past the scopes, by the delivery's own order key: for a user who
        // sees only their own records the relation read null and the order
        // was not recomputed (2026-11-26).
        $order = Order::withoutGlobalScopes()
            ->whereNull('deleted_at')
            ->find($event->operation->sale_order_id);

        if ($order) {
            SaleOrderFacade::computeSaleOrder($order);
        }
    }
}
