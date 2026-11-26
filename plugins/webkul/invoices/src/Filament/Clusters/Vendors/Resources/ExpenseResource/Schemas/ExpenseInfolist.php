<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources\ExpenseResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\ExpenseResource;
use Webkul\Invoice\Models\Expense;
use Webkul\Support\Support\CompanyCurrencyEquivalent;
use Webkul\Support\Support\CompanyStorage;

/**
 * User text (reference, note, vendor) is printed by TextEntry, which
 * escapes it; nothing here is rendered as HTML.
 */
class ExpenseInfolist
{
    protected const LANG = ExpenseResource::LANG.'.form';

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Grid::make(['default' => 1, 'sm' => 2])
                            ->schema([
                                TextEntry::make('number')
                                    ->label(__(self::LANG.'.fields.number')),
                                TextEntry::make('date')
                                    ->label(__(self::LANG.'.fields.date'))
                                    ->date(),
                                TextEntry::make('amount_total')
                                    ->label(__(self::LANG.'.fields.amount'))
                                    ->money(fn (Expense $record): ?string => $record->currency?->name)
                                    ->helperText(fn (Expense $record): ?string => CompanyCurrencyEquivalent::short((float) $record->amount_total_company, $record->currency_id, $record->company)),
                                TextEntry::make('amount_tax')
                                    ->label(__(self::LANG.'.fields.tax-amount'))
                                    ->money(fn (Expense $record): ?string => $record->currency?->name)
                                    ->visible(fn (Expense $record): bool => (float) $record->amount_tax !== 0.0),
                                TextEntry::make('expenseAccount.name')
                                    ->label(__(self::LANG.'.fields.expense-account')),
                                TextEntry::make('journal.name')
                                    ->label(__(self::LANG.'.fields.paid-from'))
                                    ->badge()
                                    ->color('info'),
                                TextEntry::make('partner.name')
                                    ->label(__(self::LANG.'.fields.vendor'))
                                    ->placeholder('—'),
                                TextEntry::make('tax.name')
                                    ->label(__(self::LANG.'.fields.tax'))
                                    ->placeholder('—'),
                                TextEntry::make('reference')
                                    ->label(__(self::LANG.'.fields.reference'))
                                    ->placeholder('—'),
                                TextEntry::make('move.name')
                                    ->label(__(self::LANG.'.fields.entry'))
                                    ->placeholder('—'),
                                TextEntry::make('note')
                                    ->label(__(self::LANG.'.fields.note'))
                                    ->placeholder('—')
                                    ->columnSpanFull(),
                                TextEntry::make('receipt_name')
                                    ->label(__(self::LANG.'.fields.receipt'))
                                    ->state(fn (Expense $record): ?string => $record->receipt_path ? ($record->receipt_name ?: basename($record->receipt_path)) : null)
                                    ->placeholder('—')
                                    ->icon(fn (Expense $record): ?string => $record->receipt_path ? 'heroicon-o-paper-clip' : null)
                                    ->url(fn (Expense $record): ?string => $record->receipt_path ? CompanyStorage::url((int) $record->company_id, $record->receipt_path) : null)
                                    ->openUrlInNewTab()
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }
}
