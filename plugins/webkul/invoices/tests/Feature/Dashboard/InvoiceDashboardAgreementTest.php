<?php

use Illuminate\Support\Carbon;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\InvoiceResource\Pages\ListInvoices;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\BillResource\Pages\ListBills;
use Webkul\Invoice\Filament\Widgets\BillStatsWidget;
use Webkul\Invoice\Filament\Widgets\CustomerRevenueChart;
use Webkul\Invoice\Filament\Widgets\InvoiceStatsWidget;
use Webkul\Invoice\Filament\Widgets\InvoiceStatusChart;
use Webkul\Invoice\Filament\Widgets\RevenueOverTimeWidget;
use Webkul\Invoice\Filament\Widgets\SalespersonPerformanceChart;
use Webkul\Invoice\Filament\Widgets\TopInvoicesWidget;
use Webkul\PluginManager\Support\PluginAccess;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/DashboardFixtureHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('invoices');
    PluginAccess::flush();
});

/**
 * @param  class-string  $class
 */
function agreementWidget(string $class): object
{
    $widget = new $class;

    $widget->pageFilters = ['start_date' => DashboardFixtureHelper::START, 'end_date' => DashboardFixtureHelper::END];

    return $widget;
}

/**
 * Dashboard plan DB-2 (2026-11-23): on one dashboard, for one filter, the
 * tile, the charts and the table must tell the same story. Before this, the
 * Customer Revenue ring and the Salesperson bars summed invoices alone while
 * the "Total invoiced" tile beside them deducted credit notes, so the ring's
 * total was larger than the tile's whenever a credit note existed.
 *
 * The figures are the fixture's known totals (DashboardFixtureHelper); the
 * other company's invoice, a draft and a February invoice must never count.
 */
it('tells the same story in the invoice tiles, charts and table', function (): void {
    $own = Company::factory()->create();
    $other = CompanyHelper::asNobody(fn () => Company::factory()->create());

    $fixture = DashboardFixtureHelper::invoices($own, $other);

    // Everyone sees their company's records by default (resource_permission
    // "global", 2026-09-28). A user made in memory carries null until it is
    // reloaded, which Bouncer reads as "own records only", hiding every
    // fixture row this user did not create.
    $user = CompanyHelper::actingAsCompanyUser($own, []);
    $user->forceFill(['resource_permission' => 'global'])->saveQuietly();

    $stats = agreementWidget(InvoiceStatsWidget::class);
    $tiles = (fn (): array => [
        'total'  => $this->aggregate($this->customerInvoicesNet(), true),
        'count'  => $this->aggregate($this->scope(), false),
        'paid'   => $this->aggregate($this->stateScope('paid'), false),
        'unpaid' => $this->aggregate($this->unpaidOnly($this->scope()), false),
    ])->call($stats);

    $byState = (fn (): array => $this->getData())->call(agreementWidget(InvoiceStatusChart::class));
    $byCustomer = (fn (): array => $this->getData())->call(agreementWidget(CustomerRevenueChart::class));
    $bySalesperson = (fn (): array => $this->getData())->call(agreementWidget(SalespersonPerformanceChart::class));
    $paidPerDay = (fn (): array => $this->getData())->call(agreementWidget(RevenueOverTimeWidget::class));
    $topRows = (fn () => $this->getTableQuery()->get())->call(agreementWidget(TopInvoicesWidget::class));

    $states = array_combine($byState['labels'], $byState['datasets'][0]['data']);

    expect($tiles)->toBe(['total' => 1800.0, 'count' => 3.0, 'paid' => 1.0, 'unpaid' => 2.0])
        // The ring and the bars add up to the tile, net of the credit note.
        ->and(array_sum($byCustomer['datasets'][0]['data']))->toBe($tiles['total'])
        ->and(array_combine($byCustomer['labels'], $byCustomer['datasets'][0]['data']))->toBe(['Customer X' => 1200.0, 'Customer Y' => 600.0])
        ->and(array_sum($bySalesperson['datasets'][0]['data']))->toBe($tiles['total'])
        // "Invoiced amount by payment status" is gross, as its description
        // says: it is the tile plus the credit note, never anything else.
        ->and(array_sum($states))->toBe($tiles['total'] + 200.0)
        ->and(count($states))->toBe(3)
        // The paid-per-day line is the "Paid" bar spread over the days.
        ->and(array_sum($paidPerDay['datasets'][0]['data']))->toBe(1000.0)
        ->and(max($states))->toBe(1000.0)
        // The table lists the period's three invoices, largest first, and
        // neither the credit note nor any trap.
        ->and($topRows->pluck('id')->all())->toBe([
            $fixture['invoices']['A']->getKey(),
            $fixture['invoices']['B']->getKey(),
            $fixture['invoices']['C']->getKey(),
        ]);
});

/**
 * Plan #10 R-9 (2026-11-24): the cards above the invoice list are the
 * dashboard's own widget for this month, so on the last day of March they
 * show exactly the March tiles: net of the credit note, without the draft,
 * February or the other client.
 */
it('shows this month above the invoice list, the same figures as the dashboard', function (): void {
    $this->travelTo(Carbon::parse('2026-03-31 12:00'));

    $own = Company::factory()->create();
    $other = CompanyHelper::asNobody(fn () => Company::factory()->create());

    DashboardFixtureHelper::invoices($own, $other);

    $user = CompanyHelper::actingAsCompanyUser($own, []);
    $user->forceFill(['resource_permission' => 'global'])->saveQuietly();

    $widgets = (new ListInvoices)->getHeaderWidgets();

    expect($widgets)->toHaveCount(1)
        ->and($widgets[0]->widget)->toBe(InvoiceStatsWidget::class);

    $cards = new InvoiceStatsWidget;

    foreach ($widgets[0]->getProperties() as $property => $value) {
        $cards->{$property} = $value;
    }

    $tiles = (fn (): array => [
        'total'  => $this->aggregate($this->customerInvoicesNet(), true),
        'count'  => $this->aggregate($this->scope(), false),
        'paid'   => $this->aggregate($this->stateScope('paid'), false),
        'unpaid' => $this->aggregate($this->unpaidOnly($this->scope()), false),
    ])->call($cards);

    expect($tiles)->toBe(['total' => 1800.0, 'count' => 3.0, 'paid' => 1.0, 'unpaid' => 2.0])
        ->and($cards->getHeading())->toBe(__('invoices::filament/widgets/invoice-dashboard.invoice-stats.this-month'))
        ->and((new InvoiceStatsWidget)->getHeading())->toBe(__('invoices::filament/widgets/invoice-dashboard.invoice-stats.heading'));
});

/**
 * Plan #10 R-9 (2026-11-24): the cards above the vendor bill list are the
 * dashboard's own bill widget for this month, so on the last day of March
 * they show exactly the March bill tiles: net of the vendor refund, without
 * the draft, February or the other client.
 */
it('shows this month above the vendor bill list, the same figures as the dashboard', function (): void {
    $this->travelTo(Carbon::parse('2026-03-31 12:00'));

    $own = Company::factory()->create();
    $other = CompanyHelper::asNobody(fn () => Company::factory()->create());

    DashboardFixtureHelper::bills($own, $other);

    $user = CompanyHelper::actingAsCompanyUser($own, []);
    $user->forceFill(['resource_permission' => 'global'])->saveQuietly();

    $widgets = (new ListBills)->getHeaderWidgets();

    expect($widgets)->toHaveCount(1)
        ->and($widgets[0]->widget)->toBe(BillStatsWidget::class);

    $cards = new BillStatsWidget;

    foreach ($widgets[0]->getProperties() as $property => $value) {
        $cards->{$property} = $value;
    }

    $billTiles = fn (): array => [
        'total'  => abs($this->aggregate($this->vendorBillsNet(), true)),
        'count'  => $this->aggregate($this->scope(), false),
        'paid'   => $this->aggregate($this->stateScope('paid'), false),
        'unpaid' => $this->aggregate($this->unpaidOnly($this->scope()), false),
    ];

    expect($billTiles->call($cards))->toBe(['total' => 1800.0, 'count' => 3.0, 'paid' => 1.0, 'unpaid' => 2.0])
        // The dashboard's own widget, filtered to March, says the same.
        ->and($billTiles->call(agreementWidget(BillStatsWidget::class)))->toBe($billTiles->call($cards))
        ->and($cards->getHeading())->toBe(__('invoices::filament/widgets/invoice-dashboard.bill-stats.this-month'))
        ->and((new BillStatsWidget)->getHeading())->toBe(__('invoices::filament/widgets/invoice-dashboard.bill-stats.heading'));
});

/**
 * Plan #10 R-9 (2026-11-24): the month's totals above the invoice and bill
 * lists are dashboard figures, so "See money figures" off removes them, as
 * it removes the Invoices dashboard. The widgets' own guard does not read
 * that switch; the lists do.
 */
it('hides the month figures above the invoice and bill lists when money figures are off', function (): void {
    $company = Company::factory()->create();
    $user = CompanyHelper::actingAsCompanyUser($company, []);

    expect((new ListInvoices)->getHeaderWidgets())->toHaveCount(1)
        ->and((new ListBills)->getHeaderWidgets())->toHaveCount(1);

    $user->forceFill(['can_see_money_figures' => false])->save();

    expect((new ListInvoices)->getHeaderWidgets())->toBe([])
        ->and((new ListBills)->getHeaderWidgets())->toBe([]);
});
