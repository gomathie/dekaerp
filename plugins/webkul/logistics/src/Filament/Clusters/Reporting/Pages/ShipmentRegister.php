<?php

namespace Webkul\Logistics\Filament\Clusters\Reporting\Pages;

use BackedEnum;
use Filament\Actions\ExportAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Logistics\Enums\ShipmentState;
use Webkul\Logistics\Filament\Exports\ShipmentRegisterExporter;
use Webkul\Logistics\Models\Shipment;

/**
 * Every shipment, filtered (WP-11).
 *
 * The plain list people reach for when asked "what did we move last month, for
 * whom, and where is it now".
 */
class ShipmentRegister extends ReportPage
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?int $navigationSort = 1;

    protected static string $lang = 'logistics::reports.shipment-register';

    protected static function pagePermission(): string
    {
        return 'page_logistics_shipment_register';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Shipment::query()->with(['customer', 'serviceType']))
            ->columns([
                TextColumn::make('name')
                    ->label(__(static::$lang.'.columns.reference'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('customer.name')
                    ->label(__(static::$lang.'.columns.customer'))
                    ->searchable(),

                TextColumn::make('state')
                    ->label(__(static::$lang.'.columns.state'))
                    ->badge(),

                TextColumn::make('origin_label')
                    ->label(__(static::$lang.'.columns.origin'))
                    ->visibleFrom('lg'),

                TextColumn::make('destination_label')
                    ->label(__(static::$lang.'.columns.destination'))
                    ->visibleFrom('lg'),

                TextColumn::make('expected_delivery_at')
                    ->label(__(static::$lang.'.columns.expected'))
                    ->dateTime()
                    ->sortable()
                    ->visibleFrom('md'),

                TextColumn::make('actual_delivery_at')
                    ->label(__(static::$lang.'.columns.delivered'))
                    ->dateTime()
                    ->sortable()
                    ->placeholder('—'),
            ])
            ->filters([
                SelectFilter::make('state')
                    ->label(__(static::$lang.'.filters.state'))
                    ->options(ShipmentState::class)
                    ->multiple(),

                SelectFilter::make('customer')
                    ->label(__(static::$lang.'.filters.customer'))
                    ->relationship('customer', 'name')
                    ->searchable()
                    ->preload(),

                Filter::make('created')
                    ->schema(DateRange::components(static::$lang))
                    ->query(fn (Builder $query, array $data): Builder => DateRange::apply($query, $data, 'created_at')),
            ])
            ->headerActions([
                ExportAction::make()
                    ->label(__(static::$lang.'.export'))
                    ->icon('heroicon-o-arrow-up-tray')
                    ->exporter(ShipmentRegisterExporter::class),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
