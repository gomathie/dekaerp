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
            ExportColumn::make('partner.name')->label(__($lang.'.customer')),
            ExportColumn::make('user.name')->label(__($lang.'.salesperson')),
            // Company currency, as on the page.
            ExportColumn::make('amount_untaxed')->label(__($lang.'.untaxed'))
                ->state(fn (Order $record): float => SalesAnalysis::inCompanyCurrency($record, (float) $record->amount_untaxed)),
            ExportColumn::make('amount_total')->label(__($lang.'.total'))
                ->state(fn (Order $record): float => SalesAnalysis::inCompanyCurrency($record, (float) $record->amount_total)),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return __('sales::filament/clusters/reporting.export-complete', ['count' => number_format($export->successful_rows)]);
    }
}
