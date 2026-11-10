<?php

use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Account\Filament\Resources\CustomerResource\Pages\ViewCustomer;
use Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\Pages\ViewShipment;
use Webkul\Logistics\Models\ShipmentCharge;
use Webkul\Logistics\Services\CustomerPage;
use Webkul\Partner\Models\Partner;

require_once __DIR__.'/../../Helpers/LogisticsHelper.php';

beforeEach(function () {
    LogisticsHelper::install();

    URL::resolveMissingNamedRoutesUsing(fn (): string => '#');
});

it('shows a customer their own shipments and open charges', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());
    $customer = Partner::factory()->create([
        'company_id'    => $company->id,
        'customer_rank' => 1,
        'name'          => 'Acme Freight',
    ]);
    $shipment = LogisticsHelper::shipment($company, [
        'customer_id' => $customer->id,
        'name'        => 'SHIP-OWN',
    ]);
    ShipmentCharge::factory()->create([
        'shipment_id' => $shipment->id,
        'description' => 'Open handling',
        'quantity'    => 1,
        'price_unit'  => 42.5,
        'subtotal'    => 42.5,
        'total'       => 42.5,
    ]);
    ShipmentCharge::factory()->create([
        'shipment_id' => $shipment->id,
        'description' => 'Not billable',
        'is_billable' => false,
    ]);

    FilamentHelper::actingAsCompanyUser($company, [
        'view_any_account_customer',
        'view_account_customer',
        'view_any_logistics_shipment',
        'view_logistics_shipment',
        'view_financials_logistics_shipment',
    ]);

    Livewire::test(ViewCustomer::class, ['record' => $customer->getRouteKey()])
        ->assertOk()
        ->assertSee('SHIP-OWN')
        ->assertSee('Open handling')
        ->assertDontSee('Not billable');
});

it('hides another company\'s shipments on a shared contact', function () {
    $own = LogisticsHelper::enable(LogisticsHelper::company());
    $other = LogisticsHelper::enable(LogisticsHelper::company());
    $shared = Partner::factory()->create([
        'company_id'    => $own->id,
        'customer_rank' => 1,
        'name'          => 'Shared Customer',
    ]);
    $shared->forceFill(['company_id' => null])->saveQuietly();

    LogisticsHelper::shipment($own, [
        'customer_id' => $shared->id,
        'name'        => 'SHIP-SHARED-OWN',
    ]);
    $theirs = LogisticsHelper::shipment($other, [
        'customer_id' => $shared->id,
        'name'        => 'SHIP-SHARED-OTHER',
    ]);

    FilamentHelper::actingAsCompanyUser($own, [
        'view_any_account_customer',
        'view_account_customer',
        'view_any_logistics_shipment',
        'view_logistics_shipment',
    ]);

    expect(CustomerPage::recentShipments($shared->fresh())->pluck('name')->all())
        ->toBe(['SHIP-SHARED-OWN']);

    Livewire::test(ViewCustomer::class, ['record' => $shared->getRouteKey()])
        ->assertOk()
        ->assertSee('SHIP-SHARED-OWN')
        ->assertDontSee('SHIP-SHARED-OTHER');

    Livewire::test(ViewShipment::class, ['record' => $theirs->getRouteKey()])
        ->assertNotFound();
});

it('hides the shipments tab when logistics is off or the user cannot view shipments', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());
    $customer = Partner::factory()->create([
        'company_id'    => $company->id,
        'customer_rank' => 1,
    ]);
    $shipment = LogisticsHelper::shipment($company, [
        'customer_id' => $customer->id,
        'name'        => 'SHIP-HIDDEN',
    ]);

    FilamentHelper::actingAsCompanyUser($company, [
        'view_any_account_customer',
        'view_account_customer',
    ]);

    Livewire::test(ViewCustomer::class, ['record' => $customer->getRouteKey()])
        ->assertOk()
        ->assertDontSee('SHIP-HIDDEN')
        ->assertDontSee(__('logistics::filament/partner.tab'));

    Livewire::test(ViewShipment::class, ['record' => $shipment->getRouteKey()])
        ->assertForbidden();

    FilamentHelper::actingAsCompanyUser($company, [
        'view_any_account_customer',
        'view_account_customer',
        'view_any_logistics_shipment',
        'view_logistics_shipment',
        'view_financials_logistics_shipment',
    ]);
    LogisticsHelper::disable($company);

    Livewire::test(ViewCustomer::class, ['record' => $customer->getRouteKey()])
        ->assertOk()
        ->assertDontSee('SHIP-HIDDEN');
});

it('hides open charges without view_financials', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());
    $customer = Partner::factory()->create([
        'company_id'    => $company->id,
        'customer_rank' => 1,
    ]);
    $shipment = LogisticsHelper::shipment($company, [
        'customer_id' => $customer->id,
        'name'        => 'SHIP-NO-MONEY',
    ]);
    ShipmentCharge::factory()->create([
        'shipment_id' => $shipment->id,
        'description' => 'Open handling',
        'total'       => 42.5,
        'subtotal'    => 42.5,
    ]);

    FilamentHelper::actingAsCompanyUser($company, [
        'view_any_account_customer',
        'view_account_customer',
        'view_any_logistics_shipment',
        'view_logistics_shipment',
    ]);

    expect(CustomerPage::showsCharges())->toBeFalse()
        ->and(CustomerPage::openCharges($customer)->isEmpty())->toBeTrue();

    Livewire::test(ViewCustomer::class, ['record' => $customer->getRouteKey()])
        ->assertOk()
        ->assertSee('SHIP-NO-MONEY')
        ->assertDontSee('Open handling');
});
