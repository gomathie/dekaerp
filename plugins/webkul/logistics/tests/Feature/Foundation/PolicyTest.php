<?php

use Illuminate\Support\Facades\Gate;
use Webkul\Logistics\Models\ServiceType;
use Webkul\Logistics\Models\Shipment;

require_once __DIR__.'/../../Helpers/LogisticsHelper.php';

beforeEach(function () {
    LogisticsHelper::install();
});

it('denies listing shipments without the permission', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());

    CompanyHelper::actingAsCompanyUser($company, []);

    expect(Gate::allows('viewAny', Shipment::class))->toBeFalse();
});

it('denies listing shipments while the company has Logistics switched off', function () {
    $company = LogisticsHelper::company();

    CompanyHelper::actingAsCompanyUser($company, ['view_any_logistics_shipment', 'create_logistics_shipment']);

    expect(Gate::allows('viewAny', Shipment::class))->toBeFalse()
        ->and(Gate::allows('create', Shipment::class))->toBeFalse();
});

it('allows listing and creating once the company has Logistics switched on', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());

    CompanyHelper::actingAsCompanyUser($company, ['view_any_logistics_shipment', 'create_logistics_shipment']);

    expect(Gate::allows('viewAny', Shipment::class))->toBeTrue()
        ->and(Gate::allows('create', Shipment::class))->toBeTrue();
});

it('guards custom shipment abilities by permission and by the record company switch', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());
    $shipment = LogisticsHelper::shipment($company);

    CompanyHelper::actingAsCompanyUser($company, ['confirm_logistics_shipment']);

    expect(Gate::allows('confirm', $shipment))->toBeTrue()
        ->and(Gate::allows('cancel', $shipment))->toBeFalse();

    LogisticsHelper::disable($company);

    expect(Gate::allows('confirm', $shipment))->toBeFalse();
});

it('lets configuration be managed before Logistics is switched on', function () {
    $company = LogisticsHelper::company();

    CompanyHelper::actingAsCompanyUser($company, ['view_any_logistics_service::type', 'create_logistics_service::type']);

    expect(Gate::allows('viewAny', ServiceType::class))->toBeTrue()
        ->and(Gate::allows('create', ServiceType::class))->toBeTrue();
});

it('keeps shared configuration read-only for users limited to some companies', function () {
    $company = LogisticsHelper::company();

    CompanyHelper::actingAsCompanyUser($company, ['update_logistics_service::type']);

    $shared = ServiceType::factory()->create(['company_id' => null]);
    $own = ServiceType::factory()->create(['company_id' => $company->id]);

    expect(Gate::allows('update', $shared))->toBeFalse()
        ->and(Gate::allows('update', $own))->toBeTrue();
});
