<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Models\Move;
use Webkul\Inventory\Enums\DeliveryStep;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Enums\ReceptionStep;
use Webkul\Inventory\Facades\Inventory;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\Support\PluginAccess;
use Webkul\Sale\Enums\OrderDeliveryStatus;
use Webkul\Sale\Filament\Widgets\OrderActivityWidget;
use Webkul\Sale\Models\Order;
use Webkul\Sale\Services\OrderFromInvoice;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../inventories/tests/Helpers/InventoryHelper.php';
require_once __DIR__.'/../../Helpers/SaleHelper.php';

/**
 * Plan 10, item 1 (2026-11-25): the November backfill cancelled the
 * deliveries of past sales but left each order's delivery status at
 * "pending", so the Sales dashboard counted them "to deliver". The backfill
 * now recomputes the order, and `sales:refresh-delivery-status` repairs the
 * orders it left behind without touching stock.
 */
beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('inventories');
    TestBootstrapHelper::ensurePluginInstalled('sales');

    foreach (['inventories', 'sales'] as $plugin) {
        DB::table('plugins')->updateOrInsert(
            ['name' => $plugin],
            ['is_installed' => true, 'is_active' => true, 'updated_at' => now()],
        );
    }

    Package::$plugins = Plugin::all()->keyBy('name');
    PluginAccess::flush();

    URL::resolveMissingNamedRoutesUsing(fn () => '#');

    SaleHelper::actingAsAdmin();

    $this->warehouse = InventoryHelper::warehouse(ReceptionStep::ONE_STEP, DeliveryStep::ONE_STEP);
    $this->product = SaleHelper::product();
});

/**
 * A posted customer invoice with no order, as a direct invoice is: built
 * through an order whose links are then removed.
 */
function postedInvoiceWithoutOrder($product, float $qty = 2): Move
{
    $order = SaleHelper::order(['warehouse_id' => test()->warehouse->id]);
    SaleHelper::line($order, $product, $qty, 40);
    SaleHelper::compute($order);

    $invoice = SaleHelper::createInvoice($order);

    DB::table('sales_order_invoices')->where('move_id', $invoice->id)->delete();
    DB::table('sales_order_line_invoices')
        ->whereIn('invoice_line_id', $invoice->lines()->pluck('id'))
        ->delete();
    $order->forceDelete();

    $invoice->lines()->where('display_type', 'product')->update(['quantity' => $qty]);
    AccountFacade::computeAccountMove($invoice->refresh());

    return SaleHelper::postInvoice($invoice->refresh());
}

/**
 * A confirmed order made from an invoice, its delivery still waiting.
 */
function orderWaitingForDelivery($product): Order
{
    $invoice = postedInvoiceWithoutOrder($product);

    // Requested after posting, then confirmed by hand: the order is made
    // exactly as on confirm, and the delivery is left open.
    app(OrderFromInvoice::class)->request($invoice, null, deliverNow: false);

    return app(OrderFromInvoice::class)->handleConfirmed($invoice)->refresh();
}

function stockSnapshot(): array
{
    return [
        DB::table('inventories_moves')->orderBy('id')->get(['id', 'state', 'quantity'])->toArray(),
        DB::table('inventories_product_quantities')->orderBy('id')->get(['id', 'quantity', 'reserved_quantity'])->toArray(),
    ];
}

it('does not count a backfilled order as to deliver once its only delivery is cancelled', function () {
    $invoice = postedInvoiceWithoutOrder($this->product);

    $this->artisan('sales:backfill-orders-from-invoices', ['--apply' => true])->assertSuccessful();

    $order = Order::withoutGlobalScopes()
        ->whereHas('accountMoves', fn ($q) => $q->whereKey($invoice->id))
        ->firstOrFail();

    expect($order->operations()->where('state', '!=', OperationState::CANCELED)->count())->toBe(0)
        ->and($order->delivery_status)->toBe(OrderDeliveryStatus::NO)
        ->and(in_array($order->delivery_status, OrderActivityWidget::TO_DELIVER, true))->toBeFalse();
});

/**
 * An order as production has it: delivery cancelled, status still
 * "pending". Since cancelling now recomputes the order (OperationCanceled),
 * the stale status is written back by hand, past the model, to stand for
 * the rows the November backfill left.
 */
function staleOrder($product): Order
{
    $order = orderWaitingForDelivery($product);
    $order->operations->each(fn ($operation) => Inventory::cancelTransfer($operation));

    Order::withoutGlobalScopes()->whereKey($order->id)->update(['delivery_status' => OrderDeliveryStatus::PENDING->value]);

    return $order->refresh();
}

it('recomputes an order when its only delivery is cancelled by hand', function () {
    $order = orderWaitingForDelivery($this->product);
    expect($order->delivery_status)->toBe(OrderDeliveryStatus::PENDING);

    // The same service the Cancel button and the API call.
    $order->operations->each(fn ($operation) => Inventory::cancelTransfer($operation));

    expect($order->refresh()->delivery_status)->toBe(OrderDeliveryStatus::NO);
});

it('corrects an order left pending after its only delivery was cancelled, without touching stock', function () {
    $stale = staleOrder($this->product);
    expect($stale->delivery_status)->toBe(OrderDeliveryStatus::PENDING);

    $waiting = orderWaitingForDelivery($this->product);

    $stock = stockSnapshot();

    $this->artisan('sales:refresh-delivery-status')->assertSuccessful();
    expect($stale->refresh()->delivery_status)->toBe(OrderDeliveryStatus::PENDING);

    $this->artisan('sales:refresh-delivery-status', ['--apply' => true])->assertSuccessful();

    expect($stale->refresh()->delivery_status)->toBe(OrderDeliveryStatus::NO)
        ->and($waiting->refresh()->delivery_status)->toBe(OrderDeliveryStatus::PENDING)
        ->and(stockSnapshot())->toEqual($stock);

    $this->artisan('sales:refresh-delivery-status', ['--apply' => true])
        ->expectsOutputToContain('already shows its real delivery status')
        ->assertSuccessful();
});

it('limits the repair to the companies named', function () {
    $stale = staleOrder($this->product);

    $this->artisan('sales:refresh-delivery-status', ['--apply' => true, '--company' => [$stale->company_id + 1000]])
        ->assertSuccessful();
    expect($stale->refresh()->delivery_status)->toBe(OrderDeliveryStatus::PENDING);

    $this->artisan('sales:refresh-delivery-status', ['--apply' => true, '--company' => [$stale->company_id]])
        ->assertSuccessful();
    expect($stale->refresh()->delivery_status)->toBe(OrderDeliveryStatus::NO);
});
