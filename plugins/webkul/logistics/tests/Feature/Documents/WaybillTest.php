<?php

use Illuminate\Auth\Access\AuthorizationException;
use Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\Actions\PrintWaybillAction;
use Webkul\Logistics\Models\ShipmentLine;
use Webkul\Logistics\Support\LogisticsSequences;
use Webkul\Support\Models\Scopes\CompanyScope;
use Webkul\Support\Models\Sequence;

require_once __DIR__.'/../../Helpers/LogisticsHelper.php';

beforeEach(function () {
    LogisticsHelper::install();
});

it('renders a waybill PDF for a shipment', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());
    $shipment = LogisticsHelper::shipment($company);

    ShipmentLine::factory()->create([
        'shipment_id' => $shipment->id,
        'description' => 'Palletised machine parts',
    ]);

    FilamentHelper::actingAsCompanyUser($company, ['view_logistics_shipment']);

    $response = PrintWaybillAction::make()->download($shipment);

    expect($response->headers->get('content-type'))->toBe('application/pdf')
        ->and($response->getContent())->toStartWith('%PDF-')
        ->and($shipment->refresh()->waybill_no)->not->toBeNull();
});

it('uses the shipment company letterhead when another company is current', function () {
    $currentCompany = LogisticsHelper::enable(LogisticsHelper::company(['name' => 'Current Session Company']));
    $shipmentCompany = LogisticsHelper::enable(LogisticsHelper::company(['name' => 'Shipment Letterhead Company']));
    $shipment = LogisticsHelper::shipment($shipmentCompany);

    FilamentHelper::actingAsCompanyUser(
        [$currentCompany, $shipmentCompany],
        ['view_logistics_shipment'],
    );

    PrintWaybillAction::make()->download($shipment);

    $html = view('logistics::pdf.waybill', [
        'record' => $shipment->refresh()->load([
            'company.partner.state',
            'company.partner.country',
            'customer',
            'pickupAddress',
            'deliveryAddress',
            'lines.packageType',
            'trips.driver',
            'trips.vehicle',
        ]),
    ])->render();

    expect(current_company_id())->toBe($currentCompany->id)
        ->and($html)->toContain('Shipment Letterhead Company')
        ->and($html)->not->toContain('Current Session Company');
});

it('reuses the first waybill number without consuming another sequence value', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());
    $shipment = LogisticsHelper::shipment($company);

    FilamentHelper::actingAsCompanyUser($company, ['view_logistics_shipment']);

    $action = PrintWaybillAction::make();
    $action->download($shipment);

    $firstNumber = $shipment->refresh()->waybill_no;
    $nextNumberAfterFirstPrint = Sequence::withoutGlobalScope(CompanyScope::class)
        ->where('code', LogisticsSequences::WAYBILL)
        ->where('company_id', $company->id)
        ->value('next_number');

    $action->download($shipment);

    expect($shipment->refresh()->waybill_no)->toBe($firstNumber)
        ->and(Sequence::withoutGlobalScope(CompanyScope::class)
            ->where('code', LogisticsSequences::WAYBILL)
            ->where('company_id', $company->id)
            ->value('next_number'))
        ->toBe($nextNumberAfterFirstPrint)
        ->and($nextNumberAfterFirstPrint)->toBe(2);
});

it('authorizes the download where the PDF work happens', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());
    $shipment = LogisticsHelper::shipment($company);

    FilamentHelper::actingAsCompanyUser($company);

    expect(fn () => PrintWaybillAction::make()->download($shipment))
        ->toThrow(AuthorizationException::class);

    expect($shipment->refresh()->waybill_no)->toBeNull();
});
