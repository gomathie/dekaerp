<?php

namespace Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\InvoiceResource\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Webkul\PlatformBilling\Models\Invoice;
use Webkul\PlatformBilling\Support\Money;

class InvoicesTable
{
    protected static string $lang = 'platform-billing::filament/billing.invoices';

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number')
                    ->label(__(static::$lang.'.columns.number'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('company.name')
                    ->label(__(static::$lang.'.columns.company'))
                    ->searchable(),
                TextColumn::make('status')
                    ->label(__(static::$lang.'.columns.status')),
                TextColumn::make('total_minor')
                    ->label(__(static::$lang.'.columns.total'))
                    ->state(fn (Invoice $record): string => Money::of((int) $record->total_minor, (string) $record->currency)->toDecimal()),
                TextColumn::make('currency')
                    ->label(__(static::$lang.'.columns.currency')),
                TextColumn::make('issued_at')
                    ->label(__(static::$lang.'.columns.issued'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('due_at')
                    ->label(__(static::$lang.'.columns.due'))
                    ->dateTime(),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->defaultSort('issued_at', 'desc')
            ->paginated([10, 25, 50])
            ->defaultPaginationPageOption(10)
            ->emptyStateHeading(__(static::$lang.'.empty'));
    }
}
