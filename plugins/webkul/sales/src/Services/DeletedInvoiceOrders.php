<?php

namespace Webkul\Sale\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Gate;
use Throwable;
use Webkul\Account\Models\Move;
use Webkul\Inventory\Models\Operation;
use Webkul\PluginManager\Package;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Facades\SaleOrder as SaleOrderFacade;
use Webkul\Sale\Models\Order;
use Webkul\Sale\Models\OrderLine;
use Webkul\Sale\Support\SalesAccess;

/**
 * A draft invoice made from a sales order, then deleted (production,
 * 2026-11-25): the delete cascades away the order's invoice links, but
 * nothing recomputed the order, so it stayed "Invoiced" with no invoice
 * behind it and its delivery kept reserving stock.
 *
 * User decision: the order is always recomputed (back to "To invoice"), and
 * the delete confirmation asks whether to cancel the order and its delivery
 * too. Yes goes through OrderWorkflow::cancel() with no mail; No keeps the
 * order to invoice later. Only someone allowed to change the order is asked.
 *
 * Bound per request: the links are read before the delete and used after it.
 */
class DeletedInvoiceOrders
{
    /** @var array<int, array{company_id: int, orders: list<int>}> */
    protected array $linksByInvoice = [];

    /**
     * Orders the invoice was made from. Read past the scopes: a listener
     * runs as whoever acted, and a scoped read silently finds nothing.
     *
     * @return Collection<int, Order>
     */
    public function ordersOf(Move $invoice): Collection
    {
        return Order::withoutGlobalScopes()
            ->whereNull('deleted_at')
            ->whereHas('accountMoves', fn ($query) => $query->withoutGlobalScopes()->whereKey($invoice->getKey()))
            ->orderBy('id')
            ->get();
    }

    /**
     * Confirmed orders of these invoices that the acting user may cancel.
     *
     * @param  iterable<Move>  $invoices
     * @return Collection<int, Order>
     */
    public function cancellableOrders(iterable $invoices): Collection
    {
        $orders = new Collection;

        foreach ($invoices as $invoice) {
            foreach ($this->ordersOf($invoice) as $order) {
                if ($this->mayCancel($order, (int) $invoice->company_id)) {
                    $orders->put($order->getKey(), $order);
                }
            }
        }

        return $orders->values();
    }

    /**
     * Before the delete: what the cascade is about to remove.
     */
    public function remember(Move $invoice): void
    {
        $orderIds = $this->ordersOf($invoice)->modelKeys();

        if ($orderIds !== []) {
            $this->linksByInvoice[$invoice->getKey()] = [
                'company_id' => (int) $invoice->company_id,
                'orders'     => $orderIds,
            ];
        }
    }

    /**
     * After the delete: the orders read their invoiced quantities again,
     * now without the deleted invoice.
     */
    public function recompute(Move $invoice): void
    {
        $orderIds = $this->linksByInvoice[$invoice->getKey()]['orders'] ?? [];

        Order::withoutGlobalScopes()
            ->whereNull('deleted_at')
            ->whereIn('id', $orderIds)
            ->get()
            ->each(fn (Order $order) => SaleOrderFacade::computeSaleOrder($this->loadedPastScopes($order)));
    }

    /**
     * The recompute reads the order through its lines, the other invoices
     * through their lines and the deliveries, and Order, Move and Operation
     * carry the ownership scope. Deleted by someone who sees only their own
     * records, the line's order came back null and the delete failed (test,
     * 2026-11-26). Each is read here past the scopes, filtered by this
     * order's own keys.
     */
    protected function loadedPastScopes(Order $order): Order
    {
        $lines = OrderLine::withoutGlobalScopes()
            ->where('order_id', $order->getKey())
            ->with([
                'accountMoveLines'      => fn ($query) => $query->withoutGlobalScopes(),
                'accountMoveLines.move' => fn ($query) => $query->withoutGlobalScopes(),
            ])
            ->get()
            ->each(fn (OrderLine $line) => $line->setRelation('order', $order));

        $order->setRelation('lines', $lines);

        if (Package::isPluginInstalled('inventories')) {
            $order->setRelation('operations', Operation::withoutGlobalScopes()->where('sale_order_id', $order->getKey())->get());
        }

        return $order;
    }

    /**
     * "Yes, also cancel" in the delete confirmation. Only orders of invoices
     * deleted in this request are reached (the links read before the
     * delete), and each is checked again: still confirmed, same company, and
     * one the acting user may change. The answer itself names no order, so
     * it cannot be pointed at another one.
     *
     * @param  list<int|string>  $deletedInvoiceIds
     * @return list<string> names of the orders cancelled
     */
    public function cancelOrdersOf(array $deletedInvoiceIds): array
    {
        $cancelled = [];

        foreach (array_unique(array_map('intval', $deletedInvoiceIds)) as $invoiceId) {
            $links = $this->linksByInvoice[$invoiceId] ?? null;

            if (! $links) {
                continue;
            }

            foreach (Order::withoutGlobalScopes()->whereNull('deleted_at')->whereIn('id', $links['orders'])->get() as $order) {
                if (! $this->mayCancel($order, $links['company_id'])) {
                    continue;
                }

                try {
                    // No data: the plain cancel, which sends the customer
                    // nothing, and cancels the order's open deliveries.
                    app(OrderWorkflow::class)->cancel($order);
                    $cancelled[] = $order->name;
                } catch (Throwable $exception) {
                    report($exception);
                }
            }
        }

        return $cancelled;
    }

    protected function mayCancel(Order $order, int $invoiceCompanyId): bool
    {
        return $order->state === OrderState::SALE
            // The invoice's own company: the order was read past the scopes.
            && (int) $order->company_id === $invoiceCompanyId
            && SalesAccess::enabledForCompany((int) $order->company_id)
            && Gate::allows('update', $order);
    }
}
