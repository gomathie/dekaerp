<?php

namespace Webkul\Sale\Listeners;

use Webkul\Inventory\Events\OperationCanceled;
use Webkul\Inventory\Events\OperationDone;
use Webkul\PluginManager\Package;
use Webkul\Sale\Facades\SaleOrder as SaleOrderFacade;

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

        if ($event->operation->saleOrder) {
            SaleOrderFacade::computeSaleOrder($event->operation->saleOrder);
        }
    }
}
