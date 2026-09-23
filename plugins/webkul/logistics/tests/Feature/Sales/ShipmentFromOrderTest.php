<?php

use Webkul\Logistics\Enums\ShipmentState;
use Webkul\Logistics\Models\Shipment;
use Webkul\Logistics\Services\ShipmentFromOrder;
use Webkul\Logistics\Support\LogisticsAccess;
use Webkul\Partner\Models\Partner;
use Webkul\Product\Models\Product;
use Webkul\Sale\Events\OrderConfirmed;
use Webkul\Sale\Models\Order;
use Webkul\Sale\Models\OrderLine;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../Helpers/LogisticsHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('sales');

    LogisticsHelper::install();
});

/**
 * One of the six LOG-* service products CompanyProvisioner creates per company.
 */
function serviceProduct(Company $company, string $reference = 'LOG-FREIGHT'): Product
{
    return Product::withoutGlobalScopes()
        ->where('company_id', $company->id)
        ->where('reference', $reference)
        ->firstOrFail();
}

function salesOrder(Company $company, array $overrides = []): Order
{
    $partner = Partner::factory()->create(['company_id' => $company->id]);

    return Order::factory()->create(array_merge([
        'company_id'  => $company->id,
        'partner_id'  => $partner->id,
        'currency_id' => $company->currency_id,
        'date_order'  => now(),
    ], $overrides));
}

function orderLine(Order $order, Product $product, array $overrides = []): OrderLine
{
    return OrderLine::factory()->create(array_merge([
        'order_id'        => $order->id,
        'company_id'      => $order->company_id,
        'currency_id'     => $order->currency_id,
        'product_id'      => $product->id,
        'product_uom_id'  => $product->uom_id,
        'product_uom_qty' => 3,
        'product_qty'     => 3,
        'price_unit'      => 250,
        'discount'        => 0,
        'name'            => 'Freight, Tema to Kumasi',
    ], $overrides));
}

/**
 * An order for a Logistics company, carrying one freight line.
 */
function logisticsOrder(Company $company): Order
{
    $order = salesOrder($company);

    orderLine($order, serviceProduct($company));

    return $order->refresh();
}

it('creates a draft shipment carrying the order’s customer, currency and charges', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());

    $order = logisticsOrder($company);

    $shipment = app(ShipmentFromOrder::class)->convert($order);

    expect($shipment)->not->toBeNull()
        // Draft, never confirmed: operations still has to accept the work.
        ->and($shipment->state)->toBe(ShipmentState::DRAFT)
        ->and($shipment->company_id)->toBe($company->id)
        ->and($shipment->customer_id)->toBe($order->partner_id)
        ->and($shipment->currency_id)->toBe($order->currency_id)
        ->and($shipment->sale_order_id)->toBe($order->id);

    $charge = $shipment->charges()->sole();

    expect($charge->description)->toBe('Freight, Tema to Kumasi')
        ->and((float) $charge->quantity)->toBe(3.0)
        ->and((float) $charge->price_unit)->toBe(250.0)
        ->and($charge->company_id)->toBe($company->id)
        // Sales already bills this order. Billing it again from Logistics
        // would invoice the customer twice for the same work.
        ->and($charge->is_billable)->toBeFalse();
});

it('does nothing for a company that has not enabled Logistics', function () {
    $sellingCompany = LogisticsHelper::company();

    // The service products only exist for enabled companies, so this company
    // could not carry a LOG-* line in the first place - but the switch is what
    // must stop it, and this proves the switch is what is being read.
    $logisticsCompany = LogisticsHelper::enable(LogisticsHelper::company());

    $order = salesOrder($sellingCompany);

    orderLine($order, serviceProduct($logisticsCompany));

    expect(app(ShipmentFromOrder::class)->convert($order))->toBeNull()
        ->and(Shipment::withoutGlobalScopes()->count())->toBe(0);
});

it('stops when a company switches Logistics back off', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());

    $order = logisticsOrder($company);

    LogisticsHelper::disable($company);

    LogisticsAccess::flush();

    expect(app(ShipmentFromOrder::class)->convert($order))->toBeNull();
});

it('ignores an order with no logistics services on it', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());

    $order = salesOrder($company);

    orderLine($order, Product::factory()->create(['company_id' => $company->id]));

    expect(app(ShipmentFromOrder::class)->convert($order))->toBeNull();
});

it('ignores another company’s logistics products on the order', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());
    $other = LogisticsHelper::enable(LogisticsHelper::company());

    $order = salesOrder($company);

    // Same LOG-FREIGHT reference, but provisioned for a different company.
    orderLine($order, serviceProduct($other));

    expect(app(ShipmentFromOrder::class)->convert($order))->toBeNull();
});

it('converts an order only once, however many times it is confirmed', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());

    $order = logisticsOrder($company);

    $converter = app(ShipmentFromOrder::class);

    expect($converter->convert($order))->not->toBeNull()
        ->and($converter->convert($order))->toBeNull()
        ->and(Shipment::withoutGlobalScopes()->where('sale_order_id', $order->id)->count())->toBe(1);
});

it('does not re-convert an order whose shipment belongs to a company the session cannot see', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());
    $elsewhere = LogisticsHelper::enable(LogisticsHelper::company());

    $order = logisticsOrder($company);

    app(ShipmentFromOrder::class)->convert($order);

    // Someone from another company confirms - the duplicate check must still
    // find the existing shipment, which the company scope would hide.
    CompanyHelper::actingAsCompanyUser($elsewhere, ['view_any_logistics_shipment']);

    expect(app(ShipmentFromOrder::class)->convert($order))->toBeNull()
        ->and(Shipment::withoutGlobalScopes()->where('sale_order_id', $order->id)->count())->toBe(1);
});

it('converts an old quotation confirmed after the company adopted Logistics', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());

    // Drafted weeks before the company enabled Logistics, confirmed today.
    // This is the work an adopting company has in hand, not history.
    $order = salesOrder($company, ['date_order' => now()->subMonth()]);

    orderLine($order, serviceProduct($company));

    expect(app(ShipmentFromOrder::class)->convert($order))->not->toBeNull();
});

it('creates the shipment when Sales dispatches OrderConfirmed', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());

    $order = logisticsOrder($company);

    // OrderWorkflow::confirm() dispatches this event; the listener is what
    // WP-9 registers, so this is the whole integration minus Sales' own work.
    OrderConfirmed::dispatch($order);

    expect(Shipment::withoutGlobalScopes()->where('sale_order_id', $order->id)->count())->toBe(1);
});

it('never lets a Logistics failure break confirming a sales order', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());

    $order = logisticsOrder($company);

    $this->mock(ShipmentFromOrder::class)
        ->shouldReceive('convert')
        ->andThrow(new RuntimeException('shipment numbering unavailable'));

    // The order is the customer's commitment; the shipment is a convenience.
    OrderConfirmed::dispatch($order);

    expect(Shipment::withoutGlobalScopes()->count())->toBe(0);
});
