<?php

namespace Webkul\Logistics\Filament\Clusters\Finance\Resources\ExpenseResource\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Webkul\Logistics\Enums\ExpenseState;

class ExpensesTable
{
    protected static string $lang = 'logistics::filament/clusters/finance/resources/expense';

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('date')->label(__(static::$lang.'.table.columns.date'))->date()->sortable(),
                TextColumn::make('category.name')->label(__(static::$lang.'.table.columns.category'))->searchable()->sortable(),
                TextColumn::make('description')->label(__(static::$lang.'.table.columns.description'))->searchable()->limit(40),
                TextColumn::make('amount')->label(__(static::$lang.'.table.columns.amount'))->numeric(decimalPlaces: 2)->sortable(),
                TextColumn::make('state')->label(__(static::$lang.'.table.columns.state'))->badge()->sortable(),
                TextColumn::make('shipment.name')->label(__(static::$lang.'.table.columns.shipment'))->placeholder('-')->visibleFrom('md'),
                TextColumn::make('company.name')->label(__(static::$lang.'.table.columns.company'))->visibleFrom('lg'),
            ])
            ->filters([
                SelectFilter::make('state')->label(__(static::$lang.'.table.filters.state'))->options(ExpenseState::options()),
                SelectFilter::make('category')->label(__(static::$lang.'.table.filters.category'))->relationship('category', 'name')->searchable()->preload(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->defaultSort('date', 'desc');
    }
}
