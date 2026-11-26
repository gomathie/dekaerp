<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Filament\Resources\InvoiceResource\Pages\EditInvoice;
use Webkul\Account\Models\Move;
use Webkul\Inventory\Enums\DeliveryStep;
use Webkul\Inventory\Enums\ReceptionStep;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\Support\PluginAccess;
use Webkul\Product\Enums\ProductType;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../inventories/tests/Helpers/InventoryHelper.php';
require_once __DIR__.'/../../Helpers/SaleHelper.php';

/**
 * Services, not only goods (plan 10, user decision 2026-11-25): "Create
 * sales order" and "Customer has taken the goods" appear on a customer
 * invoice only while a line holds a product that makes a delivery. An
 * invoice of services only shows neither.
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
});

/**
 * A draft customer invoice with no order, one line of this product: built
 * through an order whose links are then removed.
 */
function draftInvoiceSelling($product): Move
{
    $order = SaleHelper::order(['warehouse_id' => test()->warehouse->id]);
    SaleHelper::line($order, $product, 1, 80);
    SaleHelper::compute($order);

    $invoice = SaleHelper::createInvoice($order);

    DB::table('sales_order_invoices')->where('move_id', $invoice->id)->delete();
    DB::table('sales_order_line_invoices')
        ->whereIn('invoice_line_id', $invoice->lines()->pluck('id'))
        ->delete();
    $order->forceDelete();

    $invoice->lines()->where('display_type', 'product')->update(['quantity' => 1]);
    AccountFacade::computeAccountMove($invoice->refresh());

    return $invoice->refresh();
}

it('offers the sales order and goods boxes on an invoice that sells goods', function () {
    $invoice = draftInvoiceSelling(SaleHelper::product(['type' => ProductType::GOODS]));

    Livewire::test(EditInvoice::class, ['record' => $invoice->id])
        ->assertFormFieldVisible('create_sale_order')
        ->fillForm(['create_sale_order' => true])
        ->assertFormFieldVisible('goods_delivered_now');
});

it('shows none of the goods options on an invoice of services only', function () {
    $invoice = draftInvoiceSelling(SaleHelper::product(['type' => ProductType::SERVICE, 'is_storable' => false]));

    Livewire::test(EditInvoice::class, ['record' => $invoice->id])
        ->assertFormFieldHidden('create_sale_order')
        ->fillForm(['create_sale_order' => true])
        ->assertFormFieldHidden('goods_delivered_now');
});
