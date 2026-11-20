<?php

namespace Webkul\Sale\Filament\Clusters\Reporting\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\ExportAction;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Page;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Webkul\PluginManager\Support\PluginEntitlement;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Filament\Clusters\Reporting;
use Webkul\Sale\Filament\Exports\SalesByProductExporter;
use Webkul\Sale\Models\OrderLine;
use Webkul\Sale\Support\DirectInvoices;
use Webkul\Support\Filament\Concerns\ChecksPagePermission;

/**
 * Sales by product (reports plan R2, "by product"; 2026-11-13): the lines of
 * confirmed orders, groupable by product or product category, with quantity
 * and untaxed amount totals in company currency. Same rule as Sales
 * analysis: confirmed orders only, ordered amounts.
 *
 * Lines are company-scoped by their own CompanyScope, and only lines of
 * orders the viewer may see are listed (whereHas applies the order's
 * company and ownership scopes). Notes and sections have no product and are
 * left out.
 */
class SalesByProduct extends Page implements HasTable
{
    use ChecksPagePermission, HasPageShield, InteractsWithTable;

    protected string $view = 'sales::filament.clusters.reporting.pages.analysis';

    protected static ?string $cluster = Reporting::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cube';

    protected static ?int $navigationSort = 2;

    protected static function getPagePermission(): ?string
    {
        return 'page_sale_sales_by_product';
    }

    /** Module switch and the page permission (see ChecksPagePermission). */
    public static function canAccess(): bool
    {
        return PluginEntitlement::enabledForCurrent('sales') && static::hasPagePermission();
    }

    public static function getNavigationLabel(): string
    {
        return __('sales::filament/clusters/reporting.by-product.title');
    }

    public function getTitle(): string
    {
        return __('sales::filament/clusters/reporting.by-product.title');
    }

    public function getSubheading(): ?string
    {
        return __('sales::filament/clusters/reporting.by-product.subheading');
    }

    public function table(Table $table): Table
    {
        $lang = 'sales::filament/clusters/reporting.by-product';

        return $table
            ->query(static::rowsQuery())
            ->columns([
                TextColumn::make('product_name')->label(__($lang.'.columns.product'))->searchable(),
                TextColumn::make('document')->label(__($lang.'.columns.order')),
                TextColumn::make('date')->label(__($lang.'.columns.date'))->date(),
                TextColumn::make('customer')->label(__($lang.'.columns.customer'))->toggleable(),
                TextColumn::make('quantity')
                    ->label(__($lang.'.columns.quantity'))
                    ->numeric(2)
                    ->sortable()
                    ->summarize(Summarizer::make()->label(__($lang.'.sum'))->numeric(2)
                        ->using(fn (QueryBuilder $query): float => (float) $query->sum('quantity'))),
                TextColumn::make('untaxed')
                    ->label(__($lang.'.columns.untaxed'))
                    ->numeric(2)
                    ->sortable()
                    ->summarize(Summarizer::make()->label(__($lang.'.sum'))->numeric(2)
                        ->using(fn (QueryBuilder $query): float => (float) $query->sum('untaxed'))),
            ])
            ->groups([
                Group::make('product_name')->label(__($lang.'.groups.product')),
                Group::make('category_name')->label(__($lang.'.groups.category')),
            ])
            ->defaultGroup('product_name')
            ->filters([
                Filter::make('period')
                    ->schema([
                        DatePicker::make('from')->label(__($lang.'.filters.from')),
                        DatePicker::make('until')->label(__($lang.'.filters.until')),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['from'] ?? null, fn (Builder $q, $date): Builder => $q->whereDate('sales_order_lines.date', '>=', $date))
                        ->when($data['until'] ?? null, fn (Builder $q, $date): Builder => $q->whereDate('sales_order_lines.date', '<=', $date))),
                SelectFilter::make('product_id')
                    ->label(__($lang.'.columns.product'))
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload(false),
            ])
            ->headerActions([
                ExportAction::make()
                    ->label(__($lang.'.export'))
                    ->icon('heroicon-o-arrow-up-tray')
                    ->exporter(SalesByProductExporter::class),
            ])
            ->defaultSort('id', 'desc')
            ->paginated([25, 50, 100]);
    }

    /**
     * One row per product line: confirmed orders' lines, plus the lines of
     * invoices made without an order (DirectInvoices: TRACE365 invoices
     * without orders, user 2026-11-20). Untaxed amounts in company currency;
     * a credit note counts negative. Invoice rows take a negative id so the
     * two sources never share a row key.
     *
     * Each part reads through its own company and ownership scopes. The outer
     * query is named `sales_order_lines` so OrderLine's product relation and
     * Filament's qualified columns resolve, and drops the model's scopes,
     * which the parts have already applied.
     */
    public static function rowsQuery(): Builder
    {
        $orderLines = OrderLine::query()
            ->whereNotNull('sales_order_lines.product_id')
            ->whereHas('order', fn (Builder $order): Builder => $order->where('state', OrderState::SALE->value))
            ->join('sales_orders as line_orders', 'line_orders.id', '=', 'sales_order_lines.order_id')
            ->leftJoin('products_products', 'products_products.id', '=', 'sales_order_lines.product_id')
            ->leftJoin('products_categories', 'products_categories.id', '=', 'products_products.category_id')
            ->leftJoin('partners_partners', 'partners_partners.id', '=', 'line_orders.partner_id')
            ->toBase()
            ->selectRaw('sales_order_lines.id as id, sales_order_lines.product_id as product_id, '
                .'products_products.name as product_name, products_categories.name as category_name, '
                .'line_orders.name as document, line_orders.date_order as date, partners_partners.name as customer, '
                .'sales_order_lines.product_uom_qty as quantity, '
                .'sales_order_lines.price_subtotal / COALESCE(NULLIF(line_orders.currency_rate, 0), 1) as untaxed');

        $invoiceLines = DirectInvoices::lines(DirectInvoices::invoices())
            ->leftJoin('products_products', 'products_products.id', '=', 'accounts_account_move_lines.product_id')
            ->leftJoin('products_categories', 'products_categories.id', '=', 'products_products.category_id')
            ->leftJoin('partners_partners', 'partners_partners.id', '=', 'direct_moves.partner_id')
            ->toBase()
            ->selectRaw('-accounts_account_move_lines.id as id, accounts_account_move_lines.product_id as product_id, '
                .'products_products.name as product_name, products_categories.name as category_name, '
                .'direct_moves.name as document, CAST(direct_moves.invoice_date AS timestamp) as date, partners_partners.name as customer, '
                .DirectInvoices::LINE_QUANTITY_SQL.' as quantity, '
                .DirectInvoices::lineAmountSql('price_subtotal').' as untaxed');

        return OrderLine::query()
            ->withoutGlobalScopes()
            ->fromSub($orderLines->unionAll($invoiceLines), 'sales_order_lines')
            ->select('sales_order_lines.*');
    }
}
