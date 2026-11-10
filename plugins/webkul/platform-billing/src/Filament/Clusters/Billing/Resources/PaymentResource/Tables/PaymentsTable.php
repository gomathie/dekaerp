<?php

namespace Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\PaymentResource\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Webkul\PlatformBilling\Models\Payment;
use Webkul\PlatformBilling\Support\Money;

class PaymentsTable
{
    protected static string $lang = 'platform-billing::filament/billing.payments';

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('idempotency_key')
                    ->label(__(static::$lang.'.columns.reference'))
                    ->searchable(),
                TextColumn::make('company.name')
                    ->label(__(static::$lang.'.columns.company'))
                    ->searchable(),
                TextColumn::make('amount_minor')
                    ->label(__(static::$lang.'.columns.amount'))
                    ->state(fn (Payment $record): string => Money::of((int) $record->amount_minor, (string) $record->currency)->toDecimal()),
                TextColumn::make('currency')
                    ->label(__(static::$lang.'.columns.currency')),
                TextColumn::make('status')
                    ->label(__(static::$lang.'.columns.status')),
                TextColumn::make('received_at')
                    ->label(__(static::$lang.'.columns.received'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->defaultSort('received_at', 'desc')
            ->paginated([10, 25, 50])
            ->defaultPaginationPageOption(10)
            ->emptyStateHeading(__(static::$lang.'.empty'));
    }
}
