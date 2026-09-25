<?php

namespace Webkul\Logistics\Filament\Clusters\Reporting\Pages;

use BackedEnum;
use Filament\Actions\ExportAction;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Webkul\Logistics\Enums\ExpenseState;
use Webkul\Logistics\Filament\Exports\ShipmentProfitabilityExporter;
use Webkul\Logistics\Models\Shipment;

/**
 * Revenue less cost, per shipment (WP-11, D2).
 *
 * Behind view_financials as well as the page permission.
 *
 * Both figures come from aggregate subqueries rather than loading every charge
 * and expense: a register covering a quarter would otherwise pull tens of
 * thousands of rows into memory to add up two columns.
 *
 * What counts:
 *
 *  - revenue is billable charges, invoiced or not, because the work was done
 *    and is owed whether or not the invoice has gone out;
 *  - cost is approved and billed expenses only. A draft or submitted expense is
 *    a claim someone has made, and counting it would let the margin move on
 *    paperwork rather than on work.
 *
 * No currency conversion: figures are in each shipment's own currency, and a
 * column that silently added several together would look authoritative while
 * being meaningless. The totals row carries the same caveat, which is why the
 * page says so in its subheading.
 */
class ShipmentProfitability extends ReportPage
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calculator';

    protected static ?int $navigationSort = 3;

    protected static string $lang = 'logistics::reports.profitability';

    protected static function pagePermission(): string
    {
        return 'page_logistics_shipment_profitability';
    }

    protected static function requiresFinancials(): bool
    {
        return true;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Shipment::query()
                ->with(['customer', 'currency'])
                ->withSum(
                    ['charges as revenue_total' => fn (Builder $query) => $query->where('is_billable', true)],
                    'subtotal',
                )
                ->withSum(
                    ['expenses as cost_total' => fn (Builder $query) => $query->whereIn('state', [
                        ExpenseState::APPROVED->value,
                        ExpenseState::BILLED->value,
                    ])],
                    'amount',
                ))
            ->columns([
                TextColumn::make('name')
                    ->label(__(static::$lang.'.columns.reference'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('customer.name')
                    ->label(__(static::$lang.'.columns.customer'))
                    ->searchable()
                    ->visibleFrom('sm'),

                TextColumn::make('revenue_total')
                    ->label(__(static::$lang.'.columns.revenue'))
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                    ->summarize(Sum::make()->label(__(static::$lang.'.columns.revenue'))->numeric(decimalPlaces: 2)),

                TextColumn::make('cost_total')
                    ->label(__(static::$lang.'.columns.costs'))
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                    ->summarize(Sum::make()->label(__(static::$lang.'.columns.costs'))->numeric(decimalPlaces: 2)),

                // Computed in PHP from the two aggregates rather than a third
                // subquery, and coloured because a loss-making job is the thing
                // this report exists to surface.
                TextColumn::make('margin')
                    ->label(__(static::$lang.'.columns.margin'))
                    ->state(fn (Model $record): float => (float) $record->revenue_total - (float) $record->cost_total)
                    ->numeric(decimalPlaces: 2)
                    ->color(fn ($state): string => $state < 0 ? 'danger' : 'success')
                    ->weight('bold'),

                TextColumn::make('currency.name')
                    ->label(__(static::$lang.'.columns.currency'))
                    ->visibleFrom('lg'),
            ])
            ->filters([
                SelectFilter::make('customer')
                    ->label(__(static::$lang.'.filters.customer'))
                    ->relationship('customer', 'name')
                    ->searchable()
                    ->preload(),

                Filter::make('delivered')
                    ->schema(DateRange::components(static::$lang))
                    ->query(fn (Builder $query, array $data): Builder => DateRange::apply($query, $data, 'actual_delivery_at'))
                    ->indicateUsing(fn (array $data): ?string => DateRange::indicator($data, static::$lang)),

                Filter::make('loss_making')
                    ->label(__(static::$lang.'.filters.loss-making'))
                    ->toggle()
                    // Compared in SQL, because the margin column is computed in
                    // PHP and cannot be filtered on.
                    // logistics_shipment_charges has no deleted_at - that model
                    // does not soft delete - while logistics_expenses does, so
                    // the two subqueries are deliberately not symmetrical.
                    ->query(fn (Builder $query): Builder => $query->whereRaw(
                        '(select coalesce(sum(subtotal), 0) from logistics_shipment_charges
                            where logistics_shipment_charges.shipment_id = logistics_shipments.id
                              and logistics_shipment_charges.is_billable = true)
                         < (select coalesce(sum(amount), 0) from logistics_expenses
                            where logistics_expenses.shipment_id = logistics_shipments.id
                              and logistics_expenses.state in (?, ?)
                              and logistics_expenses.deleted_at is null)',
                        [ExpenseState::APPROVED->value, ExpenseState::BILLED->value],
                    )),
            ])
            ->headerActions([
                ExportAction::make()
                    ->label(__(static::$lang.'.export'))
                    ->icon('heroicon-o-arrow-up-tray')
                    ->exporter(ShipmentProfitabilityExporter::class),
            ])
            ->defaultSort('actual_delivery_at', 'desc');
    }
}
