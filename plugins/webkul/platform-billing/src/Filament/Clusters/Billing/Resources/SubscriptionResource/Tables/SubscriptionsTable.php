<?php

namespace Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\SubscriptionResource\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\PlanResource\Schemas\PlanForm;
use Webkul\PlatformBilling\Models\Subscription;
use Webkul\PlatformBilling\Support\Money;

class SubscriptionsTable
{
    protected static string $lang = 'platform-billing::filament/billing.subscriptions';

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('company.name')
                    ->label(__(static::$lang.'.columns.company'))
                    ->searchable(),
                TextColumn::make('plan_name')
                    ->label(__(static::$lang.'.columns.plan'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__(static::$lang.'.columns.status'))
                    ->formatStateUsing(fn (mixed $state): string => __(static::$lang.'.statuses.'.$state))
                    ->badge(),
                TextColumn::make('amount_minor')
                    ->label(__(static::$lang.'.columns.amount'))
                    ->formatStateUsing(fn (mixed $state, Subscription $record): string => Money::of((int) $record->amount_minor, (string) $record->currency)->toDecimal().' '.$record->currency)
                    ->sortable(),
                TextColumn::make('interval_months')
                    ->label(__(static::$lang.'.columns.interval'))
                    ->formatStateUsing(fn (mixed $state): string => PlanForm::intervals()[(int) $state] ?? (string) $state),
                TextColumn::make('period_end_at')
                    ->label(__(static::$lang.'.columns.period-end'))
                    ->dateTime()
                    ->placeholder('—'),
                IconColumn::make('cancel_at_period_end')
                    ->label(__(static::$lang.'.columns.cancel'))
                    ->boolean(),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->defaultSort('id', 'desc')
            ->paginated([10, 25, 50])
            ->defaultPaginationPageOption(10)
            ->emptyStateHeading(__(static::$lang.'.empty'));
    }
}
