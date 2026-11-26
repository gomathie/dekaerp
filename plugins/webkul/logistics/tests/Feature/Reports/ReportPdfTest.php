<?php

use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Logistics\Enums\ShipmentState;
use Webkul\Logistics\Filament\Clusters\Reporting\Pages\DeliveryPerformance;
use Webkul\Logistics\Filament\Clusters\Reporting\Pages\DriverTripHistory;
use Webkul\Logistics\Filament\Clusters\Reporting\Pages\ShipmentProfitability;
use Webkul\Logistics\Filament\Clusters\Reporting\Pages\ShipmentRegister;
use Webkul\Logistics\Filament\Clusters\Reporting\Pages\VehicleTripHistory;
use Webkul\Partner\Models\Partner;
use Webkul\Support\Support\ReportPdf;

require_once __DIR__.'/../../Helpers/LogisticsHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/ReportPdfHelper.php';

/**
 * "Export to PDF" on the Logistics reports (plan #9 R-2, 2026-11-25): the
 * PDF prints what the screen shows for the current filters, for the same
 * people the page opens for, and nothing from another company.
 */
beforeEach(function () {
    LogisticsHelper::install();

    // The report page's header links to its cluster. A test panel does not
    // register every sibling route; a missing one is not this page's data.
    URL::resolveMissingNamedRoutesUsing(fn () => '#');
});

afterEach(function () {
    ReportPdf::$maxRows = 2000;
});

/**
 * Every permission the five reports ask for between them.
 *
 * @return list<string>
 */
function logisticsReportPdfPermissions(): array
{
    return [
        'page_logistics_shipment_register',
        'page_logistics_delivery_performance',
        'page_logistics_shipment_profitability',
        'page_logistics_vehicle_trip_history',
        'page_logistics_driver_trip_history',
        'view_financials_logistics_shipment',
    ];
}

it('offers Export to PDF on every report and downloads a PDF', function (string $page) {
    $company = LogisticsHelper::enable(LogisticsHelper::company());
    LogisticsHelper::shipment($company);

    CompanyHelper::actingAsCompanyUser($company, logisticsReportPdfPermissions());

    Livewire::test($page)
        ->assertTableActionVisible('export-pdf')
        ->callTableAction('export-pdf')
        ->assertFileDownloaded(contentType: 'application/pdf');
})->with([
    'shipment register'      => ShipmentRegister::class,
    'delivery performance'   => DeliveryPerformance::class,
    'shipment profitability' => ShipmentProfitability::class,
    'vehicle trip history'   => VehicleTripHistory::class,
    'driver trip history'    => DriverTripHistory::class,
]);

it('prints the rows the filters left on screen and says which filters', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());

    $delivered = LogisticsHelper::shipment($company, ['state' => ShipmentState::DELIVERED]);
    $draft = LogisticsHelper::shipment($company, ['state' => ShipmentState::DRAFT]);

    CompanyHelper::actingAsCompanyUser($company, ['page_logistics_shipment_register']);

    $html = ReportPdfHelper::tableHtml(
        Livewire::test(ShipmentRegister::class)->filterTable('state', [ShipmentState::DELIVERED->value])
    );

    expect($html)->toContain(e($delivered->name))
        ->not->toContain(e($draft->name))
        ->toContain(e(__('logistics::reports.shipment-register.filters.state').': '.ShipmentState::DELIVERED->getLabel()))
        ->toContain(e(__('logistics::reports.shipment-register.title')))
        ->toContain(e($company->name));
});

it('describes a date range that shows no filter chip', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());
    LogisticsHelper::shipment($company);

    CompanyHelper::actingAsCompanyUser($company, ['page_logistics_shipment_register']);

    // The register's "created" filter has no indicator of its own; without a
    // description the PDF of one month would read as if it covered all time.
    $html = ReportPdfHelper::tableHtml(
        Livewire::test(ShipmentRegister::class)->filterTable('created', ['from' => '2026-11-01', 'until' => '2026-11-30'])
    );

    expect($html)->toContain('From 1 Nov 2026, Until 30 Nov 2026');
});

it('never prints another company’s shipments', function () {
    $mine = LogisticsHelper::enable(LogisticsHelper::company());
    $theirs = LogisticsHelper::enable(LogisticsHelper::company());

    $ours = LogisticsHelper::shipment($mine, ['name' => 'OUR-SHIPMENT-PDF']);
    $other = LogisticsHelper::shipment($theirs, ['name' => 'THEIR-SHIPMENT-PDF']);

    CompanyHelper::actingAsCompanyUser($mine, logisticsReportPdfPermissions());

    foreach ([ShipmentRegister::class, ShipmentProfitability::class, DeliveryPerformance::class] as $page) {
        expect(ReportPdfHelper::tableHtml(Livewire::test($page)))
            ->toContain(e($ours->name))
            ->not->toContain(e($other->name));
    }
});

it('prints the totals row the screen shows', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());

    foreach ([600, 500] as $revenue) {
        LogisticsHelper::shipment($company)->charges()->create([
            'description' => 'Freight',
            'quantity'    => 1,
            'price_unit'  => $revenue,
            'is_billable' => true,
            'currency_id' => $company->currency_id,
        ]);
    }

    CompanyHelper::actingAsCompanyUser($company, ['page_logistics_shipment_profitability', 'view_financials_logistics_shipment']);

    expect(ReportPdfHelper::tableHtml(Livewire::test(ShipmentProfitability::class)))
        ->toContain(e(__('support::report-pdf.total')))
        ->toContain('1,100.00');
});

it('escapes what users typed', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());

    LogisticsHelper::shipment($company, [
        'customer_id' => Partner::factory()->create(['company_id' => $company->id, 'name' => '<b>Bold & Co</b>'])->id,
    ]);

    CompanyHelper::actingAsCompanyUser($company, ['page_logistics_shipment_register']);

    expect(ReportPdfHelper::tableHtml(Livewire::test(ShipmentRegister::class)))
        ->toContain('&lt;b&gt;Bold &amp; Co&lt;/b&gt;')
        ->not->toContain('<b>Bold');
});

it('refuses the PDF to someone who may not open the report', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());
    LogisticsHelper::shipment($company);

    CompanyHelper::actingAsCompanyUser($company, ['page_logistics_shipment_register']);
    $component = Livewire::test(ShipmentRegister::class);

    // The same open page, now driven by a user without the permission: the
    // action checks the page's own rule again instead of trusting the mount.
    CompanyHelper::actingAsCompanyUser($company, []);

    expect(ShipmentRegister::canAccess())->toBeFalse();

    Livewire::test(ShipmentRegister::class)->assertForbidden();

    expect(ReportPdfHelper::downloaded($component))->toBeFalse();
});

it('keeps the profitability PDF behind view_financials', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());
    LogisticsHelper::shipment($company);

    CompanyHelper::actingAsCompanyUser($company, ['page_logistics_shipment_profitability', 'view_financials_logistics_shipment']);
    $component = Livewire::test(ShipmentProfitability::class);

    // Revenue, costs and margins: the page permission alone is not enough.
    CompanyHelper::actingAsCompanyUser($company, ['page_logistics_shipment_profitability']);

    expect(ShipmentProfitability::canAccess())->toBeFalse();

    Livewire::test(ShipmentProfitability::class)->assertForbidden();

    expect(ReportPdfHelper::downloaded($component))->toBeFalse();
});

it('asks to narrow the filters instead of printing too many rows', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());
    LogisticsHelper::shipment($company);
    LogisticsHelper::shipment($company);

    CompanyHelper::actingAsCompanyUser($company, ['page_logistics_shipment_register']);

    ReportPdf::$maxRows = 1;

    $component = Livewire::test(ShipmentRegister::class)
        ->callTableAction('export-pdf')
        ->assertNotified(__('support::report-pdf.too-many.title'));

    expect(ReportPdfHelper::downloaded($component))->toBeFalse();
});
