<?php

use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Account\Models\Account;
use Webkul\Account\Models\Journal;
use Webkul\Logistics\Filament\Clusters\Finance\Pages\UnbilledCharges;
use Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\Pages\ViewShipment;
use Webkul\Logistics\Models\Shipment;
use Webkul\Logistics\Services\ShipmentInvoicer;
use Webkul\Product\Models\Product;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;

require_once __DIR__.'/../../Helpers/LogisticsHelper.php';

beforeEach(function () {
    LogisticsHelper::install();

    // The panel boots before the plugin is installed in tests, so its routes
    // are missing when this file runs on its own.
    URL::resolveMissingNamedRoutesUsing(fn (): string => '#');
});

/**
 * Same setup as ShipmentInvoicerTest::billableCompany(), under another name:
 * both files declare plain functions, and a shared name would be a fatal
 * redeclaration when the suite loads both.
 */
function invoiceScreenCompany(): Company
{
    $company = LogisticsHelper::company();

    LogisticsHelper::enable($company);

    $currency = Currency::query()->find($company->currency_id) ?? Currency::query()->first();

    Journal::factory()->sale()->create([
        'company_id'         => $company->id,
        'currency_id'        => $currency?->id,
        'default_account_id' => Account::factory()->income()->create([
            'currency_id' => $currency?->id,
        ])->id,
    ]);

    return $company;
}

function invoiceScreenCharge(Shipment $shipment, float $priceUnit = 100, bool $billable = true)
{
    $product = Product::withoutGlobalScopes()
        ->where('company_id', $shipment->company_id)
        ->where('reference', 'LOG-FREIGHT')
        ->first();

    return $shipment->charges()->create([
        'description' => 'Freight',
        'quantity'    => 1,
        'price_unit'  => $priceUnit,
        'is_billable' => $billable,
        'product_id'  => $product?->id,
        'uom_id'      => $product?->uom_id,
        'currency_id' => $shipment->currency_id,
    ]);
}

it('offers the invoice action to a user who may create invoices', function () {
    $company = invoiceScreenCompany();
    $shipment = LogisticsHelper::shipment($company);

    invoiceScreenCharge($shipment);

    FilamentHelper::actingAsCompanyUser($company, [
        'view_any_logistics_shipment',
        'view_logistics_shipment',
        'create_invoice_logistics_shipment',
    ]);

    Livewire::test(ViewShipment::class, ['record' => $shipment->id])
        ->assertActionVisible('createShipmentInvoice');
});

it('hides the invoice action from a user without the permission', function () {
    $company = invoiceScreenCompany();
    $shipment = LogisticsHelper::shipment($company);

    invoiceScreenCharge($shipment);

    FilamentHelper::actingAsCompanyUser($company, [
        'view_any_logistics_shipment',
        'view_logistics_shipment',
    ]);

    Livewire::test(ViewShipment::class, ['record' => $shipment->id])
        ->assertActionHidden('createShipmentInvoice');
});

it('creates the invoice through the page, not just the service', function () {
    $company = invoiceScreenCompany();
    $shipment = LogisticsHelper::shipment($company);

    invoiceScreenCharge($shipment, priceUnit: 300);

    FilamentHelper::actingAsCompanyUser($company, [
        'view_any_logistics_shipment',
        'view_logistics_shipment',
        'create_invoice_logistics_shipment',
    ]);

    Livewire::test(ViewShipment::class, ['record' => $shipment->id])
        ->callAction('createShipmentInvoice');

    expect($shipment->invoices()->count())->toBe(1)
        ->and($shipment->charges()->first()->refresh()->move_line_id)->not->toBeNull();
});

it('reports nothing to invoice instead of failing when the charges are already invoiced', function () {
    $company = invoiceScreenCompany();
    $shipment = LogisticsHelper::shipment($company);

    // Present but not billable, so there is never anything to pick up.
    invoiceScreenCharge($shipment, billable: false);

    FilamentHelper::actingAsCompanyUser($company, [
        'view_any_logistics_shipment',
        'view_logistics_shipment',
        'create_invoice_logistics_shipment',
    ]);

    // The action catches NothingToInvoice and notifies; it must not blow up.
    Livewire::test(ViewShipment::class, ['record' => $shipment->id])
        ->callAction('createShipmentInvoice')
        ->assertHasNoActionErrors();

    expect($shipment->invoices()->count())->toBe(0);
});

it('needs both the page permission and shipment financials to open unbilled charges', function () {
    $company = invoiceScreenCompany();

    // Page permission alone is not enough: the page lists charge amounts and
    // customer names, so it also requires shipment financials.
    FilamentHelper::actingAsCompanyUser($company, ['page_logistics_unbilled_charges']);
    expect(UnbilledCharges::canAccess())->toBeFalse();

    // Financials alone is not enough either.
    FilamentHelper::actingAsCompanyUser($company, ['view_financials_logistics_shipment']);
    expect(UnbilledCharges::canAccess())->toBeFalse();

    FilamentHelper::actingAsCompanyUser($company, [
        'page_logistics_unbilled_charges',
        'view_financials_logistics_shipment',
    ]);
    expect(UnbilledCharges::canAccess())->toBeTrue();
});

it('lists only billable charges that have not been invoiced', function () {
    $company = invoiceScreenCompany();
    $shipment = LogisticsHelper::shipment($company);

    $notBillable = invoiceScreenCharge($shipment, priceUnit: 50, billable: false);

    // Invoiced for real rather than stamped with a made-up id: move_line_id is
    // a foreign key, and a fake value only proves the test can write nonsense.
    $alreadyInvoiced = invoiceScreenCharge($shipment, priceUnit: 75);

    FilamentHelper::actingAsCompanyUser($company, [
        'view_any_logistics_shipment',
        'create_invoice_logistics_shipment',
    ]);

    app(ShipmentInvoicer::class)->createInvoice($shipment);

    // Added after the invoice, so this is the only one still outstanding.
    $open = invoiceScreenCharge($shipment, priceUnit: 100);

    FilamentHelper::actingAsCompanyUser($company, [
        'page_logistics_unbilled_charges',
        'view_financials_logistics_shipment',
    ]);

    Livewire::test(UnbilledCharges::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$open])
        ->assertCanNotSeeTableRecords([$notBillable, $alreadyInvoiced]);
});

it('never shows another company’s unbilled charges', function () {
    $a = invoiceScreenCompany();
    $b = invoiceScreenCompany();

    $mine = invoiceScreenCharge(LogisticsHelper::shipment($a));
    $theirs = invoiceScreenCharge(LogisticsHelper::shipment($b));

    FilamentHelper::actingAsCompanyUser($a, [
        'page_logistics_unbilled_charges',
        'view_financials_logistics_shipment',
    ]);

    Livewire::test(UnbilledCharges::class)
        ->assertCanSeeTableRecords([$mine])
        ->assertCanNotSeeTableRecords([$theirs]);
});
