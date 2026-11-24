<?php

use Illuminate\Support\Carbon;
use Webkul\PluginManager\Support\PluginAccess;
use Webkul\Sale\Filament\Clusters\Orders\Resources\OrderResource\Pages\ListOrders;
use Webkul\Sale\Filament\Widgets\MonthlySalesChart;
use Webkul\Sale\Filament\Widgets\RevenueByCategoryChart;
use Webkul\Sale\Filament\Widgets\SaleHeadlineWidget;
use Webkul\Sale\Filament\Widgets\SalesPersonPerformanceChart;
use Webkul\Sale\Filament\Widgets\SaleStatsOverview;
use Webkul\Sale\Filament\Widgets\TopCustomersTable;
use Webkul\Sale\Filament\Widgets\TopProductsTable;
use Webkul\Sale\Filament\Widgets\TopSalesOrdersTable;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/DashboardFixtureHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('sales');
    PluginAccess::flush();
});

/**
 * @param  class-string  $class
 */
function salesAgreementWidget(string $class): object
{
    $widget = new $class;

    $widget->pageFilters = ['startDate' => DashboardFixtureHelper::START, 'endDate' => DashboardFixtureHelper::END];

    return $widget;
}

/**
 * Dashboard plan DB-2 (2026-11-23): the Sales dashboard reads two figures,
 * and each must agree with itself.
 *
 * - Revenue is invoiced sales (DB-8, user 2026-11-19): the Revenue figure,
 *   its daily line and the monthly chart.
 * - Sales value is confirmed orders plus invoices made without an order
 *   (user, 2026-11-20 hotfix, DirectInvoices): the salesperson, category,
 *   customer and product breakdowns, and the top sales.
 *
 * They differ by exactly the orders not invoiced yet, which the test pins,
 * so a third way of counting cannot slip in. Before this test, a customer's
 * "Sales" column counted a credit note as a sale, while the Sales figure
 * did not.
 */
it('tells the same story in the sales figures, charts and tables', function (): void {
    $own = Company::factory()->create();
    $other = CompanyHelper::asNobody(fn () => Company::factory()->create());

    $fixture = DashboardFixtureHelper::sales($own, $other);

    // A user made in memory carries a null resource_permission, which
    // Bouncer reads as "own records only"; everyone gets "global".
    $user = CompanyHelper::actingAsCompanyUser($own, []);
    $user->forceFill(['resource_permission' => 'global'])->saveQuietly();

    $figures = (fn (): array => [
        'revenue'    => $this->invoicedRevenue(),
        'sales'      => $this->salesCount(),
        'quotations' => $this->quotations()->count(),
    ])->call(salesAgreementWidget(SaleStatsOverview::class));

    $dailyRevenue = (fn (): ?array => $this->dailyRevenue())->call(salesAgreementWidget(SaleHeadlineWidget::class));
    $monthly = (fn (): array => $this->getData())->call(salesAgreementWidget(MonthlySalesChart::class));

    $bySalesperson = (fn (): array => $this->getData())->call(salesAgreementWidget(SalesPersonPerformanceChart::class));
    $byCategory = (fn (): array => $this->resolveChartData())->call(salesAgreementWidget(RevenueByCategoryChart::class));
    $customers = (fn () => $this->getTableQuery()->get())->call(salesAgreementWidget(TopCustomersTable::class));
    $products = (fn () => $this->getTableQuery()->get())->call(salesAgreementWidget(TopProductsTable::class));
    $topSales = (fn () => $this->getTableQuery()->get())->call(salesAgreementWidget(TopSalesOrdersTable::class));

    $salesValue = array_sum($bySalesperson['datasets'][0]['data']);
    $notInvoiced = (float) $fixture['orders']['SO-2']->amount_total;

    expect($figures)->toBe(['revenue' => 1200.0, 'sales' => 3, 'quotations' => 1])
        // Revenue, its daily line and the monthly chart are one figure.
        ->and(array_sum($dailyRevenue))->toBe($figures['revenue'])
        ->and(array_sum($monthly['datasets'][0]['data']))->toBe($figures['revenue'])
        // Sales value is one figure across every breakdown.
        ->and($salesValue)->toBe(1700.0)
        ->and(array_combine($bySalesperson['labels'], $bySalesperson['datasets'][0]['data']))->toBe(['Salesperson A' => 1300.0, 'Salesperson B' => 400.0])
        ->and(array_combine($byCategory['labels'], $byCategory['data']))->toBe(['Goods category' => 1300.0, 'Services category' => 400.0])
        ->and($customers->mapWithKeys(fn ($row): array => [$row->name => (float) $row->revenue])->all())->toBe(['Customer X' => 1300.0, 'Customer Y' => 400.0])
        ->and($products->mapWithKeys(fn ($row): array => [$row->name => [(float) $row->revenue, (float) $row->quantity]])->all())->toBe(['Goods' => [1300.0, 13.0], 'Services' => [400.0, 4.0]])
        // A customer's "Sales" add up to the Sales figure: a credit note is
        // not a sale.
        ->and($customers->sum(fn ($row): int => (int) $row->orders_count))->toBe($figures['sales'])
        // The top sales are the period's three sales, largest first.
        ->and($topSales->map(fn ($row): float => (float) $row->amount_total)->all())->toBe([1000.0, 500.0, 300.0])
        // The two figures differ by the order not invoiced yet, nothing else.
        ->and($salesValue - $figures['revenue'])->toBe($notInvoiced);

    // Each breakdown says it is sales value, so its "Revenue" is not read as
    // the invoiced Revenue figure above it.
    $caption = __('sales::filament/widgets/sales-dashboard.sales-value');

    expect((new SalesPersonPerformanceChart)->getDescription())->toBe($caption)
        ->and((new RevenueByCategoryChart)->getDescription())->toBe($caption)
        ->and((fn () => $this->getTableDescription())->call(new TopCustomersTable))->toBe($caption)
        ->and((fn () => $this->getTableDescription())->call(new TopProductsTable))->toBe($caption);
});

/**
 * Plan #9 R-9 (2026-11-24): the cards above the sales orders list are the
 * Sales dashboard's own key figures for this month, so on the last day of
 * March they show exactly the March figures: no cancelled order, February
 * order, draft invoice or other client.
 */
it('shows this month above the sales orders list, the same figures as the dashboard', function (): void {
    $this->travelTo(Carbon::parse('2026-03-31 12:00'));

    $own = Company::factory()->create();
    $other = CompanyHelper::asNobody(fn () => Company::factory()->create());

    DashboardFixtureHelper::sales($own, $other);

    $user = CompanyHelper::actingAsCompanyUser($own, []);
    $user->forceFill(['resource_permission' => 'global'])->saveQuietly();

    $widgets = (new ListOrders)->getHeaderWidgets();

    expect($widgets)->toHaveCount(1)
        ->and($widgets[0]->widget)->toBe(SaleStatsOverview::class)
        ->and($widgets[0]->getProperties()['pageFilters'])->toBe(['startDate' => '2026-03-01', 'endDate' => '2026-03-31']);

    $cards = new SaleStatsOverview;

    foreach ($widgets[0]->getProperties() as $property => $value) {
        $cards->{$property} = $value;
    }

    $figures = (fn (): array => [
        'revenue'    => $this->invoicedRevenue(),
        'sales'      => $this->salesCount(),
        'quotations' => $this->quotations()->count(),
    ])->call($cards);

    expect($figures)->toBe(['revenue' => 1200.0, 'sales' => 3, 'quotations' => 1])
        ->and($cards->getHeading())->toBe(__('sales::filament/widgets/sales-dashboard.stats.this-month'))
        ->and((new SaleStatsOverview)->getHeading())->toBe(__('sales::filament/widgets/sales-dashboard.stats.heading'));
});

/**
 * The money-figures switch (users.can_see_money_figures) hides the month's
 * revenue above the orders list, as on the invoice, bill and purchase order
 * lists; the widget's own guard does not read it.
 */
it('hides the month figures above the sales orders list when money figures are off', function (): void {
    $company = Company::factory()->create();
    $user = CompanyHelper::actingAsCompanyUser($company, []);

    expect((new ListOrders)->getHeaderWidgets())->toHaveCount(1);

    $user->forceFill(['can_see_money_figures' => false])->save();

    expect((new ListOrders)->getHeaderWidgets())->toBe([]);
});
