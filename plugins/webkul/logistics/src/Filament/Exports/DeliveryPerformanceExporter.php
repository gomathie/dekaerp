<?php

namespace Webkul\Logistics\Filament\Exports;

use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Webkul\Logistics\Filament\Clusters\Reporting\Pages\DeliveryPerformance;
use Webkul\Logistics\Models\Shipment;

class DeliveryPerformanceExporter extends Exporter
{
    protected static ?string $model = Shipment::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name')->label(__('logistics::reports.delivery-performance.columns.reference')),
            ExportColumn::make('customer.name')->label(__('logistics::reports.delivery-performance.columns.customer')),
            ExportColumn::make('expected_delivery_at')->label(__('logistics::reports.delivery-performance.columns.expected')),
            ExportColumn::make('actual_delivery_at')->label(__('logistics::reports.delivery-performance.columns.delivered')),
            // The same verdict the screen shows, from the same method, so the
            // file cannot drift from the page.
            ExportColumn::make('outcome')
                ->label(__('logistics::reports.delivery-performance.columns.outcome'))
                ->state(fn (Shipment $record): string => __(
                    'logistics::reports.delivery-performance.outcomes.'.DeliveryPerformance::outcome($record),
                )),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return __('logistics::reports.export-complete', [
            'count' => number_format($export->successful_rows),
        ]);
    }
}
