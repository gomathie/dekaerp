<?php

namespace Webkul\Sale\Filament\Pages;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Widgets\Widget;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Invoice\Filament\Pages\Overview;
use Webkul\PluginManager\Support\PluginEntitlement;
use Webkul\Product\Models\Category;
use Webkul\Sale\Filament\Widgets\MonthlySalesChart;
use Webkul\Sale\Filament\Widgets\RevenueByCategoryChart;
use Webkul\Sale\Filament\Widgets\SalesPersonPerformanceChart;
use Webkul\Sale\Filament\Widgets\SaleStatsOverview;
use Webkul\Sale\Filament\Widgets\TopCustomersTable;
use Webkul\Sale\Filament\Widgets\TopProductsTable;
use Webkul\Sale\Filament\Widgets\TopQuotationsTable;
use Webkul\Sale\Filament\Widgets\TopSalesOrdersTable;
use Webkul\Sale\Models\Product;
use Webkul\Sale\Models\Team;
use Webkul\Support\Enums\NavigationGroup;
use Webkul\Support\Filament\Concerns\ChecksPagePermission;
use Webkul\Support\Filament\Concerns\ListsCompanyMembers;
use Webkul\Support\Filament\Forms\Components\DashboardDateRange;
use Webkul\Support\Models\Country;

class SalesDashboard extends BaseDashboard
{
    use BaseDashboard\Concerns\HasFiltersForm;
    use ChecksPagePermission;
    use HasPageShield;
    use ListsCompanyMembers;

    protected static string $routePath = 'sales';

    /**
     * The name PermissionManager generates for this page (plugin key "sale"
     * from the namespace). Pinned by SalesDashboardTest against
     * FilamentShield::getPages().
     */
    protected static function getPagePermission(): ?string
    {
        return 'page_sale_sales_dashboard';
    }

    /**
     * Module switch (SAAS-05) plus the page permission. This replaces
     * HasPageShield::canAccess(), so the permission half must be checked here
     * (ChecksPagePermission explains why). Also decides the Sales section of
     * the combined Overview.
     */
    public static function canAccess(): bool
    {
        return PluginEntitlement::enabledForCurrent('sales')
            && static::hasPagePermission();
    }

    /**
     * Replaced in the menu by the combined Overview (user, 2026-11-11). The
     * page stays so old links work: it opens the Overview with the Sales
     * section chosen.
     */
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public function mount(): void
    {
        $this->redirect(Overview::getUrl(['section' => 'sales']));
    }

    public static function getNavigationLabel(): string
    {
        return __('sales::filament/pages/sales-dashboard.navigation.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Dashboard;
    }

    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null
    {
        return null;
    }

    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        ...DashboardDateRange::make(
                            __('sales::filament/pages/sales-dashboard.filters.date-range'),
                        ),
                        Select::make('products')
                            ->label(__('sales::filament/pages/sales-dashboard.filters.products'))
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->options(fn () => Product::pluck('name', 'id')),
                        Select::make('categories')
                            ->label(__('sales::filament/pages/sales-dashboard.filters.categories'))
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->options(fn () => Category::pluck('name', 'id')),
                        ...static::sectionFilterFields(),
                    ])
                    ->columnSpanFull()
                    ->columns([
                        'default' => 1,
                        'sm'      => 2,
                        'md'      => 3,
                        'xl'      => 6,
                    ]),
            ]);
    }

    /**
     * The filters only Sales has. Also shown on the Overview page when the
     * Sales section alone is chosen, so both pages build them here.
     *
     * @return array<int, Select>
     */
    public static function sectionFilterFields(): array
    {
        return [
            Select::make('countries')
                ->label(__('sales::filament/pages/sales-dashboard.filters.countries'))
                ->multiple()
                ->searchable()
                ->preload()
                ->options(fn () => Country::pluck('name', 'id')),
            Select::make('teams')
                ->label(__('sales::filament/pages/sales-dashboard.filters.teams'))
                ->multiple()
                ->searchable()
                ->preload()
                ->options(fn () => Team::pluck('name', 'id')),
            Select::make('salesPersons')
                ->label(__('sales::filament/pages/sales-dashboard.filters.sales-persons'))
                ->multiple()
                ->searchable()
                ->preload()
                ->options(fn () => static::companyMemberOptions()),
        ];
    }

    public function getWidgetsContentComponent(): Component
    {
        return parent::getWidgetsContentComponent()
            ->extraAttributes([
                'data-dashboard-widgets' => 'true',
                'wire:loading.class'     => 'dashboard-widgets--loading',
                'wire:target'            => 'filters',
            ]);
    }

    /**
     * @return array<class-string<Widget>>
     */
    public function getWidgets(): array
    {
        return static::sectionWidgets();
    }

    /**
     * The widgets this dashboard shows, also used by its Overview section.
     *
     * @return list<class-string<Widget>>
     */
    public static function sectionWidgets(): array
    {
        return [
            SaleStatsOverview::class,
            MonthlySalesChart::class,
            RevenueByCategoryChart::class,
            SalesPersonPerformanceChart::class,
            TopProductsTable::class,
            TopCustomersTable::class,
            TopSalesOrdersTable::class,
            TopQuotationsTable::class,
        ];
    }
}
