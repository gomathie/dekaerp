<?php

namespace Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\PlanResource\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PlansTable
{
    protected static string $lang = 'platform-billing::filament/billing.plans';

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__(static::$lang.'.columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('code')
                    ->label(__(static::$lang.'.columns.code'))
                    ->searchable()
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label(__(static::$lang.'.columns.active'))
                    ->boolean(),
                TextColumn::make('seat_limit')
                    ->label(__(static::$lang.'.columns.seats'))
                    ->placeholder(__(static::$lang.'.infolist.unlimited')),
                TextColumn::make('prices_count')
                    ->counts('prices')
                    ->label(__(static::$lang.'.columns.prices')),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->defaultSort('name')
            ->paginated([10, 25, 50])
            ->defaultPaginationPageOption(10)
            ->emptyStateHeading(__(static::$lang.'.empty'));
    }
}
