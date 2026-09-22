<?php

namespace Webkul\Logistics\Filament\Clusters\Finance\Pages;

use Filament\Pages\Page;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Webkul\Logistics\Filament\Clusters\Finance;
use Webkul\Logistics\Models\ShipmentCharge;
use Webkul\Logistics\Support\LogisticsAccess;

/**
 * Money earned but not yet invoiced.
 *
 * Every billable charge with no move line, across shipments, so nothing is
 * quietly forgotten between delivery and billing. Read-only: invoicing happens
 * on the shipment, where the charges can be seen in context.
 *
 * Rows are company-scoped by CompanyScope on ShipmentCharge; nothing here
 * removes that scope.
 */
class UnbilledCharges extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?int $navigationSort = 1;

    protected static ?string $cluster = Finance::class;

    protected static string $lang = 'logistics::invoicing.unbilled';

    protected string $view = 'logistics::filament.pages.unbilled-charges';

    public static function getNavigationLabel(): string
    {
        return __(static::$lang.'.title');
    }

    public function getTitle(): string
    {
        return __(static::$lang.'.title');
    }

    /**
     * Both permissions, as the dispatch board does. Shield generates
     * `page_logistics_unbilled_charges`, which grants the screen, but the page
     * lists charge amounts and customers - so a user who may open it must also
     * be allowed to see shipment financials.
     */
    public static function canAccess(): bool
    {
        $user = auth()->user();

        return ($user?->can('page_logistics_unbilled_charges') ?? false)
            && ($user?->can('view_financials_logistics_shipment') ?? false)
            && LogisticsAccess::enabledForCurrent();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => ShipmentCharge::query()
                ->uninvoiced()
                ->with(['shipment:id,name,customer_id', 'shipment.customer:id,name', 'currency:id,code']))
            ->columns([
                TextColumn::make('shipment.name')
                    ->label(__(static::$lang.'.columns.shipment'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('shipment.customer.name')
                    ->label(__(static::$lang.'.columns.customer'))
                    ->placeholder('—')
                    ->visibleFrom('sm'),

                TextColumn::make('description')
                    ->label(__(static::$lang.'.columns.description'))
                    ->searchable()
                    ->visibleFrom('md'),

                TextColumn::make('quantity')
                    ->label(__(static::$lang.'.columns.quantity'))
                    ->numeric()
                    ->visibleFrom('lg'),

                TextColumn::make('subtotal')
                    ->label(__(static::$lang.'.columns.subtotal'))
                    ->money(fn (Model $record): ?string => $record->currency?->code)
                    // Only meaningful when one currency is in view, so the
                    // currency filter below exists for exactly this.
                    ->summarize(Sum::make()->label(__(static::$lang.'.columns.total'))),
            ])
            ->filters([
                // `name`, not `code`: Currency::code is an accessor, not a
                // column, so filtering or sorting on it fails in SQL. The money
                // column above can still use ->code because that runs in PHP.
                SelectFilter::make('currency')
                    ->label(__(static::$lang.'.filters.currency'))
                    ->relationship('currency', 'name')
                    ->preload(),

                SelectFilter::make('shipment')
                    ->label(__(static::$lang.'.filters.shipment'))
                    ->relationship('shipment', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->defaultPaginationPageOption(25)
            ->defaultSort('created_at', 'desc');
    }
}
