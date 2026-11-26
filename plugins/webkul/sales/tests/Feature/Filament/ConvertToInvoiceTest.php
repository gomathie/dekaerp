<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Account\Enums\DisplayType;
use Webkul\Account\Enums\InvoicePolicy;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Models\Move;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\Support\PluginAccess;
use Webkul\Sale\Enums\AdvancedPayment;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Filament\Clusters\Orders\Resources\OrderInvoiceResource;
use Webkul\Sale\Filament\Clusters\Orders\Resources\OrderResource;
use Webkul\Sale\Filament\Clusters\Orders\Resources\OrderResource\Pages\ViewOrder;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Actions\ConfirmAction;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Actions\ConvertToInvoiceAction;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Actions\CreateInvoiceAction;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Pages\EditQuotation;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Pages\ViewQuotation;
use Webkul\Sale\Models\Order;
use Webkul\Support\Models\Currency;
use Webkul\Support\Models\CurrencyRate;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../Helpers/SaleHelper.php';

/**
 * "Convert to invoice" on a quotation (TRACE365, user 2026-11-25): one click
 * confirms the quotation, creates its regular invoice through the same paths
 * as Confirm and Create invoice, and opens the draft invoice for checking.
 */
beforeEach(function () {
    foreach (['inventories', 'invoices', 'sales', 'accounts'] as $plugin) {
        TestBootstrapHelper::ensurePluginInstalled($plugin);

        DB::table('plugins')->updateOrInsert(
            ['name' => $plugin],
            ['is_installed' => true, 'is_active' => true, 'updated_at' => now()],
        );
    }

    Package::$plugins = Plugin::all()->keyBy('name');
    PluginAccess::flush();

    // Panel routes are not always registered when this file runs alone.
    // Spell the route name and its parameters into the URL instead of '#',
    // so a redirect still shows where it would have gone.
    URL::resolveMissingNamedRoutesUsing(fn (string $name, mixed $parameters = []): string => '/'.$name.'?'.http_build_query(
        collect((array) $parameters)->map(fn ($value) => $value instanceof Model ? $value->getRouteKey() : $value)->all()
    ));
});

/** Everything a salesperson who also invoices holds. */
function convertingSalesperson(): array
{
    return [
        'view_any_sale_quotation',
        'update_sale_quotation',
        'view_any_sale_order',
        'update_sale_order',
        'create_invoice_invoice',
    ];
}

function convertibleQuotation(?int $userId, InvoicePolicy $policy = InvoicePolicy::ORDER): Order
{
    $order = SaleHelper::order(['state' => OrderState::DRAFT, 'user_id' => $userId]);

    SaleHelper::line($order, SaleHelper::product(['invoice_policy' => $policy->value]), qty: 2, priceUnit: 100);

    return SaleHelper::compute($order)->refresh();
}

it('confirms the quotation and opens one draft invoice with its lines and confirmed rate', function () {
    $user = FilamentHelper::actingAs(convertingSalesperson());

    $company = SaleHelper::company();
    $foreign = Currency::query()->whereKeyNot($company->currency_id)->firstOrFail();
    $foreign->forceFill(['active' => true])->save();
    CurrencyRate::factory()->create([
        'currency_id' => $foreign->id,
        'company_id'  => $company->id,
        'rate'        => 10.0,
        'name'        => now()->subDay()->toDateString(),
    ]);

    $order = SaleHelper::order(['state' => OrderState::DRAFT, 'user_id' => $user->id, 'currency_id' => $foreign->id, 'date_order' => now()]);
    $order->forceFill([
        'currency_rate'              => 12.5,
        'currency_rate_source'       => Move::RATE_MANUAL,
        'currency_rate_confirmed_by' => $user->id,
    ])->save();
    SaleHelper::line($order, SaleHelper::product(['invoice_policy' => InvoicePolicy::ORDER->value]), qty: 2, priceUnit: 100);
    SaleHelper::compute($order);

    $component = Livewire::test(EditQuotation::class, ['record' => $order->id])
        ->assertActionVisible(ConvertToInvoiceAction::class)
        ->callAction(ConvertToInvoiceAction::class);

    $order->refresh();
    $invoices = $order->accountMoves()->get();
    $invoice = $invoices->first();
    $productLines = $invoice->lines()->where('display_type', DisplayType::PRODUCT)->get();

    expect($order->state)->toBe(OrderState::SALE)
        ->and($invoices)->toHaveCount(1)
        ->and($invoice->state)->toBe(MoveState::DRAFT)
        ->and($invoice->move_type)->toBe(MoveType::OUT_INVOICE)
        ->and($productLines)->toHaveCount(1)
        ->and((float) $productLines->first()->quantity)->toBe(2.0)
        ->and((float) $productLines->first()->price_unit)->toBe(100.0)
        ->and((float) $invoice->invoice_currency_rate)->toBe(12.5)
        ->and($invoice->currency_rate_source)->toBe(Move::RATE_MANUAL);

    // The invoice's edit page under the order, as the order's Invoices tab
    // opens it, with the parameter name that nested route expects.
    expect(OrderInvoiceResource::getParentResourceRegistration()->getParentRouteParameterName())->toBe('sales_order');

    $invoiceUrl = OrderInvoiceResource::getUrl('edit', ['sales_order' => $order, 'record' => $invoice]);

    expect($invoiceUrl)->toContain('invoices')->toContain('edit')->toContain((string) $invoice->id);

    $component->assertRedirect($invoiceUrl);
});

it('offers convert to invoice next to confirm on a draft quotation, on its view page too', function () {
    $user = FilamentHelper::actingAs([...convertingSalesperson(), 'view_sale_quotation']);

    $quotation = convertibleQuotation($user->id);

    Livewire::test(ViewQuotation::class, ['record' => $quotation->id])
        ->assertActionVisible(ConfirmAction::class)
        ->assertActionVisible(ConvertToInvoiceAction::class);
});

it('hides convert to invoice from a user who may not create invoices', function () {
    $user = FilamentHelper::actingAs(array_values(array_diff(convertingSalesperson(), ['create_invoice_invoice'])));

    $quotation = convertibleQuotation($user->id);

    Livewire::test(EditQuotation::class, ['record' => $quotation->id])
        ->assertActionVisible(ConfirmAction::class)
        ->assertActionHidden(ConvertToInvoiceAction::class);
});

it('hides convert to invoice once the quotation is a confirmed order', function () {
    $user = FilamentHelper::actingAs(convertingSalesperson());

    $order = SaleHelper::confirm(convertibleQuotation($user->id));

    Livewire::test(EditQuotation::class, ['record' => $order->id])
        ->assertActionHidden(ConvertToInvoiceAction::class);
});

it('hides convert to invoice when the company does not have the invoices module', function () {
    $user = FilamentHelper::actingAs(convertingSalesperson());

    $quotation = convertibleQuotation($user->id);

    DB::table('company_plugin')
        ->where('company_id', $quotation->company_id)
        ->where('plugin_id', Plugin::query()->where('name', 'invoices')->value('id'))
        ->delete();
    PluginAccess::flush();

    Livewire::test(EditQuotation::class, ['record' => $quotation->id])
        ->assertActionHidden(ConvertToInvoiceAction::class);

    expect($quotation->refresh()->state)->toBe(OrderState::DRAFT)
        ->and($quotation->accountMoves()->count())->toBe(0);
});

it('only confirms when nothing can be invoiced yet and makes no empty invoice', function () {
    $user = FilamentHelper::actingAs(convertingSalesperson());

    $quotation = convertibleQuotation($user->id, InvoicePolicy::DELIVERY);

    Livewire::test(EditQuotation::class, ['record' => $quotation->id])
        ->callAction(ConvertToInvoiceAction::class)
        ->assertNotified(__('sales::filament/clusters/orders/resources/quotation/actions/create-invoice.notification.no-invoiceable-lines.title'))
        ->assertRedirect(OrderResource::getUrl('edit', ['record' => $quotation]));

    expect(OrderResource::getUrl('edit', ['record' => $quotation]))->not->toContain('invoices');

    expect($quotation->refresh()->state)->toBe(OrderState::SALE)
        ->and($quotation->accountMoves()->count())->toBe(0);
});

/**
 * Create invoice on a confirmed order (TRACE365, user 2026-11-25): the user
 * used to stay on the order and had to find the new invoice. It opens now.
 */
it('opens the new invoice after creating it from a confirmed order', function () {
    $user = FilamentHelper::actingAs(['view_any_sale_order', 'view_sale_order']);

    $order = SaleHelper::confirm(convertibleQuotation($user->id));

    $component = Livewire::test(ViewOrder::class, ['record' => $order->id])
        ->callAction(CreateInvoiceAction::class, data: [
            'advance_payment_method' => AdvancedPayment::DELIVERED->value,
        ]);

    $invoices = $order->refresh()->accountMoves()->get();
    $invoiceUrl = OrderInvoiceResource::getUrl('edit', ['sales_order' => $order, 'record' => $invoices->first()]);

    expect($invoices)->toHaveCount(1)
        ->and($invoiceUrl)->toContain('invoices')->toContain((string) $invoices->first()->id);

    $component->assertRedirect($invoiceUrl);
});
