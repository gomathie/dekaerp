<?php

namespace Webkul\Invoice\Filament\Exports;

use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\ExpenseResource;
use Webkul\Invoice\Models\Expense;

class ExpenseExporter extends Exporter
{
    protected const LANG = ExpenseResource::LANG.'.export';

    protected static ?string $model = Expense::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('number')
                ->label(__(self::LANG.'.columns.number')),
            ExportColumn::make('date')
                ->label(__(self::LANG.'.columns.date')),
            ExportColumn::make('expenseAccount.name')
                ->label(__(self::LANG.'.columns.expense-account')),
            ExportColumn::make('journal.name')
                ->label(__(self::LANG.'.columns.paid-from')),
            ExportColumn::make('partner.name')
                ->label(__(self::LANG.'.columns.vendor')),
            ExportColumn::make('reference')
                ->label(__(self::LANG.'.columns.reference')),
            ExportColumn::make('currency.name')
                ->label(__(self::LANG.'.columns.currency')),
            ExportColumn::make('amount_total')
                ->label(__(self::LANG.'.columns.amount')),
            ExportColumn::make('amount_tax')
                ->label(__(self::LANG.'.columns.tax')),
            ExportColumn::make('amount_total_company')
                ->label(__(self::LANG.'.columns.amount-company')),
            ExportColumn::make('note')
                ->label(__(self::LANG.'.columns.note'))
                ->enabledByDefault(false),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = __(self::LANG.'.notification.completed', [
            'count' => number_format($export->successful_rows),
        ]);

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.__(self::LANG.'.notification.failed', [
                'count' => number_format($failedRowsCount),
            ]);
        }

        return $body;
    }
}
