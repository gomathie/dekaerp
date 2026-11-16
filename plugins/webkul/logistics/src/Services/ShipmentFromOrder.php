<?php

namespace Webkul\Logistics\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Webkul\Logistics\Models\Shipment;
use Webkul\Logistics\Support\LogisticsAccess;
use Webkul\Product\Models\Product;
use Webkul\Sale\Models\Order;
use Webkul\Sale\Models\OrderLine;

/**
 * Turns a confirmed sales order into a draft shipment (D1).
 *
 * Draft, never confirmed: a salesperson confirming an order is saying the
 * customer agreed to buy, not that operations agreed to a route and a date.
 * Someone in Logistics still has to look at it.
 *
 * Deliberately conservative about when it runs at all - see shouldConvert().
 * An integration that quietly creates records is one people stop trusting, so
 * every reason to decline is explicit and testable.
 */
class ShipmentFromOrder
{
    /**
     * The service products that mean "this order needs transporting".
     */
    public const SERVICE_REFERENCES = [
        'LOG-FREIGHT',
        'LOG-PICKUP',
        'LOG-DELIVERY',
        'LOG-HANDLING',
        'LOG-WAITING',
        'LOG-STORAGE',
    ];

    public function convert(Order $order): ?Shipment
    {
        if (! $this->shouldConvert($order)) {
            return null;
        }

        return DB::transaction(function () use ($order): ?Shipment {
            // Re-checked inside the transaction: two confirmations racing would
            // otherwise both pass the check above and create two shipments for
            // one order.
            if ($this->existingShipment($order)) {
                return null;
            }

            $shipment = Shipment::create([
                'company_id'         => $order->company_id,
                'customer_id'        => $order->partner_id,
                'currency_id'        => $order->currency_id,
                'sale_order_id'      => $order->getKey(),
                'customer_reference' => $order->name,
            ]);

            foreach ($this->serviceLines($order) as $line) {
                // company_id is left to InheritsParentCompany: the charge takes
                // the shipment's company, never the session's.
                $shipment->charges()->create([
                    'description' => $line->name,
                    'quantity'    => $line->product_uom_qty,
                    'price_unit'  => $line->price_unit,
                    'discount'    => $line->discount ?? 0,
                    'is_billable' => false,
                    'product_id'  => $line->product_id,
                    'uom_id'      => $line->product_uom_id,
                    'currency_id' => $order->currency_id,
                ]);
            }

            return $shipment;
        });
    }

    /**
     * Every reason this order is none of Logistics' business.
     */
    public function shouldConvert(Order $order): bool
    {
        if (! $order->company_id || ! LogisticsAccess::enabledFor((int) $order->company_id)) {
            return false;
        }

        // There is deliberately no guard on the order's own dates. The rule is
        // "no shipment for an order confirmed before the company enabled
        // Logistics", and that is enforced by *when* this runs: the listener
        // fires on confirmation, so the switch checked above is the switch as
        // it stood at that moment. An order confirmed while the company was
        // off never fires the event again.
        //
        // Testing date_order instead would block the opposite case - a
        // quotation drafted last month and confirmed today, the day after the
        // company adopted Logistics - which is precisely the work a company
        // adopting Logistics has in hand. Orders already confirmed before
        // adoption are converted deliberately, through the "From sales order"
        // picker on the shipment form.

        if ($this->existingShipment($order)) {
            return false;
        }

        return $this->serviceLines($order)->isNotEmpty();
    }

    protected function existingShipment(Order $order): bool
    {
        // Scopes removed on purpose: this runs from an event, where the active
        // company is whoever confirmed the order. A shipment that already
        // exists for this order must be found whether or not the current
        // session could see it - otherwise a duplicate is created.
        return Shipment::withoutGlobalScopes()
            ->where('sale_order_id', $order->getKey())
            ->exists();
    }

    /**
     * Order lines whose product is one of this company's logistics services.
     *
     * @return Collection<int, OrderLine>
     */
    protected function serviceLines(Order $order): Collection
    {
        $serviceProductIds = Product::withoutGlobalScopes()
            ->where('company_id', $order->company_id)
            ->whereIn('reference', self::SERVICE_REFERENCES)
            ->pluck('id');

        if ($serviceProductIds->isEmpty()) {
            return new Collection;
        }

        // Scope-free for the same reason as existingShipment(): the order's own
        // lines decide this, not whichever company the session that confirmed
        // the order happens to be looking at. OrderLine is company-scoped, so
        // reading through $order->lines() would silently find nothing whenever
        // the two differ - a queued confirmation, a console script - and the
        // order would look like it had no logistics services at all.
        return OrderLine::withoutGlobalScopes()
            ->where('order_id', $order->getKey())
            ->whereIn('product_id', $serviceProductIds)
            ->get();
    }
}
