<?php

namespace Webkul\Logistics\Filament\Clusters\Configurations\Resources\ServiceTypeResource\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Webkul\Logistics\Filament\Clusters\Configurations\Resources\ServiceTypeResource;

class ServiceTypesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ServiceTypeResource::nameColumn(),
                ServiceTypeResource::codeColumn(),
                TextColumn::make('transport_mode')
                    ->label(__(ServiceTypeResource::$lang.'.table.columns.transport-mode'))
                    ->badge()
                    ->sortable(),
                TextColumn::make('product_reference')
                    ->label(__(ServiceTypeResource::$lang.'.table.columns.product-reference'))
                    ->placeholder('—')
                    ->visibleFrom('lg'),
                ServiceTypeResource::companyColumn(),
                ServiceTypeResource::activeColumn(),
            ])
            ->filters([
                ServiceTypeResource::activeFilter(),
                ServiceTypeResource::companyFilter(),
                TrashedFilter::make(),
            ])
            ->reorderable('sort')
            ->defaultSort('sort')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
