<?php

namespace Webkul\Logistics\Listeners;

use Throwable;
use Webkul\Logistics\Services\ShipmentFromOrder;
use Webkul\Sale\Events\OrderConfirmed;

/**
 * Creates a draft shipment when a sales order with logistics services is
 * confirmed (D1).
 *
 * Listens rather than editing Sales: `OrderWorkflow::confirm()` already
 * dispatches `OrderConfirmed`, so no upstream file changes.
 *
 * Failures are swallowed on purpose. Confirming a sales order must not fail
 * because Logistics could not make a shipment - the order is the customer's
 * commitment and the shipment is a convenience. The exception is reported so
 * it reaches Sentry rather than disappearing.
 */
class CreateShipmentFromOrder
{
    public function __construct(protected ShipmentFromOrder $converter) {}

    public function handle(OrderConfirmed $event): void
    {
        try {
            $this->converter->convert($event->order);
        } catch (Throwable $e) {
            report($e);
        }
    }
}
