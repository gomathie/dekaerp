<?php

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Support\PluginAccess;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseOrderResource;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\VendorResource;
use Webkul\Purchase\Filament\Admin\Clusters\Products\Resources\ProductResource as PurchaseProductResource;
use Webkul\Purchase\Filament\Admin\Pages\Purchases;
use Webkul\Purchase\Filament\Admin\Widgets\PurchaseHeadlineWidget;
use Webkul\Purchase\Filament\Admin\Widgets\PurchaseStatsWidget;
use Webkul\Purchase\Filament\Admin\Widgets\PurchaseTrendWidget;
use Webkul\Purchase\Filament\Admin\Widgets\TopOrdersWidget;
use Webkul\Purchase\Filament\Admin\Widgets\TopPurchasedProductsWidget;
use Webkul\Purchase\Filament\Admin\Widgets\TopRfqsWidget;
use Webkul\Purchase\Filament\Admin\Widgets\TopVendorsWidget;
use Webkul\Purchase\Models\Order;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/DashboardWidgetGuardHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../Helpers/PurchaseHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('purchases');
    PluginAccess::flush();
});

function revokePurchasesFor(Company $company): void
{
    $plugin = Plugin::query()->where('name', 'purchases')->firstOrFail();

    DB::table('company_plugin')
        ->where('company_id', $company->getKey())
        ->where('plugin_id', $plugin->getKey())
        ->delete();

    PluginAccess::flush();
}

it('generates the permission name the purchase dashboard checks', function (): void {
    FilamentHelper::bootAdminPanel();

    $generated = collect(FilamentShield::getPages())
        ->flatMap(fn (array $page): array => array_keys($page['permissions']))
        ->all();

    expect($generated)->toContain('page_purchase_purchases');
});

it('opens the purchase dashboard only with the permission and the Purchases module', function (): void {
    $company = Company::factory()->create();

    CompanyHelper::actingAsCompanyUser($company, []);

    expect(Purchases::canAccess())->toBeFalse();

    CompanyHelper::actingAsCompanyUser($company, ['page_purchase_purchases']);

    expect(Purchases::canAccess())->toBeTrue();

    revokePurchasesFor($company);

    expect(Purchases::canAccess())->toBeFalse();
});

/**
 * Upstream gave TopRfqsWidget no check at all. It now needs its own
 * permission and the Purchases module, like every other dashboard widget.
 */
it('guards the RFQ widget by permission and by the Purchases module', function (): void {
    FilamentHelper::bootAdminPanel();

    $company = Company::factory()->create();

    CompanyHelper::actingAsCompanyUser($company, []);

    expect(TopRfqsWidget::canView())->toBeFalse();

    $permission = array_key_first(FilamentShield::getWidgets()[TopRfqsWidget::class]['permissions'] ?? []);

    expect($permission)->not->toBeNull();

    CompanyHelper::actingAsCompanyUser($company, [$permission]);

    expect(TopRfqsWidget::canView())->toBeTrue();

    revokePurchasesFor($company);

    expect(TopRfqsWidget::canView())->toBeFalse();
});

it('counts orders per day and state in one grouped query within the company', function (): void {
    $company = Company::factory()->create();
    $otherCompany = Company::factory()->create();

    $user = CompanyHelper::actingAsCompanyUser($company, []);

    $orders = [
        PurchaseHelper::order(['company_id' => $company->getKey(), 'user_id' => $user->getKey()]),
        PurchaseHelper::order(['company_id' => $company->getKey(), 'user_id' => $user->getKey()]),
        // Another client's order is set up with nobody acting: the cross-company
        // write guard (UF-025) refuses this user creating it, as it should.
        CompanyHelper::asNobody(fn () => PurchaseHelper::order(['company_id' => $otherCompany->getKey()])),
    ];

    Order::withoutGlobalScopes()->whereKey($orders[0]->getKey())->update(['ordered_at' => '2026-03-01 10:00', 'state' => 'draft']);
    Order::withoutGlobalScopes()->whereKey($orders[1]->getKey())->update(['ordered_at' => '2026-03-02 10:00', 'state' => 'purchase']);
    Order::withoutGlobalScopes()->whereKey($orders[2]->getKey())->update(['ordered_at' => '2026-03-01 10:00', 'state' => 'draft']);

    $widget = new PurchaseTrendWidget;
    $widget->pageFilters = ['start_date' => '2026-03-01', 'end_date' => '2026-03-02'];

    DB::flushQueryLog();
    DB::enableQueryLog();

    $data = (fn (): array => $this->getData())->call($widget);

    $orderQueries = collect(DB::getQueryLog())
        // Data queries only, not one-off column lookups (pg_attribute).
        ->filter(fn (array $entry): bool => str_contains($entry['query'], 'from "purchases_orders"'))
        ->count();

    DB::disableQueryLog();

    $byLabel = collect($data['datasets'])->mapWithKeys(fn (array $set, int $index): array => [$index => $set['data']]);

    // Series order follows PurchaseTrendWidget::SERIES_COLORS: draft first, purchase third.
    expect($byLabel[0])->toBe([1, 0])
        ->and($byLabel[2])->toBe([0, 1])
        ->and($orderQueries)->toBe(1);
});

/**
 * Sentry DEKAERP-12 (2026-11-11): the Overview's purchases card summed
 * amount_total, a Sales column, and 500'd the page. Spend is the confirmed
 * orders' total_cc_amount, within the company.
 */
it('computes the overview purchase spend from confirmed orders in company currency', function (): void {
    $company = Company::factory()->create();
    $user = CompanyHelper::actingAsCompanyUser($company, ['page_purchase_purchases']);

    $confirmed = PurchaseHelper::order(['company_id' => $company->getKey(), 'user_id' => $user->getKey()]);
    $draft = PurchaseHelper::order(['company_id' => $company->getKey(), 'user_id' => $user->getKey()]);

    Order::withoutGlobalScopes()->whereKey($confirmed->getKey())->update(['ordered_at' => now(), 'state' => 'purchase', 'total_cc_amount' => 250]);
    Order::withoutGlobalScopes()->whereKey($draft->getKey())->update(['ordered_at' => now(), 'state' => 'draft', 'total_cc_amount' => 999]);

    $stats = (new PurchaseHeadlineWidget)->headlineStats([]);

    // Three since DB-4 (2026-11-23): To receive joined Spend and Open RFQs.
    // Confirmed orders is the fourth only with PurchaseStatsWidget's own
    // permission, which this user does not have.
    expect($stats)->toHaveCount(3)
        ->and((string) $stats[0]->getValue())->toContain('250')
        ->and((string) $stats[0]->getValue())->not->toContain('999')
        ->and((string) $stats[1]->getValue())->toBe('1')
        // Production 2026-11-22: the spend tile read "No sales"; it counts orders.
        // Read as text: since DB-9 the description is HTML with a trend pill.
        ->and(strip_tags((string) $stats[0]->getDescription()))->toStartWith('1 order')
        ->and(strip_tags((string) $stats[0]->getDescription()))->not->toContain('sale');
});

/**
 * DB-4 (approved mockup, 2026-11-23): the Purchases key figures are Spend,
 * Confirmed orders, Open RFQs and To receive. Confirmed orders shows only to
 * someone PurchaseStatsWidget admits, so moving it into the headline gives
 * it to no new role. To receive is today's state: confirmed orders whose
 * goods are not all in, whatever their date.
 */
it('shows four purchase figures, confirmed orders only to whoever its own widget admits', function (): void {
    FilamentHelper::bootAdminPanel();

    $company = Company::factory()->create();
    $user = CompanyHelper::actingAsCompanyUser($company, ['page_purchase_purchases']);

    foreach (['pending', 'partial', 'full'] as $receipt) {
        $order = PurchaseHelper::order(['company_id' => $company->getKey(), 'user_id' => $user->getKey()]);
        Order::withoutGlobalScopes()->whereKey($order->getKey())->update(['ordered_at' => now()->subYears(2), 'state' => 'purchase', 'receipt_status' => $receipt]);
    }

    $labels = fn (): array => array_map(fn ($stat): string => (string) $stat->getLabel(), (new PurchaseHeadlineWidget)->headlineStats([]));
    $toReceive = fn (): string => (string) collect((new PurchaseHeadlineWidget)->headlineStats([]))->last()->getValue();

    expect($labels())->toBe(['Purchase spend', 'Open RFQs', 'To receive'])
        ->and($toReceive())->toBe('2');

    $permission = array_key_first(FilamentShield::getWidgets()[PurchaseStatsWidget::class]['permissions'] ?? []);
    CompanyHelper::actingAsCompanyUser($company, ['page_purchase_purchases', $permission]);

    expect($labels())->toBe(['Purchase spend', 'Confirmed Orders', 'Open RFQs', 'To receive']);
});

/**
 * U69 (2026-11-23): TRACE365's two July orders left the Purchases tab at
 * zero with no word of why. An empty period now names the latest order.
 */
it('names the latest purchase order when the period has none', function (): void {
    $company = Company::factory()->create();
    $user = CompanyHelper::actingAsCompanyUser($company, ['page_purchase_purchases']);

    $order = PurchaseHelper::order(['company_id' => $company->getKey(), 'user_id' => $user->getKey()]);
    Order::withoutGlobalScopes()->whereKey($order->getKey())->update(['ordered_at' => '2025-07-01 09:00', 'state' => 'purchase']);

    $thisMonth = ['start_date' => now()->startOfMonth()->toDateString(), 'end_date' => now()->toDateString()];

    expect((new PurchaseHeadlineWidget)->latestBeforePeriod($thisMonth)?->toDateString())->toBe('2025-07-01')
        ->and((new PurchaseHeadlineWidget)->latestBeforePeriod(['start_date' => '2025-01-01', 'end_date' => '2025-12-31']))->toBeNull();
});

/**
 * Dashboard plan DB-3 (2026-11-23): every widget this dashboard lists is
 * absent without its permission or once the module is off, and present with
 * both. A widget added without its guard fails here.
 */
it('shows every purchase dashboard widget only with its permission and the module', function (): void {
    FilamentHelper::bootAdminPanel();

    $company = Company::factory()->create();

    expect(DashboardWidgetGuardHelper::visibility(Purchases::sectionWidgets(), 'purchases', $company))
        ->toBe(DashboardWidgetGuardHelper::guarded(Purchases::sectionWidgets()));
});

/**
 * DB-9 (2026-11-24): every purchase dashboard list opens its whole list, and
 * only for someone who may open it. The resource answers, not a permission
 * name here.
 */
it('offers "View all" on the order, vendor and product lists only to who may open them', function (): void {
    FilamentHelper::bootAdminPanel();
    $company = Company::factory()->create();

    $headerActions = function (string $widgetClass): array {
        $widget = new $widgetClass;
        $widget->pageFilters = [];

        return array_map(fn ($action): ?string => $action->getName(), array_values($widget->table(Table::make($widget))->getHeaderActions()));
    };

    $widgets = [TopOrdersWidget::class, TopVendorsWidget::class, TopPurchasedProductsWidget::class];

    CompanyHelper::actingAsCompanyUser($company, ['view_any_purchase_purchase::order', 'view_any_purchase_vendor', 'view_any_partner_partner', 'view_any_purchase_product', 'view_any_product_product']);

    expect(PurchaseOrderResource::canViewAny())->toBeTrue()
        ->and(VendorResource::canViewAny())->toBeTrue()
        ->and(PurchaseProductResource::canViewAny())->toBeTrue();

    foreach ($widgets as $widgetClass) {
        expect($headerActions($widgetClass))->toContain('dashboardViewAll');
    }

    CompanyHelper::actingAsCompanyUser($company, []);

    foreach ($widgets as $widgetClass) {
        expect($headerActions($widgetClass))->not->toContain('dashboardViewAll');
    }
});
