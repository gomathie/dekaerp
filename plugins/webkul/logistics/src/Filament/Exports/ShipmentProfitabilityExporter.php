<?php

namespace Webkul\Logistics\Filament\Exports;

use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Webkul\Logistics\Models\Shipment;

/**
 * Profitability, as a file.
 *
 * revenue_total and cost_total are the aggregate aliases the page's query adds,
 * so the file carries the same figures the screen does rather than recomputing
 * them a second way and disagreeing.
 */
class ShipmentProfitabilityExporter extends Exporter
{
    protected static ?string $model = Shipment::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name')->label(__('logistics::reports.profitability.columns.reference')),
            ExportColumn::make('customer.name')->label(__('logistics::reports.profitability.columns.customer')),
            ExportColumn::make('revenue_total')->label(__('logistics::reports.profitability.columns.revenue')),
            ExportColumn::make('cost_total')->label(__('logistics::reports.profitability.columns.costs')),
            ExportColumn::make('margin')
                ->label(__('logistics::reports.profitability.columns.margin'))
                ->state(fn (Shipment $record): float => (float) $record->revenue_total - (float) $record->cost_total),
            // Named, because the figures above are each in the shipment's own
            // currency and a column of bare numbers from several currencies
            // would invite someone to total it.
            ExportColumn::make('currency.name')->label(__('logistics::reports.profitability.columns.currency')),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return __('logistics::reports.export-complete', [
            'count' => number_format($export->successful_rows),
        ]);
    }
}
