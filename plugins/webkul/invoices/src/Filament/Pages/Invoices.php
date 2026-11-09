<?php

namespace Webkul\Invoice\Filament\Pages;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Account\Enums\PaymentState;
use Webkul\Invoice\Filament\Widgets;
use Webkul\Partner\Models\Partner;
use Webkul\PluginManager\Support\PluginEntitlement;
use Webkul\Product\Models\Category;
use Webkul\Product\Models\Product;
use Webkul\Support\Enums\NavigationGroup;
use Webkul\Support\Filament\Concerns\ChecksPagePermission;
use Webkul\Support\Filament\Concerns\ListsCompanyMembers;
use Webkul\Support\Filament\Forms\Components\DashboardDateRange;

class Invoices extends BaseDashboard
{
    use BaseDashboard\Concerns\HasFiltersForm;
    use ChecksPagePermission;
    use HasPageShield;
    use ListsCompanyMembers;

    protected static string $routePath = 'invoice';

    /**
     * The name PermissionManager generates for this page (plugin key "invoice"
     * from the namespace). Pinned by InvoiceDashboardTest against
     * FilamentShield::getPages(): if the two ever disagree the page is
     * unopenable for everyone while tests that grant the name by hand pass.
     */
    protected static function getPagePermission(): ?string
    {
        return 'page_invoice_invoices';
    }

    /**
     * Module switch (SAAS-05) plus the page permission. This replaces
     * HasPageShield::canAccess(), so the permission half must be checked here
     * (ChecksPagePermission explains why).
     */
    public static function canAccess(): bool
    {
        return PluginEntitlement::enabledForCurrent('invoices')
            && static::hasPagePermission();
    }

    public static function getNavigationLabel(): string
    {
        return __('invoices::filament/pages/dashboard.navigation.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Dashboard;
    }

    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null
    {
        return null;
    }

    public function filtersForm(Schema $form): Schema
    {
        return $form->schema([
            Section::make()
                ->schema([
                    ...DashboardDateRange::make(
                        __('invoices::filament/pages/dashboard.filters.date-range'),
                        'start_date',
                        'end_date',
                        columnSpan: ['xl' => 2],
                    ),

                    Select::make('product_id')
                        ->label(__('invoices::filament/pages/dashboard.filters.product'))
                        ->options(fn () => Product::pluck('name', 'id')->toArray())
                        ->multiple()
                        ->searchable()
                        ->placeholder(__('invoices::filament/pages/dashboard.filters.all-products'))
                        ->live(),

                    Select::make('category_id')
                        ->label(__('invoices::filament/pages/dashboard.filters.category'))
                        ->options(fn () => Category::pluck('name', 'id')->toArray())
                        ->multiple()
                        ->searchable()
                        ->placeholder(__('invoices::filament/pages/dashboard.filters.all-categories'))
                        ->live(),

                    Select::make('customer_id')
                        ->label(__('invoices::filament/pages/dashboard.filters.customer'))
                        ->options(fn () => Partner::where('customer_rank', '>', 0)->pluck('name', 'id')->toArray())
                        ->multiple()
                        ->searchable()
                        ->placeholder(__('invoices::filament/pages/dashboard.filters.all-customers'))
                        ->live(),

                    Select::make('vendor_id')
                        ->label(__('invoices::filament/pages/dashboard.filters.vendor'))
                        ->options(fn () => Partner::where('supplier_rank', '>', 0)->pluck('name', 'id')->toArray())
                        ->multiple()
                        ->searchable()
                        ->placeholder(__('invoices::filament/pages/dashboard.filters.all-vendors'))
                        ->live(),

                    Select::make('salesperson_id')
                        ->label(__('invoices::filament/pages/dashboard.filters.salesperson'))
                        ->options(fn () => static::companyMemberOptions())
                        ->multiple()
                        ->searchable()
                        ->placeholder(__('invoices::filament/pages/dashboard.filters.all-salespersons'))
                        ->live(),

                    Select::make('payment_state')
                        ->label(__('invoices::filament/pages/dashboard.filters.payment-state'))
                        ->options(PaymentState::options())
                        ->multiple()
                        ->searchable()
                        ->placeholder(__('invoices::filament/pages/dashboard.filters.all-payment-states'))
                        ->live(),

                ])
                ->columnSpanFull()
                ->columns([
                    'default' => 1,
                    'sm'      => 2,
                    'md'      => 3,
                    'xl'      => 4,
                ]),
        ]);
    }

    public function getWidgetsContentComponent(): Component
    {
        return parent::getWidgetsContentComponent()
            ->extraAttributes([
                'data-dashboard-widgets'         => 'true',
                'wire:loading.class'             => 'dashboard-widgets--loading',
                'wire:target'                    => 'filters',
            ]);
    }

    public function getWidgets(): array
    {
        return [
            Widgets\InvoiceStatsWidget::class,
            Widgets\BillStatsWidget::class,
            Widgets\RevenueOverTimeWidget::class,
            Widgets\InvoiceStatusChart::class,
            Widgets\CustomerRevenueChart::class,
            Widgets\SalespersonPerformanceChart::class,
            Widgets\TopInvoicesWidget::class,
            Widgets\TopBillsWidget::class,
        ];
    }
}
