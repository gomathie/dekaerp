<?php

namespace Webkul\Sale\Listeners;

use Filament\Notifications\Notification;
use Webkul\Account\Events\InvoicesDeletedFromScreen;
use Webkul\Account\Events\MoveDeleted;
use Webkul\Account\Events\MoveDeleting;
use Webkul\PluginManager\Package;
use Webkul\Sale\Services\DeletedInvoiceOrders;

/**
 * Keeps a sales order true to its invoices when one is deleted
 * (DeletedInvoiceOrders explains the rule).
 */
class RecomputeOrdersOfDeletedInvoiceListener
{
    public function handle(MoveDeleting|MoveDeleted|InvoicesDeletedFromScreen $event): void
    {
        if (! Package::isPluginInstalled('sales')) {
            return;
        }

        $orders = app(DeletedInvoiceOrders::class);

        if ($event instanceof MoveDeleting) {
            $orders->remember($event->move);

            return;
        }

        if ($event instanceof MoveDeleted) {
            $orders->recompute($event->move);

            return;
        }

        // "No" (or the question never asked) keeps the order to invoice later.
        if (! ($event->data['cancel_sale_orders'] ?? false)) {
            return;
        }

        $cancelled = $orders->cancelOrdersOf($event->moveIds);

        if ($cancelled !== []) {
            Notification::make()
                ->success()
                ->title(__('sales::filament/contributions/invoice.delete.cancelled', ['orders' => implode(', ', $cancelled)]))
                ->send();
        }
    }
}
