<?php

namespace Webkul\Account\Filament\Exports;

use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Account\Models\Payment;

class PaymentExporter extends Exporter
{
    protected static ?string $model = Payment::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('date')
                ->label(__('accounts::filament/exports/payment.columns.date')),
            ExportColumn::make('name')
                ->label(__('accounts::filament/exports/payment.columns.name')),
            ExportColumn::make('journal.name')
                ->label(__('accounts::filament/exports/payment.columns.journal')),
            ExportColumn::make('paymentMethod.name')
                ->label(__('accounts::filament/exports/payment.columns.payment-method')),
            ExportColumn::make('partner.name')
                ->label(__('accounts::filament/exports/payment.columns.partner')),
            // The invoices a payment settled and its reference, as on the list
            // (TRACE365, 2026-11-23).
            ExportColumn::make('invoices')
                ->label(__('accounts::filament/resources/payment.table.columns.invoices'))
                ->state(fn (Payment $record): string => $record->invoices->pluck('name')->filter()->implode(', ')),
            ExportColumn::make('memo')
                ->label(__('accounts::filament/resources/payment.form.sections.fields.memo')),
            ExportColumn::make('amount_company_currency_signed')
                ->label(__('accounts::filament/exports/payment.columns.amount-currency')),
            ExportColumn::make('amount')
                ->label(__('accounts::filament/exports/payment.columns.amount')),
            ExportColumn::make('unused_amount')
                ->label(__('accounts::filament/resources/payment.table.columns.unused'))
                ->state(fn (Payment $record): ?float => $record->unusedAmount()),
            ExportColumn::make('state')
                ->label(__('accounts::filament/exports/payment.columns.state'))
                ->formatStateUsing(fn ($state) => is_object($state) ? ($state->getLabel() ?? $state->value ?? '') : (string) $state),
            ExportColumn::make('company.name')
                ->label(__('accounts::filament/exports/payment.columns.company'))
                ->enabledByDefault(false),
        ];
    }

    /** One query for every row's invoices, not one per payment. */
    public static function modifyQuery(Builder $query): Builder
    {
        return $query->with('invoices:id,name')->withUnusedAmount();
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = __('accounts::filament/exports/payment.notification.completed', [
            'count' => number_format($export->successful_rows),
        ]);

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.__('accounts::filament/exports/payment.notification.failed', [
                'count' => number_format($failedRowsCount),
            ]);
        }

        return $body;
    }
}
