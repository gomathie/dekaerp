<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\App;
use Webkul\Logistics\Exceptions\LogisticsNotEnabledException;
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

    // A StreamedResponse holds a callback, not bytes: getContent() returns
    // false, so the PDF only exists once the stream has been sent.
    ob_start();
    $response->sendContent();
    $content = ob_get_clean();

    expect($response->headers->get('content-type'))->toBe('application/pdf')
        ->and($content)->toStartWith('%PDF-')
        ->and($shipment->refresh()->waybill_no)->not->toBeNull();
});

it('builds a download filename that Symfony will accept', function () {
    // The waybill sequence prefix contains slashes (WB/2026/00001) and Symfony
    // rejects a Content-Disposition filename holding a path separator, so every
    // print threw InvalidArgumentException after rendering the PDF. Asserted on
    // the pure helper so the regression is pinned without paying for dompdf.
    expect(PrintWaybillAction::fileName('WB/2026/00001'))->toBe('waybill-WB-2026-00001.pdf')
        ->and(PrintWaybillAction::fileName('WB\\2026\\00002'))->toBe('waybill-WB-2026-00002.pdf')
        ->and(PrintWaybillAction::fileName(null))->toBe('waybill-.pdf');

    foreach (['WB/2026/00001', 'WB\\2026\\00002', null] as $number) {
        expect(PrintWaybillAction::fileName($number))
            ->not->toContain('/')
            ->not->toContain('\\');
    }
});

it('sets a Content-Disposition the browser can act on', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());
    $shipment = LogisticsHelper::shipment($company);

    FilamentHelper::actingAsCompanyUser($company, ['view_logistics_shipment']);

    $response = PrintWaybillAction::make()->download($shipment);

    $disposition = (string) $response->headers->get('content-disposition');

    expect($disposition)->toContain('attachment')
        ->and($disposition)->toContain(str_replace('/', '-', (string) $shipment->refresh()->waybill_no));
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
        'isRtl' => false,
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

it('refuses to print after the shipment company disables Logistics', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());
    $shipment = LogisticsHelper::shipment($company);

    FilamentHelper::actingAsCompanyUser($company, ['view_logistics_shipment']);
    LogisticsHelper::disable($company);

    expect(fn () => PrintWaybillAction::make()->download($shipment))
        ->toThrow(LogisticsNotEnabledException::class);

    expect($shipment->refresh()->waybill_no)->toBeNull();
});

it('loads the waybill translations in every supported locale', function () {
    // The French file shipped with typographic quotes as PHP string delimiters,
    // so it did not parse and printing a waybill in French was a fatal error.
    // Loading the group in each locale is what proves the file parses at all.
    $titles = [];

    foreach (['en', 'ar', 'es', 'fr', 'pt_BR'] as $locale) {
        App::setLocale($locale);

        $labels = trans('logistics::documents/waybill.labels');

        expect($labels)->toBeArray()
            ->toHaveKeys(['waybill-number', 'shipment-number', 'consignor', 'date']);

        $titles[$locale] = trans('logistics::documents/waybill.title');

        expect($titles[$locale])->not->toBe('logistics::documents/waybill.title');
    }

    App::setLocale('en');

    expect($titles)->toHaveKeys(['en', 'ar', 'es', 'fr', 'pt_BR'])
        ->and(array_unique($titles))->toHaveCount(5);
});
