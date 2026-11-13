<?php

namespace Webkul\Sale\Filament\Clusters\Reporting\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\ExportAction;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Page;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\PluginManager\Support\PluginEntitlement;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Filament\Clusters\Reporting;
use Webkul\Sale\Filament\Exports\SalesAnalysisExporter;
use Webkul\Sale\Models\Order;
use Webkul\Support\Filament\CompanyMembers;
use Webkul\Support\Filament\Concerns\ChecksPagePermission;

/**
 * Sales analysis (reports plan R2, 2026-11-13): confirmed orders with their
 * untaxed and total amounts, groupable by customer, salesperson or month,
 * with totals per group, filters and export. One page instead of one per
 * view ("no overkill", user, 2026-11-12). It counts ordered amounts
 * (confirmed orders), not invoiced or delivered ones.
 *
 * Rows are company-scoped by the model's CompanyScope; nothing here removes
 * it. Export runs the filtered query (getTableQueryForExport()).
 */
class SalesAnalysis extends Page implements HasTable
{
    use ChecksPagePermission, HasPageShield, InteractsWithTable;

    protected string $view = 'sales::filament.clusters.reporting.pages.analysis';

    protected static ?string $cluster = Reporting::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar-square';

    protected static function getPagePermission(): ?string
    {
        return 'page_sale_sales_analysis';
    }

    /**
     * Module switch and the page permission. Written out because a
     * canAccess() here replaces HasPageShield's (see ChecksPagePermission).
     */
    public static function canAccess(): bool
    {
        return PluginEntitlement::enabledForCurrent('sales') && static::hasPagePermission();
    }

    public static function getNavigationLabel(): string
    {
        return __('sales::filament/clusters/reporting.analysis.title');
    }

    public function getTitle(): string
    {
        return __('sales::filament/clusters/reporting.analysis.title');
    }

    public function getSubheading(): ?string
    {
        return __('sales::filament/clusters/reporting.analysis.subheading');
    }

    public function table(Table $table): Table
    {
        $lang = 'sales::filament/clusters/reporting.analysis';

        return $table
            // Confirmed orders only, the same rule as the Sales dashboard.
            ->query(Order::query()->where('state', OrderState::SALE->value)->with(['partner', 'user', 'currency']))
            ->columns([
                TextColumn::make('name')->label(__($lang.'.columns.reference'))->searchable(),
                TextColumn::make('date_order')->label(__($lang.'.columns.date'))->date()->sortable(),
                TextColumn::make('partner.name')->label(__($lang.'.columns.customer'))->searchable(),
                TextColumn::make('user.name')->label(__($lang.'.columns.salesperson'))->placeholder('—'),
                // In company currency, as on the Sales dashboard: an order in
                // another currency is divided by its rate, so totals never
                // add cedis to dollars.
                TextColumn::make('amount_untaxed')
                    ->label(__($lang.'.columns.untaxed'))
                    ->state(fn (Order $record): float => static::inCompanyCurrency($record, (float) $record->amount_untaxed))
                    ->numeric(2)
                    ->sortable()
                    ->summarize(Summarizer::make()->label(__($lang.'.sum'))->numeric(2)
                        ->using(fn (QueryBuilder $query): float => (float) $query->sum(DB::raw(static::companyCurrencySql('amount_untaxed'))))),
                TextColumn::make('amount_total')
                    ->label(__($lang.'.columns.total'))
                    ->state(fn (Order $record): float => static::inCompanyCurrency($record, (float) $record->amount_total))
                    ->numeric(2)
                    ->sortable()
                    ->summarize(Summarizer::make()->label(__($lang.'.sum'))->numeric(2)
                        ->using(fn (QueryBuilder $query): float => (float) $query->sum(DB::raw(static::companyCurrencySql('amount_total'))))),
            ])
            ->groups([
                Group::make('partner.name')->label(__($lang.'.groups.customer')),
                Group::make('user.name')->label(__($lang.'.groups.salesperson')),
                Group::make('date_order')->label(__($lang.'.groups.month'))
                    ->getKeyFromRecordUsing(fn (Order $record): string => $record->date_order?->format('Y-m') ?? '')
                    ->getTitleFromRecordUsing(fn (Order $record): string => $record->date_order?->translatedFormat('F Y') ?? '—')
                    ->orderQueryUsing(fn (Builder $query, string $direction): Builder => $query->orderBy('date_order', $direction)),
            ])
            ->filters([
                Filter::make('period')
                    ->schema([
                        DatePicker::make('from')->label(__($lang.'.filters.from')),
                        DatePicker::make('until')->label(__($lang.'.filters.until')),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['from'] ?? null, fn (Builder $q, $date): Builder => $q->whereDate('date_order', '>=', $date))
                        ->when($data['until'] ?? null, fn (Builder $q, $date): Builder => $q->whereDate('date_order', '<=', $date))),
                SelectFilter::make('partner_id')
                    ->label(__($lang.'.columns.customer'))
                    ->relationship('partner', 'name')
                    ->searchable()
                    ->preload(false),
                SelectFilter::make('user_id')
                    ->label(__($lang.'.columns.salesperson'))
                    ->options(fn (): array => CompanyMembers::options()),
            ])
            ->headerActions([
                ExportAction::make()
                    ->label(__($lang.'.export'))
                    ->icon('heroicon-o-arrow-up-tray')
                    ->exporter(SalesAnalysisExporter::class),
            ])
            ->defaultSort('date_order', 'desc')
            ->paginated([25, 50, 100]);
    }

    public static function inCompanyCurrency(Order $order, float $amount): float
    {
        return (float) $order->currency_rate > 0 ? round($amount / (float) $order->currency_rate, 2) : $amount;
    }

    /** The same conversion in SQL, for the column totals. Column names are fixed, never input. */
    protected static function companyCurrencySql(string $column): string
    {
        return "CASE WHEN currency_rate > 0 THEN {$column} / currency_rate ELSE {$column} END";
    }
}
