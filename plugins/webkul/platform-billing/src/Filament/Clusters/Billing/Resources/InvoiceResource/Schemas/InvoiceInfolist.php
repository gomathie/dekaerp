<?php

namespace Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\InvoiceResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Webkul\PlatformBilling\Models\Invoice;
use Webkul\PlatformBilling\Support\Money;

class InvoiceInfolist
{
    protected static string $lang = 'platform-billing::filament/billing.invoices';

    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('number')->label(__(static::$lang.'.columns.number')),
            TextEntry::make('company.name')->label(__(static::$lang.'.columns.company')),
            TextEntry::make('status')->label(__(static::$lang.'.columns.status')),
            TextEntry::make('currency')->label(__(static::$lang.'.columns.currency')),
            TextEntry::make('issuer_name')->label(__(static::$lang.'.snapshot.issuer')),
            TextEntry::make('tax_rate')->label(__(static::$lang.'.snapshot.tax-rate')),
            TextEntry::make('tax_enabled')->label(__(static::$lang.'.snapshot.tax-enabled'))->badge(),
            TextEntry::make('subtotal_minor')
                ->label(__(static::$lang.'.snapshot.subtotal'))
                ->state(fn (Invoice $record): string => static::money($record, (int) $record->subtotal_minor)),
            TextEntry::make('discount_minor')
                ->label(__(static::$lang.'.snapshot.discount'))
                ->state(fn (Invoice $record): string => static::money($record, (int) $record->discount_minor)),
            TextEntry::make('discount_code')->label(__(static::$lang.'.snapshot.code'))->placeholder('—'),
            TextEntry::make('tax_minor')
                ->label(__(static::$lang.'.snapshot.tax'))
                ->state(fn (Invoice $record): string => static::money($record, (int) $record->tax_minor)),
            TextEntry::make('total_minor')
                ->label(__(static::$lang.'.columns.total'))
                ->state(fn (Invoice $record): string => static::money($record, (int) $record->total_minor)),
            TextEntry::make('issued_at')->label(__(static::$lang.'.columns.issued'))->dateTime(),
            TextEntry::make('due_at')->label(__(static::$lang.'.columns.due'))->dateTime(),
            TextEntry::make('grace_starts_at')->label(__(static::$lang.'.snapshot.grace'))->dateTime(),
            TextEntry::make('lines_summary')
                ->label(__(static::$lang.'.snapshot.lines'))
                ->state(function (Invoice $record): string {
                    return $record->lines()
                        ->orderBy('position')
                        ->get()
                        ->map(fn ($line): string => $line->description.' '.static::money($record, (int) $line->amount_minor))
                        ->implode("\n");
                })
                ->placeholder(__(static::$lang.'.snapshot.no-lines')),
        ]);
    }

    protected static function money(Invoice $invoice, int $minor): string
    {
        return Money::of($minor, (string) $invoice->currency)->toDecimal().' '.$invoice->currency;
    }
}
