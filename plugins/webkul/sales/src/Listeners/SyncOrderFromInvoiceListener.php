<?php

namespace Webkul\Sale\Listeners;

use Webkul\Account\Events\MoveCancelled;
use Webkul\Account\Events\MoveConfirmed;
use Webkul\Account\Events\MoveDrafted;
use Webkul\PluginManager\Package;
use Webkul\Sale\Services\OrderFromInvoice;

/**
 * Keeps the sales order made from an invoice ("Create sales order") in step
 * with that invoice: made on confirm, cancelled on cancel or reset to draft.
 */
class SyncOrderFromInvoiceListener
{
    public function handle(MoveConfirmed|MoveCancelled|MoveDrafted $event): void
    {
        if (! Package::isPluginInstalled('sales')) {
            return;
        }

        $service = app(OrderFromInvoice::class);

        if ($event instanceof MoveConfirmed) {
            $service->handleConfirmed($event->move);

            return;
        }

        $service->handleUndone($event->move);
    }
}
