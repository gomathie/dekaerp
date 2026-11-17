<?php

namespace Webkul\Logistics\Filament\Clusters\Configurations\Resources\PackageTypeResource\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Webkul\Logistics\Filament\Clusters\Configurations\Resources\PackageTypeResource;

class PackageTypesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                PackageTypeResource::nameColumn(),
                PackageTypeResource::codeColumn(),
                PackageTypeResource::companyColumn(),
                PackageTypeResource::activeColumn(),
            ])
            ->filters([
                PackageTypeResource::activeFilter(),
                PackageTypeResource::companyFilter(),
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
