<?php

namespace Webkul\Sale\Filament\Exports;

use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Webkul\Sale\Filament\Clusters\Reporting\Pages\SalesAnalysis;
use Webkul\Sale\Models\Order;

/**
 * Sales analysis export (reports plan R2): the filtered rows of the page.
 */
class SalesAnalysisExporter extends Exporter
{
    protected static ?string $model = Order::class;

    public static function getColumns(): array
    {
        $lang = 'sales::filament/clusters/reporting.analysis.columns';

        return [
            ExportColumn::make('name')->label(__($lang.'.reference')),
            ExportColumn::make('date_order')->label(__($lang.'.date')),
            ExportColumn::make('customer')->label(__($lang.'.customer')),
            ExportColumn::make('salesperson')->label(__($lang.'.salesperson')),
            // The page's rows (SalesAnalysis::rowsQuery()): orders and
            // invoices made without one, already in company currency.
            ExportColumn::make('amount_untaxed')->label(__($lang.'.untaxed'))
                ->state(fn (Order $record): float => round((float) $record->amount_untaxed, 2)),
            ExportColumn::make('amount_total')->label(__($lang.'.total'))
                ->state(fn (Order $record): float => round((float) $record->amount_total, 2)),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return __('sales::filament/clusters/reporting.export-complete', ['count' => number_format($export->successful_rows)]);
    }
}
