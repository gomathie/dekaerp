<?php

namespace Webkul\Logistics\Filament\Clusters\Operations\Resources\TripResource\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Webkul\Logistics\Enums\TripState;

class TripsTable
{
    protected static string $lang = 'logistics::filament/clusters/operations/resources/trip';

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__(static::$lang.'.table.columns.number'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('state')
                    ->label(__(static::$lang.'.table.columns.state'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('vehicle.registration_no')
                    ->label(__(static::$lang.'.table.columns.vehicle'))
                    ->searchable()
                    ->placeholder('—'),

                TextColumn::make('driver.name')
                    ->label(__(static::$lang.'.table.columns.driver'))
                    ->searchable()
                    ->placeholder('—')
                    ->visibleFrom('sm'),

                TextColumn::make('shipments_count')
                    ->label(__(static::$lang.'.table.columns.shipments'))
                    ->counts('shipments')
                    ->visibleFrom('md'),

                TextColumn::make('planned_start_at')
                    ->label(__(static::$lang.'.table.columns.planned-start-at'))
                    ->dateTime()
                    ->sortable()
                    ->placeholder('—')
                    ->visibleFrom('lg'),

                TextColumn::make('company.name')
                    ->label(__(static::$lang.'.table.columns.company'))
                    ->visibleFrom('lg'),
            ])
            ->filters([
                SelectFilter::make('state')
                    ->label(__(static::$lang.'.table.filters.state'))
                    ->options(TripState::options()),

                SelectFilter::make('vehicle')
                    ->label(__(static::$lang.'.table.filters.vehicle'))
                    ->relationship('vehicle', 'registration_no')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('driver')
                    ->label(__(static::$lang.'.table.filters.driver'))
                    ->relationship('driver', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->defaultSort('planned_start_at', 'desc');
    }
}
