<?php

namespace Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\DiscountCodeResource\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DiscountCodesTable
{
    protected static string $lang = 'platform-billing::filament/billing.discounts';

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label(__(static::$lang.'.columns.code'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('discount_type')
                    ->label(__(static::$lang.'.columns.type')),
                TextColumn::make('eligibility_type')
                    ->label(__(static::$lang.'.columns.eligibility')),
                IconColumn::make('is_active')
                    ->label(__(static::$lang.'.columns.active'))
                    ->boolean(),
                TextColumn::make('redemption_count')
                    ->label(__(static::$lang.'.columns.redeemed')),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->defaultSort('code')
            ->paginated([10, 25, 50])
            ->defaultPaginationPageOption(10)
            ->emptyStateHeading(__(static::$lang.'.empty'));
    }
}
