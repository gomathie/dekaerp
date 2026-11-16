<?php

namespace Webkul\Attendance\Filament\Exports;

use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Webkul\Attendance\Models\Attendance;

/**
 * Attendance rows as a file.
 *
 * Filament runs the table's own filtered query, so the file matches what is on
 * screen - and with it the company scope and the manager's team restriction.
 *
 * Location and IP are personal data. They exist as columns but are off by
 * default: someone who needs them turns them on deliberately, and nobody
 * exports where a colleague was by accident.
 */
class AttendanceExporter extends Exporter
{
    protected static ?string $model = Attendance::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('employee.name')
                ->label(__('attendances::filament/resources/attendance.table.columns.employee')),
            ExportColumn::make('check_in_at')
                ->label(__('attendances::filament/resources/attendance.table.columns.check-in')),
            ExportColumn::make('check_out_at')
                ->label(__('attendances::filament/resources/attendance.table.columns.check-out')),
            ExportColumn::make('worked_minutes')
                ->label(__('attendances::filament/resources/attendance.table.columns.worked')),
            ExportColumn::make('source_in')
                ->label(__('attendances::filament/resources/attendance.table.columns.source')),
            ExportColumn::make('source_out')
                ->label(__('attendances::filament/resources/attendance.table.columns.source-out')),
            ExportColumn::make('needs_review')
                ->label(__('attendances::filament/resources/attendance.table.columns.review')),
            ExportColumn::make('note')
                ->label(__('attendances::filament/resources/attendance.table.columns.note')),
            ExportColumn::make('latitude_in')
                ->label(__('attendances::filament/resources/attendance.table.columns.latitude'))
                ->enabledByDefault(false),
            ExportColumn::make('longitude_in')
                ->label(__('attendances::filament/resources/attendance.table.columns.longitude'))
                ->enabledByDefault(false),
            ExportColumn::make('ip_in')
                ->label(__('attendances::filament/resources/attendance.table.columns.ip'))
                ->enabledByDefault(false),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return __('attendances::filament/resources/attendance.export-complete', [
            'count' => number_format($export->successful_rows),
        ]);
    }
}
