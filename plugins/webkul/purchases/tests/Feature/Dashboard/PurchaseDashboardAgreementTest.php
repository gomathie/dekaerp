<?php

use Illuminate\Support\Carbon;
use Webkul\PluginManager\Support\PluginAccess;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseOrderResource\Pages\ListPurchaseOrders;
use Webkul\Purchase\Filament\Admin\Widgets\PurchaseHeadlineWidget;
use Webkul\Purchase\Filament\Admin\Widgets\PurchaseStatsWidget;
use Webkul\Purchase\Filament\Admin\Widgets\TopOrdersWidget;
use Webkul\Purchase\Filament\Admin\Widgets\TopPurchasedProductsWidget;
use Webkul\Purchase\Filament\Admin\Widgets\TopRfqsWidget;
use Webkul\Purchase\Filament\Admin\Widgets\TopVendorsWidget;
use Webkul\Purchase\Filament\Admin\Widgets\VendorSpendChart;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/DashboardFixtureHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('purchases');
    PluginAccess::flush();
});

/**
 * @param  class-string  $class
 */
function purchaseAgreementWidget(string $class): object
{
    $widget = new $class;

    $widget->pageFilters = ['start_date' => DashboardFixtureHelper::START, 'end_date' => DashboardFixtureHelper::END];

    return $widget;
}

/**
 * Dashboard plan DB-2 (2026-11-23): the Purchases dashboard's figures, ring
 * and lists agree. Before this, the vendor ring, top vendors, top products
 * and top orders summed every order of the period, so a draft quote and a
 * cancelled order counted as spend while the "Spend" figure did not.
 *
 * The figures are the fixture's known totals (DashboardFixtureHelper::
 * purchases()); a cancelled order, a February order and the other
 * company's order must never count as spend.
 */
it('tells the same story in the purchase figures, ring and lists', function (): void {
    $own = Company::factory()->create();
    $other = CompanyHelper::asNobody(fn () => Company::factory()->create());

    $fixture = DashboardFixtureHelper::purchases($own, $other);

    // A user made in memory carries a null resource_permission, which
    // Bouncer reads as "own records only"; everyone gets "global".
    $user = CompanyHelper::actingAsCompanyUser($own, []);
    $user->forceFill(['resource_permission' => 'global'])->saveQuietly();

    $figures = (fn (): array => [
        'spend'     => (float) $this->confirmedPurchaseOrders()->sum('total_cc_amount'),
        'confirmed' => $this->countByState(['purchase', 'done']),
        'drafts'    => $this->countByState(['draft']),
        'value'     => $this->sumByState(['purchase', 'done']),
    ])->call(purchaseAgreementWidget(PurchaseStatsWidget::class));

    $ring = (fn (): array => $this->getData())->call(purchaseAgreementWidget(VendorSpendChart::class));
    $vendors = (fn () => $this->getTableQuery()->get())->call(purchaseAgreementWidget(TopVendorsWidget::class));
    $products = (fn () => $this->getTableQuery()->get())->call(purchaseAgreementWidget(TopPurchasedProductsWidget::class));
    $orders = (fn () => $this->getTableQuery()->get())->call(purchaseAgreementWidget(TopOrdersWidget::class));
    $rfqs = (fn () => $this->getTableQuery()->get())->call(purchaseAgreementWidget(TopRfqsWidget::class));
    $headline = (fn (): float => (float) $this->confirmedPurchaseOrders()->sum('total_cc_amount'))->call(purchaseAgreementWidget(PurchaseHeadlineWidget::class));

    expect($figures)->toBe(['spend' => 1500.0, 'confirmed' => 2.0, 'drafts' => 1.0, 'value' => 1500.0])
        // The headline Spend and the stats' purchase value are one figure.
        ->and($headline)->toBe($figures['spend'])
        // The vendor ring and the top vendors add up to it.
        ->and(array_combine($ring['labels'], $ring['datasets'][0]['data']))->toBe(['Vendor V' => 1000.0, 'Vendor W' => 500.0])
        ->and(array_sum($ring['datasets'][0]['data']))->toBe($figures['spend'])
        ->and($vendors->mapWithKeys(fn ($row): array => [$row->name => (float) $row->total_spent])->all())->toBe(['Vendor V' => 1000.0, 'Vendor W' => 500.0])
        ->and($vendors->sum(fn ($row): int => (int) $row->orders_count))->toBe((int) $figures['confirmed'])
        // Top products are what was bought, not quoted or cancelled.
        ->and($products->mapWithKeys(fn ($row): array => [$row->name => [(float) $row->total_value, (float) $row->total_quantity]])->all())->toBe(['Goods' => [1000.0, 10.0], 'Services' => [500.0, 5.0]])
        // Top orders are orders; the quote is in Top RFQs.
        ->and($orders->pluck('id')->all())->toBe([$fixture['PO-1']->getKey(), $fixture['PO-2']->getKey()])
        ->and($rfqs->pluck('id')->all())->toBe([$fixture['RFQ-1']->getKey()]);
});

/**
 * Plan #10 R-9 (2026-11-24): the cards above the purchase order list are the
 * Purchases dashboard's own figures widget for this month, so on the last
 * day of March they show exactly the March figures: the cancelled order,
 * February and the other client never count.
 */
it('shows this month above the purchase order list, the same figures as the dashboard', function (): void {
    $this->travelTo(Carbon::parse('2026-03-31 12:00'));

    $own = Company::factory()->create();
    $other = CompanyHelper::asNobody(fn () => Company::factory()->create());

    DashboardFixtureHelper::purchases($own, $other);

    $user = CompanyHelper::actingAsCompanyUser($own, []);
    $user->forceFill(['resource_permission' => 'global'])->saveQuietly();

    $widgets = (new ListPurchaseOrders)->getHeaderWidgets();

    expect($widgets)->toHaveCount(1)
        ->and($widgets[0]->widget)->toBe(PurchaseStatsWidget::class);

    $cards = new PurchaseStatsWidget;

    foreach ($widgets[0]->getProperties() as $property => $value) {
        $cards->{$property} = $value;
    }

    $figures = fn (): array => [
        'drafts'    => $this->countByState(['draft']),
        'sent'      => $this->countByState(['sent']),
        'confirmed' => $this->countByState(['purchase', 'done']),
        'value'     => $this->sumByState(['purchase', 'done']),
    ];

    expect($figures->call($cards))->toBe(['drafts' => 1.0, 'sent' => 0.0, 'confirmed' => 2.0, 'value' => 1500.0])
        // The dashboard's own widget, filtered to March, says the same.
        ->and($figures->call(purchaseAgreementWidget(PurchaseStatsWidget::class)))->toBe($figures->call($cards))
        ->and($cards->getHeading())->toBe(__('purchases::filament/admin/widgets/purchase-dashboard.stats.this-month'))
        ->and((new PurchaseStatsWidget)->getHeading())->toBe(__('purchases::filament/admin/widgets/purchase-dashboard.stats.heading'));
});

/**
 * Plan #10 R-9 (2026-11-24): the month's purchase value is a dashboard
 * figure, so "See money figures" off removes the cards, as it removes the
 * Purchases dashboard. The widget's own guard does not read that switch.
 */
it('hides the month figures above the purchase order list when money figures are off', function (): void {
    $company = Company::factory()->create();
    $user = CompanyHelper::actingAsCompanyUser($company, []);

    expect((new ListPurchaseOrders)->getHeaderWidgets())->toHaveCount(1);

    $user->forceFill(['can_see_money_figures' => false])->save();

    expect((new ListPurchaseOrders)->getHeaderWidgets())->toBe([]);
});
