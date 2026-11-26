<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Webkul\Account\Enums\InvoicePolicy;
use Webkul\Inventory\Enums\DeliveryStep;
use Webkul\Inventory\Enums\ReceptionStep;
use Webkul\Inventory\Facades\Inventory;
use Webkul\Inventory\Models\Operation;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\Support\PluginAccess;
use Webkul\Sale\Enums\InvoiceStatus;
use Webkul\Sale\Enums\OrderDeliveryStatus;
use Webkul\Sale\Models\Order;
use Webkul\Security\Enums\PermissionType;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../inventories/tests/Helpers/InventoryHelper.php';
require_once __DIR__.'/../../Helpers/SaleHelper.php';

/**
 * 2026-11-26: an order is recomputed by listeners running as whoever acted.
 * Order, Move and Operation carry the ownership scope, so when that user saw
 * only their own records the listeners found no order, or the recompute read
 * the order's lines and deliveries as missing. Both now read past the scope,
 * by the order's own keys (OrderCalculator, ComputeSaleOrder*Listener).
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
    $this->product = SaleHelper::product(['invoice_policy' => InvoicePolicy::ORDER->value]);
});

/** A member of the order's company who sees only records they own. */
function actingAsOwnRecordsOnly(): void
{
    $user = CompanyHelper::actingAsCompanyUser(SaleHelper::company(), []);
    $user->forceFill(['resource_permission' => PermissionType::INDIVIDUAL])->saveQuietly();
}

it('recomputes the order when someone who sees only their own records cancels its invoice', function () {
    $order = SaleHelper::confirmedOrder($this->warehouse, $this->product, 2, 50);
    $invoice = SaleHelper::postInvoice(SaleHelper::createInvoice($order));

    expect($order->refresh()->invoice_status)->toBe(InvoiceStatus::INVOICED);

    actingAsOwnRecordsOnly();

    SaleHelper::cancelInvoice($invoice);

    expect(Order::withoutGlobalScopes()->find($order->id)->invoice_status)->toBe(InvoiceStatus::TO_INVOICE);
});

it('recomputes the order when someone who sees only their own records cancels its delivery', function () {
    $order = SaleHelper::confirmedOrder($this->warehouse, $this->product, 2, 50);

    expect($order->refresh()->delivery_status)->not->toBe(OrderDeliveryStatus::NO);

    $deliveries = Operation::withoutGlobalScopes()->where('sale_order_id', $order->id)->get();

    actingAsOwnRecordsOnly();

    $deliveries->each(fn (Operation $operation) => Inventory::cancelTransfer($operation));

    expect(Order::withoutGlobalScopes()->find($order->id)->delivery_status)->toBe(OrderDeliveryStatus::NO);
});
