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
use Illuminate\Support\Facades\DB;
use Webkul\PluginManager\Support\PluginEntitlement;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Filament\Clusters\Reporting;
use Webkul\Sale\Filament\Exports\SalesByProductExporter;
use Webkul\Sale\Models\OrderLine;
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
            ->query(OrderLine::query()
                ->whereNotNull('product_id')
                ->whereHas('order', fn (Builder $order): Builder => $order->where('state', OrderState::SALE->value))
                ->with(['product.category', 'order.partner']))
            ->columns([
                TextColumn::make('product.name')->label(__($lang.'.columns.product'))->searchable(),
                TextColumn::make('order.name')->label(__($lang.'.columns.order')),
                TextColumn::make('order.date_order')->label(__($lang.'.columns.date'))->date(),
                TextColumn::make('order.partner.name')->label(__($lang.'.columns.customer'))->toggleable(),
                TextColumn::make('product_uom_qty')
                    ->label(__($lang.'.columns.quantity'))
                    ->numeric(2)
                    ->sortable()
                    ->summarize(Summarizer::make()->label(__($lang.'.sum'))->numeric(2)
                        ->using(fn (QueryBuilder $query): float => (float) $query->sum('product_uom_qty'))),
                TextColumn::make('price_subtotal')
                    ->label(__($lang.'.columns.untaxed'))
                    ->state(fn (OrderLine $record): float => static::inCompanyCurrency($record))
                    ->numeric(2)
                    ->sortable()
                    ->summarize(Summarizer::make()->label(__($lang.'.sum'))->numeric(2)
                        ->using(fn (QueryBuilder $query): float => (float) $query->sum(DB::raw(static::companyCurrencySql())))),
            ])
            ->groups([
                Group::make('product.name')->label(__($lang.'.groups.product')),
                Group::make('product.category.name')->label(__($lang.'.groups.category')),
            ])
            ->defaultGroup('product.name')
            ->filters([
                Filter::make('period')
                    ->schema([
                        DatePicker::make('from')->label(__($lang.'.filters.from')),
                        DatePicker::make('until')->label(__($lang.'.filters.until')),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['from'] ?? null, fn (Builder $q, $date): Builder => $q->whereHas('order', fn (Builder $o): Builder => $o->whereDate('date_order', '>=', $date)))
                        ->when($data['until'] ?? null, fn (Builder $q, $date): Builder => $q->whereHas('order', fn (Builder $o): Builder => $o->whereDate('date_order', '<=', $date)))),
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

    /** The line's untaxed amount divided by its order's rate, as Sales analysis does. */
    public static function inCompanyCurrency(OrderLine $line): float
    {
        $rate = (float) $line->order?->currency_rate;

        return $rate > 0 ? round((float) $line->price_subtotal / $rate, 2) : (float) $line->price_subtotal;
    }

    /** The same in SQL for the totals. Fixed column names, never input. */
    protected static function companyCurrencySql(): string
    {
        return 'price_subtotal / COALESCE(NULLIF((SELECT currency_rate FROM sales_orders WHERE sales_orders.id = sales_order_lines.order_id), 0), 1)';
    }
}
