<?php

use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Partner\Models\Partner;
use Webkul\PluginManager\Support\PluginAccess;
use Webkul\Product\Models\Product;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Filament\Clusters\Reporting\Pages\SalesAnalysis;
use Webkul\Sale\Filament\Clusters\Reporting\Pages\SalesByProduct;
use Webkul\Sale\Models\Order;
use Webkul\Sale\Models\OrderLine;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/ReportPdfHelper.php';
require_once __DIR__.'/../../Helpers/SaleHelper.php';

/**
 * "Export to PDF" on Sales analysis and Sales by product (plan #9 R-2,
 * 2026-11-25): the PDF carries the rows the filters leave on screen, with
 * the filters in words and the totals, only for someone the page opens for,
 * and never another company's sales.
 */
beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('sales');
    // SaleHelper::product() fills Inventory's is_storable column.
    TestBootstrapHelper::ensurePluginInstalled('inventories');
    PluginAccess::flush();

    // The report page's header links to its cluster. A test panel does not
    // register every sibling route; a missing one is not this page's data.
    URL::resolveMissingNamedRoutesUsing(fn () => '#');
});

/**
 * A confirmed order in the company's own currency, so its amounts are
 * already company currency (rate 1).
 */
function salesReportPdfOrder(Company $company, string $date, float $untaxed): Order
{
    return SaleHelper::order([
        'company_id'     => $company->getKey(),
        'partner_id'     => Partner::factory()->create(['company_id' => $company->getKey()])->getKey(),
        'currency_id'    => $company->currency_id,
        'state'          => OrderState::SALE,
        'date_order'     => $date,
        'amount_untaxed' => $untaxed,
        'amount_tax'     => 0,
        'amount_total'   => $untaxed,
        'currency_rate'  => 1,
    ])->refresh();
}

function salesReportPdfLine(Order $order, Product $product, float $qty, float $subtotal): OrderLine
{
    // The report reads the line. Saving it must not start a delivery.
    return OrderLine::withoutEvents(fn (): OrderLine => OrderLine::factory()->create([
        'order_id'        => $order->getKey(),
        'company_id'      => $order->company_id,
        'state'           => $order->state,
        'product_id'      => $product->getKey(),
        'product_uom_id'  => $product->uom_id,
        'product_uom_qty' => $qty,
        'product_qty'     => $qty,
        'price_unit'      => $subtotal / $qty,
        'price_subtotal'  => $subtotal,
        'price_total'     => $subtotal,
        'customer_lead'   => 0,
        'currency_id'     => $order->currency_id,
    ]));
}

it('offers Export to PDF on both sales reports and downloads a PDF', function (string $page, string $permission): void {
    $company = Company::factory()->create();
    salesReportPdfOrder($company, now()->toDateTimeString(), 100);

    FilamentHelper::actingAsCompanyUser($company, [$permission]);

    Livewire::test($page)
        ->assertTableActionVisible('export-pdf')
        ->callTableAction('export-pdf')
        ->assertFileDownloaded(contentType: 'application/pdf');
})->with([
    'sales analysis'   => [SalesAnalysis::class, 'page_sale_sales_analysis'],
    'sales by product' => [SalesByProduct::class, 'page_sale_sales_by_product'],
]);

it('prints the sales of the filtered period with its total, and no other company’s', function (): void {
    $company = Company::factory()->create();
    $stranger = Company::factory()->create();

    $inPeriod = salesReportPdfOrder($company, '2026-11-10 10:00:00', 123.45);
    $before = salesReportPdfOrder($company, '2026-10-02 10:00:00', 999);
    $theirs = salesReportPdfOrder($stranger, '2026-11-12 10:00:00', 555);

    FilamentHelper::actingAsCompanyUser($company, ['page_sale_sales_analysis']);

    $html = ReportPdfHelper::tableHtml(
        Livewire::test(SalesAnalysis::class)->filterTable('period', ['from' => '2026-11-01', 'until' => '2026-11-30'])
    );

    expect($html)->toContain(e($inPeriod->name))
        ->not->toContain(e($before->name))
        ->not->toContain(e($theirs->name))
        ->toContain('From 1 Nov 2026, Until 30 Nov 2026')
        ->toContain(e(__('support::report-pdf.total')))
        ->toContain('123.45')
        ->not->toContain('999.00');
});

it('prints the filtered product with its group subtotal and says which product', function (): void {
    $company = Company::factory()->create();
    $order = salesReportPdfOrder($company, now()->toDateTimeString(), 0);

    $chosen = SaleHelper::product(['company_id' => $company->getKey(), 'name' => 'Cement 50kg']);
    $other = SaleHelper::product(['company_id' => $company->getKey(), 'name' => 'Roofing sheet']);

    salesReportPdfLine($order, $chosen, 2, 40);
    salesReportPdfLine($order, $chosen, 3, 60);
    salesReportPdfLine($order, $other, 1, 777);

    FilamentHelper::actingAsCompanyUser($company, ['page_sale_sales_by_product']);

    $html = ReportPdfHelper::tableHtml(
        Livewire::test(SalesByProduct::class)->filterTable('product_id', $chosen->getKey())
    );

    expect($html)->toContain('Cement 50kg')
        ->not->toContain('Roofing sheet')
        ->toContain(e(__('sales::filament/clusters/reporting.by-product.columns.product').': Cement 50kg'))
        ->toContain(e(__('support::report-pdf.group-total', ['group' => 'Cement 50kg'])))
        // 2 + 3 units, 40 + 60 untaxed, in the group subtotal and the total.
        ->toContain('5.00')
        ->toContain('100.00');
});

it('refuses the sales PDF once the viewer may no longer open the report', function (): void {
    $company = Company::factory()->create();
    salesReportPdfOrder($company, now()->toDateTimeString(), 100);

    FilamentHelper::actingAsCompanyUser($company, ['page_sale_sales_analysis']);
    $component = Livewire::test(SalesAnalysis::class);

    // A later request is a different user. The PDF action uses this same rule.
    FilamentHelper::actingAsCompanyUser($company, []);

    expect(SalesAnalysis::canAccess())->toBeFalse();

    Livewire::test(SalesAnalysis::class)->assertForbidden();

    expect(ReportPdfHelper::downloaded($component))->toBeFalse();
});
