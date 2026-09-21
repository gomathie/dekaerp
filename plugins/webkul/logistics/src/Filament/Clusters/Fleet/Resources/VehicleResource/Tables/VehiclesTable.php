<?php

namespace Webkul\Logistics\Filament\Clusters\Fleet\Resources\VehicleResource\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Webkul\Logistics\Enums\VehicleOwnership;

class VehiclesTable
{
    protected static string $lang = 'logistics::filament/clusters/fleet/resources/vehicle';

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('registration_no')
                    ->label(__(static::$lang.'.table.columns.registration-no'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__(static::$lang.'.table.columns.name'))
                    ->placeholder('—')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('ownership')
                    ->label(__(static::$lang.'.table.columns.ownership'))
                    ->badge()
                    ->sortable(),
                TextColumn::make('vehicleType.name')
                    ->label(__(static::$lang.'.table.columns.vehicle-type'))
                    ->placeholder('—')
                    ->visibleFrom('md')
                    ->sortable(),
                TextColumn::make('carrier.name')
                    ->label(__(static::$lang.'.table.columns.carrier'))
                    ->placeholder('—')
                    ->visibleFrom('lg')
                    ->sortable(),
                TextColumn::make('capacity_kg')
                    ->label(__(static::$lang.'.table.columns.capacity-kg'))
                    ->numeric()
                    ->suffix(' kg')
                    ->placeholder('—')
                    ->visibleFrom('lg')
                    ->sortable(),
                TextColumn::make('telematics_device_ref')
                    ->label(__(static::$lang.'.table.columns.telematics-device-ref'))
                    ->placeholder('—')
                    ->visibleFrom('lg')
                    ->searchable(),
                TextColumn::make('company.name')
                    ->label(__(static::$lang.'.table.columns.company'))
                    ->visibleFrom('lg'),
                IconColumn::make('is_active')
                    ->label(__(static::$lang.'.table.columns.is-active'))
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('ownership')
                    ->label(__(static::$lang.'.table.filters.ownership'))
                    ->options(VehicleOwnership::class),
                SelectFilter::make('vehicle_type')
                    ->label(__(static::$lang.'.table.filters.vehicle-type'))
                    ->relationship('vehicleType', 'name'),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('id', 'desc');
    }
}
