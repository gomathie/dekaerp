<?php

use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Partner\Models\Partner;
use Webkul\PluginManager\Support\PluginAccess;
use Webkul\Product\Models\Product;
use Webkul\Purchase\Enums\OrderState;
use Webkul\Purchase\Filament\Admin\Clusters\Reporting\Pages\PurchaseAnalysis;
use Webkul\Purchase\Filament\Admin\Clusters\Reporting\Pages\PurchasesByProduct;
use Webkul\Purchase\Models\Order;
use Webkul\Purchase\Models\OrderLine;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/ReportPdfHelper.php';
require_once __DIR__.'/../../Helpers/PurchaseHelper.php';

/**
 * "Export to PDF" on Purchase analysis and Purchases by product (plan #9
 * R-2, 2026-11-25): the rows the filters leave on screen, the filters in
 * words, the totals, the page's own permission, and no other company's
 * purchases.
 */
beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('purchases');
    PluginAccess::flush();

    // The report page's header links to its cluster. A test panel does not
    // register every sibling route; a missing one is not this page's data.
    URL::resolveMissingNamedRoutesUsing(fn () => '#');
});

/**
 * A confirmed purchase order in the company's own currency (rate 1).
 */
function purchasesReportPdfOrder(Company $company, string $date, float $untaxed): Order
{
    return PurchaseHelper::order([
        'company_id'      => $company->getKey(),
        'partner_id'      => Partner::factory()->create(['company_id' => $company->getKey()])->getKey(),
        'currency_id'     => $company->currency_id,
        'state'           => OrderState::PURCHASE,
        'ordered_at'      => $date,
        'untaxed_amount'  => $untaxed,
        'tax_amount'      => 0,
        'total_amount'    => $untaxed,
        'total_cc_amount' => $untaxed,
        'currency_rate'   => 1,
    ])->refresh();
}

function purchasesReportPdfLine(Order $order, Product $product, float $qty, float $subtotal): OrderLine
{
    // The report reads the line. Saving it must not start a receipt.
    return OrderLine::withoutEvents(fn (): OrderLine => OrderLine::factory()->create([
        'order_id'        => $order->getKey(),
        'company_id'      => $order->company_id,
        'state'           => $order->state,
        'product_id'      => $product->getKey(),
        'uom_id'          => $product->uom_id,
        'product_qty'     => $qty,
        'product_uom_qty' => $qty,
        'price_unit'      => $subtotal / $qty,
        'price_subtotal'  => $subtotal,
        'price_total'     => $subtotal,
        'partner_id'      => $order->partner_id,
        'currency_id'     => $order->currency_id,
    ]));
}

it('offers Export to PDF on both purchase reports and downloads a PDF', function (string $page, string $permission): void {
    $company = Company::factory()->create();
    purchasesReportPdfOrder($company, now()->toDateTimeString(), 100);

    FilamentHelper::actingAsCompanyUser($company, [$permission]);

    Livewire::test($page)
        ->assertTableActionVisible('export-pdf')
        ->callTableAction('export-pdf')
        ->assertFileDownloaded(contentType: 'application/pdf');
})->with([
    'purchase analysis'    => [PurchaseAnalysis::class, 'page_purchase_purchase_analysis'],
    'purchases by product' => [PurchasesByProduct::class, 'page_purchase_purchases_by_product'],
]);

it('prints the purchases of the filtered period with its total, and no other company’s', function (): void {
    $company = Company::factory()->create();
    $stranger = Company::factory()->create();

    $inPeriod = purchasesReportPdfOrder($company, '2026-11-10 10:00:00', 321.5);
    $before = purchasesReportPdfOrder($company, '2026-10-02 10:00:00', 999);
    $theirs = purchasesReportPdfOrder($stranger, '2026-11-12 10:00:00', 555);

    FilamentHelper::actingAsCompanyUser($company, ['page_purchase_purchase_analysis']);

    $html = ReportPdfHelper::tableHtml(
        Livewire::test(PurchaseAnalysis::class)->filterTable('period', ['from' => '2026-11-01', 'until' => '2026-11-30'])
    );

    expect($html)->toContain(e($inPeriod->name))
        ->not->toContain(e($before->name))
        ->not->toContain(e($theirs->name))
        ->toContain('From 1 Nov 2026, Until 30 Nov 2026')
        ->toContain(e(__('support::report-pdf.total')))
        ->toContain('321.50')
        ->not->toContain('999.00');
});

it('prints the filtered product with its group subtotal and says which product', function (): void {
    $company = Company::factory()->create();
    $order = purchasesReportPdfOrder($company, now()->toDateTimeString(), 0);

    $chosen = PurchaseHelper::product(['company_id' => $company->getKey(), 'name' => 'Diesel 20L']);
    $other = PurchaseHelper::product(['company_id' => $company->getKey(), 'name' => 'Tyre 315']);

    purchasesReportPdfLine($order, $chosen, 2, 40);
    purchasesReportPdfLine($order, $chosen, 3, 60);
    purchasesReportPdfLine($order, $other, 1, 777);

    FilamentHelper::actingAsCompanyUser($company, ['page_purchase_purchases_by_product']);

    $html = ReportPdfHelper::tableHtml(
        Livewire::test(PurchasesByProduct::class)->filterTable('product_id', $chosen->getKey())
    );

    expect($html)->toContain('Diesel 20L')
        ->not->toContain('Tyre 315')
        ->toContain(e(__('purchases::filament/admin/clusters/reporting.by-product.columns.product').': Diesel 20L'))
        ->toContain(e(__('support::report-pdf.group-total', ['group' => 'Diesel 20L'])))
        ->toContain('5.00')
        ->toContain('100.00');
});

it('refuses the purchases PDF once the viewer may no longer open the report', function (): void {
    $company = Company::factory()->create();
    purchasesReportPdfOrder($company, now()->toDateTimeString(), 100);

    FilamentHelper::actingAsCompanyUser($company, ['page_purchase_purchase_analysis']);
    $component = Livewire::test(PurchaseAnalysis::class);

    FilamentHelper::actingAsCompanyUser($company, []);

    expect(PurchaseAnalysis::canAccess())->toBeFalse();

    Livewire::test(PurchaseAnalysis::class)->assertForbidden();

    expect(ReportPdfHelper::downloaded($component))->toBeFalse();
});
