<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Logistics\Enums\ShipmentState;
use Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource;
use Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\Pages\ListShipments;
use Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\Pages\ManageTimeline;
use Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\Pages\ViewShipment;
use Webkul\Logistics\Models\Shipment;
use Webkul\Logistics\Services\ShipmentWorkflow;
use Webkul\Logistics\Support\LogisticsAccess;

require_once __DIR__.'/../../Helpers/LogisticsHelper.php';

beforeEach(function () {
    LogisticsHelper::install();

    // The panel boots before the plugin is installed in tests, so its routes are
    // missing when this file runs on its own. Same approach as the Accounts tests.
    URL::resolveMissingNamedRoutesUsing(fn (): string => '#');
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

it('loads the list relations once, not once per row', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());

    foreach (range(1, 5) as $ignored) {
        LogisticsHelper::shipment($company);
    }

    FilamentHelper::actingAsCompanyUser($company, ['view_any_logistics_shipment']);

    $statements = [];

    DB::listen(function ($query) use (&$statements): void {
        $statements[] = $query->sql;
    });

    Livewire::test(ListShipments::class)->assertOk();

    $hits = fn (string $table): int => collect($statements)
        ->filter(fn (string $sql): bool => str_contains($sql, '"'.$table.'"'))
        ->count();

    // Each table behind a column is queried a fixed number of times for the whole
    // page. Per-row loading would give five or more.
    // (HasCustomFields loads per record, which is framework behaviour, so the
    // total query count is not asserted here.)
    expect($hits('partners_partners'))->toBeLessThanOrEqual(2)
        ->and($hits('logistics_service_types'))->toBeLessThanOrEqual(2)
        ->and($hits('companies'))->toBeLessThanOrEqual(3);
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
        ->assertActionHidden('confirmShipment');

    $user = FilamentHelper::actingAsCompanyUser($company, ['view_logistics_shipment', 'view_any_logistics_shipment', 'confirm_logistics_shipment']);

    // Narrow down where a failure comes from: the permission, the policy, the
    // switch, or the action's own visibility rule. Compared as one array so a
    // failure names the link that broke.
    expect([
        'permission' => $user->can('confirm_logistics_shipment'),
        'enabled'    => LogisticsAccess::enabledFor($shipment->company_id),
        'policy'     => $user->can('confirm', $shipment),
        'state'      => $shipment->refresh()->state->value,
    ])->toBe([
        'permission' => true,
        'enabled'    => true,
        'policy'     => true,
        'state'      => ShipmentState::DRAFT->value,
    ]);

    Livewire::test(ViewShipment::class, ['record' => $shipment->id])
        ->assertActionVisible('confirmShipment')
        ->callAction('confirmShipment');

    expect($shipment->refresh()->state)->toBe(ShipmentState::CONFIRMED);
});

it('finds shipments in global search only within the user’s companies', function () {
    $a = LogisticsHelper::enable(LogisticsHelper::company());
    $b = LogisticsHelper::enable(LogisticsHelper::company());

    $mine = LogisticsHelper::shipment($a, ['customer_reference' => 'FINDME-A']);
    LogisticsHelper::shipment($b, ['customer_reference' => 'FINDME-B']);

    // "view" is needed as well: Filament drops a search result whose record the
    // user may not open, because it has no URL to link to.
    FilamentHelper::actingAsCompanyUser($a, ['view_any_logistics_shipment', 'view_logistics_shipment']);

    // The scoped query must see exactly the caller's shipment...
    $scoped = ShipmentResource::getGlobalSearchEloquentQuery()
        ->where('customer_reference', 'like', '%FINDME%')
        ->pluck('customer_reference');

    expect($scoped)->toHaveCount(1)
        ->and($scoped->first())->toBe('FINDME-A');

    // ...and the resource's own search must return it and nothing else.
    $results = collect(ShipmentResource::getGlobalSearchResults('FINDME'))->pluck('title');

    expect($results)->toHaveCount(1)
        ->and($results->first())->toBe($mine->name);
});
