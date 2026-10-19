<?php

namespace Webkul\Logistics\Filament\Exports;

use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Webkul\Logistics\Models\Shipment;

/**
 * The shipment register, as a file.
 *
 * Filament's ExportAction runs the table's own query, so the filters on screen
 * are the filters in the file - a report that exported more than it displayed
 * would be a quiet way to leak another company's rows past the company scope.
 */
class ShipmentRegisterExporter extends Exporter
{
    protected static ?string $model = Shipment::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name')->label(__('logistics::reports.shipment-register.columns.reference')),
            ExportColumn::make('customer.name')->label(__('logistics::reports.shipment-register.columns.customer')),
            ExportColumn::make('state')
                ->label(__('logistics::reports.shipment-register.columns.state'))
                ->formatStateUsing(fn ($state) => is_object($state) ? ($state->getLabel() ?? $state->value ?? '') : (string) $state),
            ExportColumn::make('origin_label')->label(__('logistics::reports.shipment-register.columns.origin')),
            ExportColumn::make('destination_label')->label(__('logistics::reports.shipment-register.columns.destination')),
            ExportColumn::make('expected_delivery_at')->label(__('logistics::reports.shipment-register.columns.expected')),
            ExportColumn::make('actual_delivery_at')->label(__('logistics::reports.shipment-register.columns.delivered')),
            ExportColumn::make('company.name')->label(__('logistics::reports.shipment-register.columns.company'))->enabledByDefault(false),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return __('logistics::reports.export-complete', [
            'count' => number_format($export->successful_rows),
        ]);
    }
}
