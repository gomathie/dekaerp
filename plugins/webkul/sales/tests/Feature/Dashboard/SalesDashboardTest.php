<?php

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Support\PluginAccess;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Filament\Clusters\Reporting\Pages\SalesAnalysis;
use Webkul\Sale\Filament\Pages\SalesDashboard;
use Webkul\Sale\Filament\Widgets\MonthlySalesChart;
use Webkul\Sale\Filament\Widgets\RevenueByCategoryChart;
use Webkul\Sale\Filament\Widgets\SalesPersonPerformanceChart;
use Webkul\Sale\Filament\Widgets\SaleStatsOverview;
use Webkul\Sale\Filament\Widgets\TopCustomersTable;
use Webkul\Sale\Filament\Widgets\TopProductsTable;
use Webkul\Sale\Filament\Widgets\TopQuotationsTable;
use Webkul\Sale\Filament\Widgets\TopSalesOrdersTable;
use Webkul\Sale\Models\Order;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../Helpers/SaleHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('sales');
    PluginAccess::flush();
});

function revokeSalesFor(Company $company): void
{
    $plugin = Plugin::query()->where('name', 'sales')->firstOrFail();

    DB::table('company_plugin')
        ->where('company_id', $company->getKey())
        ->where('plugin_id', $plugin->getKey())
        ->delete();

    PluginAccess::flush();
}

it('generates the permission name the sales dashboard checks', function (): void {
    // Every other test grants the name by hand, so a mismatch with what Shield
    // generates would pass here and leave the page unopenable in production.
    FilamentHelper::bootAdminPanel();

    $generated = collect(FilamentShield::getPages())
        ->flatMap(fn (array $page): array => array_keys($page['permissions']))
        ->all();

    expect($generated)->toContain('page_sale_sales_dashboard');
});

it('opens the sales dashboard only with the permission and the Sales module', function (): void {
    $company = Company::factory()->create();

    CompanyHelper::actingAsCompanyUser($company, []);

    expect(SalesDashboard::canAccess())->toBeFalse();

    CompanyHelper::actingAsCompanyUser($company, ['page_sale_sales_dashboard']);

    expect(SalesDashboard::canAccess())->toBeTrue();

    revokeSalesFor($company);

    expect(SalesDashboard::canAccess())->toBeFalse();
});

it('hides the sales widgets once Sales is revoked for the company', function (): void {
    FilamentHelper::bootAdminPanel();

    $company = Company::factory()->create();

    $widgetPermission = array_key_first(FilamentShield::getWidgets()[SaleStatsOverview::class]['permissions'] ?? []);

    expect($widgetPermission)->not->toBeNull();

    CompanyHelper::actingAsCompanyUser($company, [$widgetPermission]);

    expect(SaleStatsOverview::canView())->toBeTrue();

    revokeSalesFor($company);

    expect(SaleStatsOverview::canView())->toBeFalse();
});

it('reads only the current company’s confirmed orders', function (): void {
    $company = Company::factory()->create();
    $otherCompany = Company::factory()->create();

    $user = CompanyHelper::actingAsCompanyUser($company, []);

    $own = SaleHelper::order(['company_id' => $company->getKey(), 'user_id' => $user->getKey()]);
    // Another client's order is set up with nobody acting: the cross-company
    // write guard (UF-025) refuses this user creating it, as it should.
    $foreign = CompanyHelper::asNobody(fn () => SaleHelper::order(['company_id' => $otherCompany->getKey()]));

    Order::withoutGlobalScopes()->whereKey([$own->getKey(), $foreign->getKey()])->update([
        'state'      => OrderState::SALE->value,
        'date_order' => now(),
    ]);

    $widget = new SaleStatsOverview;
    $widget->pageFilters = [];

    $ids = (fn () => $this->saleOrders()->pluck('id')->all())->call($widget);

    expect($ids)->toContain($own->getKey())
        ->not->toContain($foreign->getKey());
});

/**
 * DB-3 (2026-11-22): an empty top-quotations list offers "Create a
 * quotation", only to someone who may create one.
 */
it('offers the next step on an empty list only to users who may take it', function (): void {
    $company = Company::factory()->create();

    CompanyHelper::actingAsCompanyUser($company, ['create_sale_quotation', 'view_any_sale_quotation']);
    $widget = new TopQuotationsTable;
    $allowed = $widget->table(Table::make($widget));

    CompanyHelper::actingAsCompanyUser($company, []);
    $widget = new TopQuotationsTable;
    $refused = $widget->table(Table::make($widget));

    expect($allowed->getEmptyStateHeading())->toBe(__('support::filament/dashboard.empty-table'))
        ->and($allowed->getEmptyStateActions())->toHaveCount(1)
        ->and($refused->getEmptyStateActions())->toHaveCount(0);
});

/**
 * DB-7 (2026-11-22): top quotations rank by company-currency value. A USD
 * quotation of 100 at 15.5 (1,550 local) outranks a local one of 500, which
 * the old own-currency sort placed first.
 */
it('ranks top quotations by their value in the company currency', function (): void {
    $company = Company::factory()->create();
    $user = CompanyHelper::actingAsCompanyUser($company, []);

    $usd = SaleHelper::order(['company_id' => $company->getKey(), 'user_id' => $user->getKey()]);
    $local = SaleHelper::order(['company_id' => $company->getKey(), 'user_id' => $user->getKey()]);

    Order::withoutGlobalScopes()->whereKey($usd->getKey())->update(['state' => OrderState::DRAFT->value, 'date_order' => now(), 'amount_total' => 100, 'currency_rate' => round(1 / 15.5, 6)]);
    Order::withoutGlobalScopes()->whereKey($local->getKey())->update(['state' => OrderState::DRAFT->value, 'date_order' => now(), 'amount_total' => 500, 'currency_rate' => 1]);

    $widget = new TopQuotationsTable;
    $widget->pageFilters = [];

    $ids = (fn () => $this->getTableQuery()->pluck('id')->all())->call($widget);

    expect(array_slice($ids, 0, 2))->toBe([$usd->getKey(), $local->getKey()]);
});

/**
 * DB-8 (user, 2026-11-19): TRACE365 invoices without a sales order, so
 * revenue read from orders showed nothing. Revenue is posted customer
 * invoices less credit notes, in company currency, for this company only.
 */
it('counts invoices made without an order as sales revenue', function (): void {
    $company = Company::factory()->create();
    $otherCompany = Company::factory()->create();

    CompanyHelper::actingAsCompanyUser($company, []);

    $move = fn (int $companyId, string $type, string $state, float $signed) => DB::table('accounts_account_moves')->insert([
        'company_id'          => $companyId,
        'move_type'           => $type,
        'state'               => $state,
        'date'                => now()->toDateString(),
        'invoice_date'        => now()->toDateString(),
        'amount_total_signed' => $signed,
        // The test user's own rows: Move keeps its ownership scope.
        'creator_id'          => auth()->id(),
        'created_at'          => now(),
        'updated_at'          => now(),
    ]);

    $move($company->getKey(), 'out_invoice', 'posted', 300);
    $move($company->getKey(), 'out_invoice', 'posted', 200);
    $move($company->getKey(), 'out_refund', 'posted', -50);
    $move($company->getKey(), 'out_invoice', 'draft', 999);
    $move($company->getKey(), 'in_invoice', 'posted', -400);
    $move($otherCompany->getKey(), 'out_invoice', 'posted', 700);

    $widget = new SaleStatsOverview;
    $widget->pageFilters = ['startDate' => now()->startOfMonth()->toDateString(), 'endDate' => now()->toDateString()];

    expect((fn (): float => $this->invoicedRevenue())->call($widget))->toBe(450.0);

    // Sentry DEKAERP-1X: the daily revenue chart failed on PostgreSQL.
    $series = (fn (): array => $this->invoicedRevenueSeries(now()->startOfDay(), now()->endOfDay()))->call($widget);

    expect($series)->toBe([450.0]);

    expect(fn () => (fn (): array => $this->getStats())->call($widget))->not->toThrow(Throwable::class);
});

/**
 * User, 2026-11-20 (hotfix): products, categories, customers, salespersons
 * and the monthly chart read confirmed orders only, so a sale invoiced
 * without an order showed nowhere. They now add such invoices, and leave
 * out an invoice made from an order, whose order is counted already.
 */
it('counts products of invoices made without an order, once', function (): void {
    // SaleHelper::product() fills Inventory's is_storable column.
    TestBootstrapHelper::ensurePluginInstalled('inventories');

    $company = Company::factory()->create();
    $user = CompanyHelper::actingAsCompanyUser($company, []);

    $product = SaleHelper::product(['company_id' => $company->getKey()]);
    $partner = SaleHelper::partner();
    $currencyId = SaleHelper::currency()->getKey();

    $invoice = function (float $amount, float $qty) use ($company, $product, $partner, $currencyId, $user): int {
        $moveId = DB::table('accounts_account_moves')->insertGetId([
            'company_id'          => $company->getKey(),
            'partner_id'          => $partner->getKey(),
            'invoice_user_id'     => $user->getKey(),
            'move_type'           => 'out_invoice',
            'state'               => 'posted',
            'date'                => now()->toDateString(),
            'invoice_date'        => now()->toDateString(),
            'amount_total'        => $amount,
            'amount_total_signed' => $amount,
            'creator_id'          => $user->getKey(),
            'created_at'          => now(),
            'updated_at'          => now(),
        ]);

        DB::table('accounts_account_move_lines')->insert([
            'move_id'      => $moveId,
            'company_id'   => $company->getKey(),
            'currency_id'  => $currencyId,
            'product_id'   => $product->getKey(),
            'display_type' => 'product',
            'quantity'     => $qty,
            'price_total'  => $amount,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        return $moveId;
    };

    $invoice(120, 2);

    // An invoice made from an order: its order is what counts, not this.
    $fromOrder = $invoice(500, 5);
    $order = SaleHelper::order(['company_id' => $company->getKey(), 'user_id' => $user->getKey()]);
    DB::table('sales_order_invoices')->insert(['order_id' => $order->getKey(), 'move_id' => $fromOrder]);

    $filters = ['startDate' => now()->startOfMonth()->toDateString(), 'endDate' => now()->endOfMonth()->toDateString()];

    $products = new TopProductsTable;
    $products->pageFilters = $filters;
    $row = (fn () => $this->getTableQuery()->get())->call($products)->firstWhere('id', $product->getKey());

    expect((float) $row?->revenue)->toBe(120.0)
        ->and((float) $row?->quantity)->toBe(2.0);

    $customers = new TopCustomersTable;
    $customers->pageFilters = $filters;
    $customer = (fn () => $this->getTableQuery()->get())->call($customers)->firstWhere('id', $partner->getKey());

    expect((float) $customer?->revenue)->toBe(120.0);

    $monthly = new MonthlySalesChart;
    $monthly->pageFilters = $filters;

    expect(array_sum((fn (): array => $this->getData())->call($monthly)['datasets'][0]['data']))->toBe(120.0);

    $categories = new RevenueByCategoryChart;
    $categories->pageFilters = $filters;

    expect(array_sum((fn (): array => $this->resolveChartData())->call($categories)['data']))->toBe(120.0);

    $people = new SalesPersonPerformanceChart;
    $people->pageFilters = $filters;

    expect((fn (): array => $this->getData())->call($people)['datasets'][0]['data'])->toBe([120.0]);

    // The direct invoice is a sale: counted, and listed among the top sales.
    $stats = new SaleStatsOverview;
    $stats->pageFilters = $filters;

    expect((fn (): int => $this->salesCount())->call($stats))->toBe(1);

    $top = new TopSalesOrdersTable;
    $top->pageFilters = $filters;

    expect((fn () => $this->getTableQuery()->get())->call($top)->pluck('amount_total')->map(fn ($amount): float => (float) $amount)->all())
        ->toBe([120.0]);

    // Sales analysis lists it as a sale, and not the invoice made from an order.
    $rows = SalesAnalysis::rowsQuery()->get();

    expect($rows->where('id', '<', 0)->pluck('amount_total')->map(fn ($amount): float => (float) $amount)->all())->toBe([120.0]);
});

it('lists only the current company’s members as salespersons', function (): void {
    $company = Company::factory()->create();
    $otherCompany = Company::factory()->create();

    $member = CompanyHelper::actingAsCompanyUser($company, []);

    $outsider = User::factory()->create();
    $outsider->allowedCompanies()->sync([$otherCompany->getKey()]);

    $options = (fn (): array => static::companyMemberOptions())->bindTo(null, SalesDashboard::class)();

    expect($options)->toHaveKey($member->getKey())
        ->not->toHaveKey($outsider->getKey());
});

/**
 * DB-4 (approved mockup, 2026-11-23): the Sales tab's third breakdown says
 * what became of the period's quotations. It has no permission of its own:
 * it opens for whoever may see "Top quotations", and for nobody else.
 */
it('says what became of the period\'s quotations, for whoever may see top quotations', function (): void {
    FilamentHelper::bootAdminPanel();

    $company = Company::factory()->create();
    $user = CompanyHelper::actingAsCompanyUser($company, []);

    foreach ([OrderState::SALE, OrderState::SALE, OrderState::DRAFT, OrderState::SENT, OrderState::CANCEL] as $state) {
        $order = SaleHelper::order(['company_id' => $company->getKey(), 'user_id' => $user->getKey()]);
        Order::withoutGlobalScopes()->whereKey($order->getKey())->update(['state' => $state->value, 'date_order' => now()]);
    }

    $old = SaleHelper::order(['company_id' => $company->getKey(), 'user_id' => $user->getKey()]);
    Order::withoutGlobalScopes()->whereKey($old->getKey())->update(['state' => OrderState::SALE->value, 'date_order' => now()->subYears(2)]);

    $widget = new \Webkul\Sale\Filament\Widgets\QuotationOutcomeChart;
    $widget->pageFilters = [];

    expect($widget->outcomes())->toBe(['won' => 2, 'open' => 2, 'cancelled' => 1])
        ->and(\Webkul\Sale\Filament\Widgets\QuotationOutcomeChart::canView())->toBeFalse();

    $permission = array_key_first(FilamentShield::getWidgets()[TopQuotationsTable::class]['permissions'] ?? []);
    CompanyHelper::actingAsCompanyUser($company, [$permission]);

    expect(\Webkul\Sale\Filament\Widgets\QuotationOutcomeChart::canView())->toBeTrue();
});
