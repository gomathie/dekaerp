<?php

namespace Webkul\Sale\Services;

use Illuminate\Support\Facades\DB;
use Throwable;
use Webkul\Account\Enums\DisplayType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Models\Move;
use Webkul\Account\Models\MoveLine;
use Webkul\Inventory\Enums\MoveState as InventoryMoveState;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Facades\Inventory;
use Webkul\Inventory\Models\Operation;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\Support\PluginEntitlement;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Facades\SaleOrder as SaleOrderFacade;
use Webkul\Sale\Models\InvoiceOrderLink;
use Webkul\Sale\Models\Order;
use Webkul\Sale\Support\SalesAccess;

/**
 * "Create sales order" on a customer invoice (user, 2026-11-22): an invoice
 * made directly, without an order, gets a confirmed sales order of its own,
 * so order history, deliveries and every order-based report include it.
 *
 * The order is made when the invoice is confirmed, not when it is saved: a
 * draft can still be edited or deleted, and an order built from a draft would
 * drift from what was finally invoiced. Cancelling or resetting the invoice
 * cancels the order (and its open delivery); confirming it again makes a new
 * order from the invoice as it then stands.
 *
 * Counted once: the invoice is attached to the order through
 * sales_order_invoices, which takes it out of DirectInvoices, and its lines
 * are linked to the order lines, which makes the order "Invoiced".
 */
class OrderFromInvoice
{
    /**
     * Records the request made on the invoice form. Idempotent.
     */
    public function request(Move $invoice, ?int $creatorId = null, bool $deliverNow = true): InvoiceOrderLink
    {
        SalesAccess::assertForCompany((int) $invoice->company_id);

        $link = InvoiceOrderLink::query()->firstOrCreate(
            ['move_id' => $invoice->getKey()],
            ['company_id' => $invoice->company_id, 'creator_id' => $creatorId, 'deliver_now' => $deliverNow],
        );

        // A draft can be re-saved with a different answer before it is
        // confirmed; once the order exists the delivery is handled on the
        // invoice page instead (markDelivered()).
        if (! $link->order_id && $link->deliver_now !== $deliverNow) {
            $link->update(['deliver_now' => $deliverNow]);
        }

        return $link;
    }

    public function link(Move $invoice): ?InvoiceOrderLink
    {
        return InvoiceOrderLink::query()->where('move_id', $invoice->getKey())->first();
    }

    /**
     * The live order made from the invoice, if any.
     */
    public function liveOrder(Move $invoice): ?Order
    {
        $orderId = $this->link($invoice)?->order_id;

        $order = $orderId ? Order::withoutGlobalScopes()->find($orderId) : null;

        return $order && $order->state !== OrderState::CANCEL ? $order : null;
    }

    /**
     * "Mark as delivered" on the invoice page (option E): validates what is
     * ready. Returns true when nothing is left to deliver.
     */
    public function markDelivered(Move $invoice): bool
    {
        $order = $this->liveOrder($invoice);

        if (! $order) {
            return false;
        }

        SalesAccess::assertForCompany((int) $order->company_id);

        $this->validateDeliveries($order);

        SaleOrderFacade::computeSaleOrder($order->refresh());

        return $this->pendingDeliveries($order) === 0;
    }

    /**
     * Deliveries of the order not done or cancelled.
     */
    public function pendingDeliveries(Order $order): int
    {
        if (! Package::isPluginInstalled('inventories')) {
            return 0;
        }

        return $order->operations()
            ->whereNotIn('state', [OperationState::DONE, OperationState::CANCELED])
            ->count();
    }

    public function isRequested(Move $invoice): bool
    {
        return InvoiceOrderLink::query()->where('move_id', $invoice->getKey())->exists();
    }

    /**
     * Called when the invoice is confirmed. Makes the order if one was asked
     * for and none is live yet.
     */
    public function handleConfirmed(Move $invoice): ?Order
    {
        $link = InvoiceOrderLink::query()->where('move_id', $invoice->getKey())->first();

        if (! $link || $invoice->move_type !== MoveType::OUT_INVOICE || $invoice->state !== MoveState::POSTED) {
            return null;
        }

        $existing = $link->order_id
            ? Order::withoutGlobalScopes()->find($link->order_id)
            : null;

        if ($existing && $existing->state !== OrderState::CANCEL) {
            return $existing;
        }

        // The module may have been revoked since the box was ticked; the
        // invoice is still confirmed, only the order is not made. The
        // company's own grant decides, not the actor: SalesAccess lets a
        // Super Admin through, which would make orders for a company that
        // does not have Sales.
        if (! PluginEntitlement::enabledForCompany('sales', (int) $invoice->company_id)) {
            return null;
        }

        return DB::transaction(function () use ($invoice, $link): Order {
            $order = $this->createOrder($invoice, $link);

            $link->update(['order_id' => $order->getKey()]);

            return $order;
        });
    }

    /**
     * Called when the invoice is cancelled or reset to draft: the order made
     * from it is cancelled, with any delivery that has not been done.
     */
    public function handleUndone(Move $invoice): void
    {
        $link = InvoiceOrderLink::query()->where('move_id', $invoice->getKey())->first();

        if (! $link?->order_id) {
            return;
        }

        $order = Order::withoutGlobalScopes()->find($link->order_id);

        // OrderWorkflow::cancel() refuses a company whose Sales module was
        // revoked. Cancelling the invoice must never fail because of that, so
        // the order is then left as it is.
        if ($order && $order->state !== OrderState::CANCEL && SalesAccess::enabledForCompany((int) $order->company_id)) {
            app(OrderWorkflow::class)->cancel($order);
        }
    }

    protected function createOrder(Move $invoice, InvoiceOrderLink $link): Order
    {
        $creatorId = $link->creator_id ?? $invoice->creator_id;

        $order = Order::query()->create([
            'company_id'         => $invoice->company_id,
            'partner_id'         => $invoice->partner_id,
            'currency_id'        => $invoice->currency_id,
            'fiscal_position_id' => $invoice->fiscal_position_id,
            'payment_term_id'    => $invoice->invoice_payment_term_id,
            'user_id'            => $invoice->invoice_user_id ?? $creatorId,
            'creator_id'         => $creatorId,
            'date_order'         => $invoice->invoice_date ?? now(),
            'origin'             => $invoice->name,
            'client_order_ref'   => $invoice->ref,
            'state'              => OrderState::DRAFT,
        ]);

        // Read past the global scopes: the invoice's own lines, whoever is
        // acting (a listener can run as another company's user or no one).
        $invoiceLines = MoveLine::withoutGlobalScopes()
            ->where('move_id', $invoice->getKey())
            ->where('display_type', DisplayType::PRODUCT)
            ->whereNotNull('product_id')
            // A zero-quantity line sells nothing, and OrderCalculator divides
            // by the line quantity (read as the string "0.0000", which PHP
            // treats as true), so such a line would fail the whole order.
            ->where('quantity', '!=', 0)
            ->orderBy('id')
            ->get();

        foreach ($invoiceLines as $sort => $invoiceLine) {
            $orderLine = $order->lines()->create([
                'sort'             => $sort,
                'name'             => $invoiceLine->name,
                'state'            => OrderState::DRAFT->value,
                'product_id'       => $invoiceLine->product_id,
                'product_uom_id'   => $invoiceLine->uom_id,
                'company_id'       => $order->company_id,
                'currency_id'      => $order->currency_id,
                'order_partner_id' => $order->partner_id,
                'salesman_id'      => $order->user_id,
                'creator_id'       => $creatorId,
                'product_qty'      => $invoiceLine->quantity,
                'product_uom_qty'  => $invoiceLine->quantity,
                'price_unit'       => $invoiceLine->price_unit,
                'discount'         => $invoiceLine->discount ?? 0,
                'customer_lead'    => 0,
            ]);

            $orderLine->taxes()->sync($invoiceLine->taxes()->allRelatedIds());

            // Both ways: the invoice line counts as this line's invoiced
            // quantity, so the order reads "Invoiced", not "To invoice".
            $orderLine->accountMoveLines()->sync([$invoiceLine->getKey()]);
        }

        $order->accountMoves()->syncWithoutDetaching([$invoice->getKey()]);

        $order = SaleOrderFacade::computeSaleOrder($order->refresh());

        // Confirming requests the delivery for storable products under the
        // existing inventory rules; services need none.
        $order = app(OrderWorkflow::class)->confirm($order);

        // "Customer has taken the goods" (option A, default from the Sales
        // setting, option D). No: the delivery waits for the warehouse.
        if ($link->deliver_now) {
            $this->validateDeliveries($order);
        }

        return SaleOrderFacade::computeSaleOrder($order->refresh());
    }

    /**
     * Invoiced directly means the goods left with the customer (user,
     * 2026-11-22: auto-validate). Each step is validated only when its stock
     * is fully reserved: a short delivery is left open for the warehouse
     * rather than pushing stock negative. A failure here must not undo the
     * confirmed invoice, so it is reported and the delivery stays open.
     */
    protected function validateDeliveries(Order $order): void
    {
        if (! Package::isPluginInstalled('inventories')) {
            return;
        }

        // Multi-step warehouses chain pick, pack and ship; the next step only
        // becomes ready when the previous one is done.
        for ($step = 0; $step < 6; $step++) {
            $ready = $order->operations()->with('moves')->get()->first(
                fn (Operation $operation): bool => ! in_array($operation->state, [OperationState::DONE, OperationState::CANCELED], true)
                    && $operation->moves->isNotEmpty()
                    && $operation->moves->every(fn ($move): bool => in_array($move->state, [InventoryMoveState::ASSIGNED, InventoryMoveState::CANCELED], true)),
            );

            if (! $ready) {
                return;
            }

            try {
                Inventory::completeTransfer($ready->refresh());
            } catch (Throwable $exception) {
                report($exception);

                return;
            }
        }
    }
}
