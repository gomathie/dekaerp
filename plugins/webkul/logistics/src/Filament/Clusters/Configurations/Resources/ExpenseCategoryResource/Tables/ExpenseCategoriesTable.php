<?php

namespace Webkul\Logistics\Filament\Clusters\Configurations\Resources\ExpenseCategoryResource\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Webkul\Logistics\Filament\Clusters\Configurations\Resources\ExpenseCategoryResource;

class ExpenseCategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ExpenseCategoryResource::nameColumn(),
                ExpenseCategoryResource::codeColumn(),
                IconColumn::make('requires_receipt')
                    ->label(__(ExpenseCategoryResource::$lang.'.table.columns.requires-receipt'))
                    ->boolean()
                    ->visibleFrom('sm'),
                IconColumn::make('is_subcontracting')
                    ->label(__(ExpenseCategoryResource::$lang.'.table.columns.is-subcontracting'))
                    ->boolean()
                    ->visibleFrom('md'),
                ExpenseCategoryResource::companyColumn(),
                ExpenseCategoryResource::activeColumn(),
            ])
            ->filters([
                ExpenseCategoryResource::activeFilter(),
                ExpenseCategoryResource::companyFilter(),
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
