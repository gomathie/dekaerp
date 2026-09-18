<?php

use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Webkul\Logistics\Enums\ShipmentState;
use Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource;
use Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\Pages\ListShipments;
use Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\Pages\ManageTimeline;
use Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\Pages\ViewShipment;
use Webkul\Logistics\Models\Shipment;
use Webkul\Logistics\Services\ShipmentWorkflow;

require_once __DIR__.'/../../Helpers/LogisticsHelper.php';

beforeEach(function () {
    LogisticsHelper::install();
});

it('lists shipments for a permitted user of an enabled company', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());
    $shipment = LogisticsHelper::shipment($company);

    FilamentHelper::actingAsCompanyUser($company, ['view_any_logistics_shipment']);

    Livewire::test(ListShipments::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$shipment])
        ->assertCanRenderTableColumn('name')
        ->assertCanRenderTableColumn('state');
});

it('forbids the shipment list without permission', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());

    FilamentHelper::actingAsCompanyUser($company, []);

    Livewire::test(ListShipments::class)->assertForbidden();
});

it('never shows another company’s shipments', function () {
    $a = LogisticsHelper::enable(LogisticsHelper::company());
    $b = LogisticsHelper::enable(LogisticsHelper::company());

    $mine = LogisticsHelper::shipment($a);
    $theirs = LogisticsHelper::shipment($b);

    FilamentHelper::actingAsCompanyUser($a, ['view_any_logistics_shipment']);

    Livewire::test(ListShipments::class)
        ->assertCanSeeTableRecords([$mine])
        ->assertCanNotSeeTableRecords([$theirs]);

    expect(Shipment::query()->whereKey($theirs->id)->exists())->toBeFalse();
});

it('keeps the list query count flat as rows are added', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());

    FilamentHelper::actingAsCompanyUser($company, ['view_any_logistics_shipment']);

    $count = function (): int {
        $queries = 0;
        DB::listen(function () use (&$queries): void {
            $queries++;
        });

        Livewire::test(ListShipments::class)->assertOk();

        return $queries;
    };

    LogisticsHelper::shipment($company);
    $withOne = $count();

    foreach (range(1, 4) as $ignored) {
        LogisticsHelper::shipment($company);
    }

    $withFive = $count();

    // Eager loading means four extra rows must not add queries.
    expect($withFive)->toBeLessThanOrEqual($withOne);
});

it('shows the shipment and its timeline', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());
    $shipment = LogisticsHelper::shipment($company);

    app(ShipmentWorkflow::class)->transition($shipment, ShipmentState::CONFIRMED);

    FilamentHelper::actingAsCompanyUser($company, ['view_any_logistics_shipment', 'view_logistics_shipment']);

    Livewire::test(ViewShipment::class, ['record' => $shipment->id])->assertOk();

    Livewire::test(ManageTimeline::class, ['record' => $shipment->id])
        ->assertOk()
        ->assertCanSeeTableRecords($shipment->events()->get());
});

it('confirms a shipment from the view page and refuses without the permission', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());
    $shipment = LogisticsHelper::shipment($company);

    FilamentHelper::actingAsCompanyUser($company, ['view_logistics_shipment', 'view_any_logistics_shipment']);

    Livewire::test(ViewShipment::class, ['record' => $shipment->id])
        ->assertActionHidden('logistics.shipment.confirm');

    FilamentHelper::actingAsCompanyUser($company, ['view_logistics_shipment', 'view_any_logistics_shipment', 'confirm_logistics_shipment']);

    Livewire::test(ViewShipment::class, ['record' => $shipment->id])
        ->assertActionVisible('logistics.shipment.confirm')
        ->callAction('logistics.shipment.confirm');

    expect($shipment->refresh()->state)->toBe(ShipmentState::CONFIRMED);
});

it('finds shipments in global search only within the user’s companies', function () {
    $a = LogisticsHelper::enable(LogisticsHelper::company());
    $b = LogisticsHelper::enable(LogisticsHelper::company());

    $mine = LogisticsHelper::shipment($a, ['customer_reference' => 'FINDME-A']);
    LogisticsHelper::shipment($b, ['customer_reference' => 'FINDME-B']);

    FilamentHelper::actingAsCompanyUser($a, ['view_any_logistics_shipment']);

    $results = collect(ShipmentResource::getGlobalSearchResults('FINDME'))->pluck('title');

    expect($results)->toHaveCount(1)
        ->and($results->first())->toBe($mine->name);
});
