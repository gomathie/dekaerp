<?php

use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Radio;
use Filament\Schemas\Components\Text;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Account\Enums\InvoicePolicy;
use Webkul\Account\Events\InvoicesDeletedFromScreen;
use Webkul\Account\Filament\Resources\InvoiceResource\Pages\EditInvoice;
use Webkul\Account\Filament\Resources\InvoiceResource\Pages\ListInvoices;
use Webkul\Account\Filament\Resources\InvoiceResource\Support\InvoiceSchemaRegistry;
use Webkul\Account\Models\Move;
use Webkul\Inventory\Enums\DeliveryStep;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Enums\ReceptionStep;
use Webkul\Inventory\Models\Operation;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\Support\PluginAccess;
use Webkul\Sale\Enums\InvoiceStatus;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Models\Order;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../inventories/tests/Helpers/InventoryHelper.php';
require_once __DIR__.'/../../Helpers/SaleHelper.php';

/**
 * Production, 2026-11-25: quotation, confirmed order with a delivery,
 * "Create invoice", then the draft invoice deleted. The order stayed
 * "Invoiced" with no invoice behind it, and its delivery kept reserving
 * stock. User decision: the order is recomputed whenever such an invoice is
 * deleted, and the delete confirmation asks whether to cancel the order and
 * its delivery (no mail), only of someone allowed to change the order.
 */
beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('inventories');
    TestBootstrapHelper::ensurePluginInstalled('sales');

    foreach (['inventories', 'sales', 'accounts', 'invoices'] as $plugin) {
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

/**
 * @return array{0: Order, 1: Move}
 */
function orderWithDraftInvoice($product): array
{
    $order = SaleHelper::confirmedOrder(test()->warehouse, $product, 2, 50);
    $invoice = SaleHelper::createInvoice($order);

    expect($order->refresh()->invoice_status)->toBe(InvoiceStatus::INVOICED);

    return [$order, $invoice];
}

/** Read past the scopes: one test acts as a user who sees only their own records. */
function openDeliveries(Order $order): int
{
    return Operation::withoutGlobalScopes()
        ->where('sale_order_id', $order->getKey())
        ->whereNotIn('state', [OperationState::DONE, OperationState::CANCELED])
        ->count();
}

it('puts an order back to invoice when its only invoice is deleted', function () {
    [$order, $invoice] = orderWithDraftInvoice($this->product);

    // From any path (the API deletes the model directly).
    $invoice->delete();

    expect($order->refresh()->state)->toBe(OrderState::SALE)
        ->and($order->invoice_status)->toBe(InvoiceStatus::TO_INVOICE)
        ->and((float) $order->lines()->first()->qty_invoiced)->toBe(0.0)
        ->and(openDeliveries($order))->toBeGreaterThan(0);
});

it('cancels the order and its delivery, sending nothing, when the person answers yes', function () {
    Mail::fake();

    [$order, $invoice] = orderWithDraftInvoice($this->product);

    Livewire::test(EditInvoice::class, ['record' => $invoice->id])
        ->callAction(DeleteAction::class, data: ['cancel_sale_orders' => true])
        ->assertHasNoActionErrors();

    expect(Move::withoutGlobalScopes()->whereKey($invoice->id)->exists())->toBeFalse()
        ->and($order->refresh()->state)->toBe(OrderState::CANCEL)
        ->and(openDeliveries($order))->toBe(0);

    Mail::assertNothingSent();
    Mail::assertNothingQueued();
});

it('keeps the order to invoice later when the person answers no', function () {
    [$order, $invoice] = orderWithDraftInvoice($this->product);

    Livewire::test(EditInvoice::class, ['record' => $invoice->id])
        // "No" as the form sends it: the Yes/No radio's options are 1 and 0.
        ->callAction(DeleteAction::class, data: ['cancel_sale_orders' => 0])
        ->assertHasNoActionErrors();

    expect(Move::withoutGlobalScopes()->whereKey($invoice->id)->exists())->toBeFalse()
        ->and($order->refresh()->state)->toBe(OrderState::SALE)
        ->and($order->invoice_status)->toBe(InvoiceStatus::TO_INVOICE)
        ->and(openDeliveries($order))->toBeGreaterThan(0);
});

it('asks once for every order when invoices are deleted together', function () {
    [$order, $invoice] = orderWithDraftInvoice($this->product);

    Livewire::test(ListInvoices::class)
        ->callTableBulkAction(DeleteBulkAction::class, [$invoice], data: ['cancel_sale_orders' => true]);

    expect(Move::withoutGlobalScopes()->whereKey($invoice->id)->exists())->toBeFalse()
        ->and($order->refresh()->state)->toBe(OrderState::CANCEL)
        ->and(openDeliveries($order))->toBe(0);
});

it('only recomputes the order for someone who may not change it', function () {
    [$order, $invoice] = orderWithDraftInvoice($this->product);

    // May see orders, may not change them.
    CompanyHelper::actingAsCompanyUser(SaleHelper::company(), ['view_any_sale_order', 'view_sale_order']);

    $fields = InvoiceSchemaRegistry::renderForm('delete', [$invoice->refresh()]);

    expect(collect($fields)->contains(fn ($field): bool => $field instanceof Radio))->toBeFalse()
        ->and(collect($fields)->contains(fn ($field): bool => $field instanceof Text))->toBeTrue();

    // A tampered "yes" is refused on the server too.
    $invoice->delete();
    InvoicesDeletedFromScreen::dispatch([$invoice->id], ['cancel_sale_orders' => true]);

    expect($order->refresh()->state)->toBe(OrderState::SALE)
        ->and($order->invoice_status)->toBe(InvoiceStatus::TO_INVOICE)
        ->and(openDeliveries($order))->toBeGreaterThan(0);
});
