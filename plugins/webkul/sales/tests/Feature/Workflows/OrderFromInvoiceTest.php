<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Filament\Resources\InvoiceResource;
use Webkul\Account\Filament\Resources\InvoiceResource\Pages\CreateInvoice;
use Webkul\Account\Models\Move;
use Webkul\Account\Services\WalkInCustomer;
use Webkul\Inventory\Enums\DeliveryStep;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Enums\ReceptionStep;
use Webkul\Inventory\Models\Move as InventoryMove;
use Webkul\Inventory\Models\ProductQuantity;
use Webkul\Inventory\Models\Warehouse;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\Support\PluginAccess;
use Webkul\Sale\Enums\InvoiceStatus;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Models\InvoiceOrderLink;
use Webkul\Sale\Models\Order;
use Webkul\Sale\Services\OrderFromInvoice;
use Webkul\Sale\Settings\QuotationAndOrderSettings;
use Webkul\Sale\Support\DirectInvoices;
use Webkul\Support\Models\Currency;
use Webkul\Support\Models\CurrencyRate;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../inventories/tests/Helpers/InventoryHelper.php';
require_once __DIR__.'/../../Helpers/SaleHelper.php';

/**
 * "Create sales order" on a customer invoice (user, 2026-11-22): a direct
 * invoice gets a confirmed sales order of its own when it is confirmed, the
 * sale is counted once, and the order follows the invoice when it is
 * cancelled or reset.
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
 * A draft customer invoice with real computed lines and no order: built
 * through an order, whose links are then removed, so it is exactly what a
 * direct invoice looks like.
 */
function directDraftInvoice($product, float $qty = 3, float $price = 50): Move
{
    $order = SaleHelper::order(['warehouse_id' => test()->warehouse->id]);
    SaleHelper::line($order, $product, $qty, $price);
    SaleHelper::compute($order);

    $invoice = SaleHelper::createInvoice($order);

    DB::table('sales_order_invoices')->where('move_id', $invoice->id)->delete();
    DB::table('sales_order_line_invoices')
        ->whereIn('invoice_line_id', $invoice->lines()->pluck('id'))
        ->delete();
    $order->forceDelete();

    // Invoiced "as delivered" with nothing delivered yet gives zero
    // quantities; a direct invoice carries the sold quantity.
    $invoice->lines()->where('display_type', 'product')->update(['quantity' => $qty]);
    AccountFacade::computeAccountMove($invoice->refresh());

    return $invoice->refresh();
}

function ordersMadeFrom(Move $invoice)
{
    return Order::withoutGlobalScopes()
        ->whereHas('accountMoves', fn ($q) => $q->whereKey($invoice->id))
        ->get();
}

it('makes a confirmed, invoiced sales order when the invoice is confirmed', function () {
    $invoice = directDraftInvoice($this->product, 3, 50);

    app(OrderFromInvoice::class)->request($invoice, auth()->id());

    expect(ordersMadeFrom($invoice))->toHaveCount(0);

    SaleHelper::postInvoice($invoice);

    $orders = ordersMadeFrom($invoice);
    expect($orders)->toHaveCount(1);

    $order = $orders->first()->load('lines.accountMoveLines');

    expect($order->state)->toBe(OrderState::SALE)
        ->and($order->invoice_status)->toBe(InvoiceStatus::INVOICED)
        ->and($order->partner_id)->toBe($invoice->partner_id)
        ->and($order->origin)->toBe($invoice->refresh()->name)
        ->and((float) $order->amount_untaxed)->toBe((float) $invoice->amount_untaxed)
        ->and($order->lines)->toHaveCount(1)
        ->and((float) $order->lines->first()->product_uom_qty)->toBe(3.0)
        ->and((float) $order->lines->first()->qty_invoiced)->toBe(3.0)
        ->and($order->lines->first()->accountMoveLines->pluck('move_id')->all())->toBe([$invoice->id])
        ->and(InvoiceOrderLink::query()->where('move_id', $invoice->id)->value('order_id'))->toBe($order->id);
});

/**
 * A foreign-currency order's company value (TRACE365, 2026-11-24).
 * `sales_orders.currency_rate` was never set, so it stayed 0 and every figure
 * dividing by it took the order's own amount as company currency.
 */
function foreignSaleCurrency(float $rate): Currency
{
    $company = SaleHelper::company();
    $foreign = Currency::query()->where('id', '!=', $company->currency_id)->firstOrFail();
    $foreign->forceFill(['active' => true])->save();

    CurrencyRate::factory()->create([
        'currency_id' => $foreign->id,
        'company_id'  => $company->id,
        'rate'        => $rate,
        'name'        => now()->subDay()->toDateString(),
    ]);

    return $foreign;
}

it('gives a sales order in another currency the day\'s rate, and one in the company currency 1', function () {
    $foreign = foreignSaleCurrency(11.8);

    // Dated today: the factory picks a random day in the last month, before
    // yesterday's rate, which would leave no rate to find.
    $foreignOrder = SaleHelper::order(['warehouse_id' => $this->warehouse->id, 'currency_id' => $foreign->id, 'date_order' => now()]);
    SaleHelper::line($foreignOrder, $this->product, 2, 59);
    SaleHelper::compute($foreignOrder);

    $baseOrder = SaleHelper::order(['warehouse_id' => $this->warehouse->id, 'currency_id' => SaleHelper::company()->currency_id, 'date_order' => now()]);
    SaleHelper::line($baseOrder, $this->product, 1, 10);
    SaleHelper::compute($baseOrder);

    expect((float) $foreignOrder->refresh()->currency_rate)->toBe(11.8)
        // 118 in the order's currency is 10 in the company's.
        ->and(round((float) $foreignOrder->amount_total / (float) $foreignOrder->currency_rate, 2))->toBe(10.0)
        ->and((float) $baseOrder->refresh()->currency_rate)->toBe(1.0);
});

it('gives an order made from a foreign-currency invoice that invoice\'s rate', function () {
    $foreign = foreignSaleCurrency(11.8);

    $invoice = directDraftInvoice($this->product, 2, 59);
    $invoice->update(['currency_id' => $foreign->id]);
    AccountFacade::computeAccountMove($invoice->refresh());

    app(OrderFromInvoice::class)->request($invoice->refresh(), auth()->id());
    SaleHelper::postInvoice($invoice);

    $order = ordersMadeFrom($invoice)->firstOrFail();

    expect((float) $invoice->refresh()->invoice_currency_rate)->toBe(11.8)
        ->and($order->currency_id)->toBe($foreign->id)
        ->and((float) $order->currency_rate)->toBe((float) $invoice->invoice_currency_rate);
});

it('counts the sale once: the invoice leaves the direct-invoice figures', function () {
    $invoice = directDraftInvoice($this->product);

    app(OrderFromInvoice::class)->request($invoice);
    SaleHelper::postInvoice($invoice);

    expect(DirectInvoices::invoices()->whereKey($invoice->id)->exists())->toBeFalse();
});

it('delivers a stocked product automatically when the stock is there', function () {
    ProductQuantity::factory()->create([
        'product_id'        => $this->product->id,
        // The order takes the company's first warehouse (Order::computeWarehouseId).
        'location_id'       => Warehouse::query()->where('company_id', SaleHelper::company()->id)->orderBy('id')->value('lot_stock_location_id'),
        'quantity'          => 10,
        'reserved_quantity' => 0,
        'incoming_at'       => now(),
        'company_id'        => SaleHelper::company()->id,
    ]);

    $invoice = directDraftInvoice($this->product, 3);

    app(OrderFromInvoice::class)->request($invoice);
    SaleHelper::postInvoice($invoice);

    $order = ordersMadeFrom($invoice)->first();

    expect($order->operations()->count())->toBeGreaterThan(0)
        ->and($order->operations()->where('state', '!=', OperationState::DONE)->count())->toBe(0)
        ->and((float) $order->lines()->first()->qty_delivered)->toBe(3.0);
});

it('leaves the delivery open when the stock is short, and still confirms the invoice', function () {
    $invoice = directDraftInvoice($this->product, 3);

    app(OrderFromInvoice::class)->request($invoice);
    SaleHelper::postInvoice($invoice);

    $order = ordersMadeFrom($invoice)->first();

    expect($invoice->refresh()->state->value)->toBe('posted')
        ->and($order->operations()->count())->toBeGreaterThan(0)
        ->and($order->operations()->where('state', OperationState::DONE)->count())->toBe(0);
});

it('makes no order when the box was not ticked', function () {
    $invoice = directDraftInvoice($this->product);

    SaleHelper::postInvoice($invoice);

    expect(ordersMadeFrom($invoice))->toHaveCount(0)
        ->and(DirectInvoices::invoices()->whereKey($invoice->id)->exists())->toBeTrue();
});

it('cancels the order and its open delivery when the invoice is cancelled', function () {
    $invoice = directDraftInvoice($this->product);

    app(OrderFromInvoice::class)->request($invoice);
    SaleHelper::postInvoice($invoice);
    $order = ordersMadeFrom($invoice)->first();

    SaleHelper::cancelInvoice($invoice);

    $order->refresh();

    expect($order->state)->toBe(OrderState::CANCEL)
        ->and($order->operations()->where('state', '!=', OperationState::CANCELED)->count())->toBe(0);
});

it('makes a fresh order matching the invoice after a reset and a new confirm', function () {
    $invoice = directDraftInvoice($this->product, 3, 50);

    app(OrderFromInvoice::class)->request($invoice);
    SaleHelper::postInvoice($invoice);
    $first = ordersMadeFrom($invoice)->first();

    SaleHelper::resetInvoiceToDraft($invoice);

    expect($first->refresh()->state)->toBe(OrderState::CANCEL);

    $invoice->refresh()->lines()->where('display_type', 'product')->update(['quantity' => 5]);
    SaleHelper::postInvoice($invoice);

    $live = ordersMadeFrom($invoice)->where('state', '!=', OrderState::CANCEL);

    expect($live)->toHaveCount(1)
        ->and($live->first()->id)->not->toBe($first->id)
        ->and((float) $live->first()->lines()->first()->product_uom_qty)->toBe(5.0);
});

it('confirms the invoice without an order when Sales was revoked for the company', function () {
    $invoice = directDraftInvoice($this->product);

    app(OrderFromInvoice::class)->request($invoice);

    DB::table('company_plugin')
        ->where('company_id', $invoice->company_id)
        ->where('plugin_id', Plugin::query()->where('name', 'sales')->value('id'))
        ->delete();
    PluginAccess::flush();

    SaleHelper::postInvoice($invoice);

    expect($invoice->refresh()->state->value)->toBe('posted')
        ->and(ordersMadeFrom($invoice))->toHaveCount(0);
});

/**
 * Options A + E (user, 2026-11-22): "Customer has taken the goods" unticked
 * leaves the delivery for the warehouse even with stock, and "Mark as
 * delivered" on the invoice page records it later.
 */
it('leaves the delivery for the warehouse when the goods were not taken, then marks it delivered', function () {
    ProductQuantity::factory()->create([
        'product_id'        => $this->product->id,
        // The order takes the company's first warehouse (Order::computeWarehouseId).
        'location_id'       => Warehouse::query()->where('company_id', SaleHelper::company()->id)->orderBy('id')->value('lot_stock_location_id'),
        'quantity'          => 10,
        'reserved_quantity' => 0,
        'incoming_at'       => now(),
        'company_id'        => SaleHelper::company()->id,
    ]);

    $invoice = directDraftInvoice($this->product, 3);

    app(OrderFromInvoice::class)->request($invoice, null, deliverNow: false);
    SaleHelper::postInvoice($invoice);

    $order = ordersMadeFrom($invoice)->first();

    expect($order->operations()->where('state', OperationState::DONE)->count())->toBe(0)
        ->and(app(OrderFromInvoice::class)->pendingDeliveries($order))->toBeGreaterThan(0);

    expect(app(OrderFromInvoice::class)->markDelivered($invoice->refresh()))->toBeTrue()
        ->and(app(OrderFromInvoice::class)->pendingDeliveries($order))->toBe(0)
        ->and((float) $order->lines()->first()->qty_delivered)->toBe(3.0);
});

/**
 * "New direct sale" (user, 2026-11-22): the invoice form opens with the
 * walk-in customer, "Paid now", the order and the delivery all set, so a
 * counter sale is one save. A plain new invoice keeps the order box clear.
 */
it('opens a direct sale with every box set and a plain invoice without', function () {
    Livewire::withQueryParams(['direct_sale' => 1])
        ->test(CreateInvoice::class)
        ->assertFormSet([
            'paid_now'            => true,
            'create_sale_order'   => true,
            'goods_delivered_now' => true,
            'partner_id'          => app(WalkInCustomer::class)->forCompany((int) current_company_id())->getKey(),
        ]);
});

// Its own test: withQueryParams() leaves the flag on the request for the
// rest of a test, which no real request ever would.
it('opens a plain new invoice with the order box clear', function () {
    Livewire::test(CreateInvoice::class)
        ->assertFormSet(['create_sale_order' => false, 'paid_now' => false]);
});

it('keeps one walk-in customer per company, found by its reference', function () {
    $first = app(WalkInCustomer::class)->forCompany((int) current_company_id());
    $again = app(WalkInCustomer::class)->forCompany((int) current_company_id());

    expect($again->getKey())->toBe($first->getKey())
        ->and($first->reference)->toBe(WalkInCustomer::REFERENCE)
        ->and((int) $first->company_id)->toBe((int) current_company_id());
});

it('takes the company setting as the default answer', function () {
    $settings = app(QuotationAndOrderSettings::class);

    expect($settings->goods_leave_on_invoice)->toBeTrue();
});

/**
 * Backfill (user, 2026-11-22): past direct invoices get an order, stock is
 * left unchanged, a dry run writes nothing, and a second run adds nothing.
 */
it('backfills orders for past invoices without touching stock', function () {
    $invoice = directDraftInvoice($this->product, 3);
    SaleHelper::postInvoice($invoice);

    $this->artisan('sales:backfill-orders-from-invoices')->assertSuccessful();
    expect(ordersMadeFrom($invoice))->toHaveCount(0);

    $this->artisan('sales:backfill-orders-from-invoices', ['--apply' => true])->assertSuccessful();

    $order = ordersMadeFrom($invoice)->first();

    expect($order)->not->toBeNull()
        ->and($order->state)->toBe(OrderState::SALE)
        ->and($order->operations()->whereNotIn('state', [OperationState::CANCELED])->count())->toBe(0)
        ->and(DirectInvoices::invoices()->whereKey($invoice->id)->exists())->toBeFalse();

    $this->artisan('sales:backfill-orders-from-invoices', ['--apply' => true])->assertSuccessful();
    expect(ordersMadeFrom($invoice))->toHaveCount(1);
});

/**
 * Plan #9 R-5 (user, 2026-11-25: "count, no stock"): a line without a
 * product stays out of the order made from the invoice and out of its
 * delivery, and is counted once, from the invoice.
 */
it('leaves a line without a product out of the order and its delivery, and counts it once from the invoice', function () {
    $invoice = directDraftInvoice($this->product, 3, 50);

    $survey = $invoice->invoiceLines()->create(InvoiceResource::prepareLineWithoutProduct([
        'name'        => 'Site survey',
        'account_id'  => $invoice->journal->default_account_id,
        'quantity'    => 1,
        'price_unit'  => 40,
        'discount'    => 0,
        'currency_id' => $invoice->currency_id,
    ]));
    AccountFacade::computeAccountMove($invoice->refresh());

    app(OrderFromInvoice::class)->request($invoice, auth()->id());
    SaleHelper::postInvoice($invoice);

    $order = ordersMadeFrom($invoice)->first()->load('lines');

    expect((float) $invoice->refresh()->amount_untaxed)->toBe(190.0)
        ->and($order->lines)->toHaveCount(1)
        ->and($order->lines->first()->product_id)->toBe($this->product->id)
        ->and((float) $order->amount_untaxed)->toBe(150.0)
        ->and(InventoryMove::query()->withoutGlobalScopes()->whereNull('product_id')->exists())->toBeFalse()
        ->and(InventoryMove::query()->withoutGlobalScopes()->where('sale_order_line_id', $order->lines->first()->id)->exists())->toBeTrue();

    // Counted once: from the invoice, as the order has no line for it.
    $counted = DirectInvoices::soldLines(DirectInvoices::postedInvoices())
        ->where('accounts_account_move_lines.move_id', $invoice->id)
        ->pluck('accounts_account_move_lines.id')
        ->all();

    expect($counted)->toBe([$survey->id]);
});
