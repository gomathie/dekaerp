<?php

namespace Webkul\Logistics\Filament\Clusters\Reporting\Pages;

use Filament\Actions\ExportAction;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Webkul\Logistics\Enums\TripState;
use Webkul\Logistics\Filament\Exports\TripHistoryExporter;
use Webkul\Logistics\Models\Trip;

/**
 * Trips, by vehicle or by driver (WP-11).
 *
 * The two reports the plan asks for are the same table with a different filter
 * in front of it, so they are one class with a subclass each. Two copies would
 * be two places to fix the distance calculation.
 */
abstract class TripHistory extends ReportPage
{
    /**
     * The relationship the subclass filters on: `vehicle` or `driver`.
     */
    abstract protected static function subject(): string;

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Trip::query()->with(['vehicle', 'driver'])->withCount('shipments'))
            ->columns([
                TextColumn::make('name')
                    ->label(__(static::$lang.'.columns.reference'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('vehicle.name')
                    ->label(__(static::$lang.'.columns.vehicle'))
                    ->searchable()
                    ->placeholder('—'),

                TextColumn::make('driver.name')
                    ->label(__(static::$lang.'.columns.driver'))
                    ->searchable()
                    ->placeholder('—'),

                TextColumn::make('state')
                    ->label(__(static::$lang.'.columns.state'))
                    ->badge()
                    ->visibleFrom('sm'),

                TextColumn::make('actual_start_at')
                    ->label(__(static::$lang.'.columns.started'))
                    ->dateTime()
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('shipments_count')
                    ->label(__(static::$lang.'.columns.shipments'))
                    ->numeric()
                    ->summarize(Sum::make()->label(__(static::$lang.'.columns.shipments'))),

                // Only where both readings exist. A trip with one odometer
                // reading tells you nothing about distance, and showing the
                // single number as if it were a distance would be worse than
                // showing nothing.
                TextColumn::make('distance')
                    ->label(__(static::$lang.'.columns.distance'))
                    ->state(function (Model $record): ?float {
                        if ($record->odometer_start === null || $record->odometer_end === null) {
                            return null;
                        }

                        return round((float) $record->odometer_end - (float) $record->odometer_start, 1);
                    })
                    ->numeric(decimalPlaces: 1)
                    ->placeholder('—')
                    ->visibleFrom('md'),
            ])
            ->filters([
                SelectFilter::make(static::subject())
                    ->label(__(static::$lang.'.filters.'.static::subject()))
                    ->relationship(static::subject(), 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('state')
                    ->label(__(static::$lang.'.filters.state'))
                    ->options(TripState::class)
                    ->multiple(),

                Filter::make('period')
                    ->schema(DateRange::components(static::$lang))
                    ->query(fn (Builder $query, array $data): Builder => DateRange::apply($query, $data, 'actual_start_at'))
                    ->indicateUsing(fn (array $data): ?string => DateRange::indicator($data, static::$lang)),
            ])
            ->headerActions([
                ExportAction::make()
                    ->label(__(static::$lang.'.export'))
                    ->icon('heroicon-o-arrow-up-tray')
                    ->exporter(TripHistoryExporter::class),
            ])
            ->defaultSort('actual_start_at', 'desc');
    }
}
