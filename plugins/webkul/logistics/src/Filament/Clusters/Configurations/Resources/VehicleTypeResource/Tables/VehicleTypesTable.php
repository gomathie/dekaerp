<?php

namespace Webkul\Logistics\Filament\Clusters\Configurations\Resources\VehicleTypeResource\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Webkul\Logistics\Filament\Clusters\Configurations\Resources\VehicleTypeResource;

class VehicleTypesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                VehicleTypeResource::nameColumn(),
                VehicleTypeResource::codeColumn(),
                TextColumn::make('capacity_kg')
                    ->label(__(VehicleTypeResource::$lang.'.table.columns.capacity-kg'))
                    ->numeric()
                    ->placeholder('—')
                    ->visibleFrom('sm')
                    ->sortable(),
                TextColumn::make('capacity_m3')
                    ->label(__(VehicleTypeResource::$lang.'.table.columns.capacity-m3'))
                    ->numeric()
                    ->placeholder('—')
                    ->visibleFrom('md')
                    ->sortable(),
                VehicleTypeResource::companyColumn(),
                VehicleTypeResource::activeColumn(),
            ])
            ->filters([
                VehicleTypeResource::activeFilter(),
                VehicleTypeResource::companyFilter(),
            ])
            ->reorderable('sort')
            ->defaultSort('sort')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
