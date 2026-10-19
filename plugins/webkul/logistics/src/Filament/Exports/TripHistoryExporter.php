<?php

namespace Webkul\Logistics\Filament\Exports;

use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Webkul\Logistics\Models\Trip;

class TripHistoryExporter extends Exporter
{
    protected static ?string $model = Trip::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name')->label(__('logistics::reports.vehicle-trips.columns.reference')),
            ExportColumn::make('vehicle.name')->label(__('logistics::reports.vehicle-trips.columns.vehicle')),
            ExportColumn::make('driver.name')->label(__('logistics::reports.vehicle-trips.columns.driver')),
            ExportColumn::make('state')
                ->label(__('logistics::reports.vehicle-trips.columns.state'))
                ->formatStateUsing(fn ($state) => is_object($state) ? ($state->getLabel() ?? $state->value ?? '') : (string) $state),
            ExportColumn::make('actual_start_at')->label(__('logistics::reports.vehicle-trips.columns.started')),
            ExportColumn::make('actual_end_at')->label(__('logistics::reports.vehicle-trips.columns.ended')),
            ExportColumn::make('shipments_count')->label(__('logistics::reports.vehicle-trips.columns.shipments')),
            // Blank where either reading is missing, rather than a number that
            // looks like a distance and is not.
            ExportColumn::make('distance')
                ->label(__('logistics::reports.vehicle-trips.columns.distance'))
                ->state(fn (Trip $record): ?float => $record->odometer_start === null || $record->odometer_end === null
                    ? null
                    : round((float) $record->odometer_end - (float) $record->odometer_start, 1)),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return __('logistics::reports.export-complete', [
            'count' => number_format($export->successful_rows),
        ]);
    }
}
