<?php

namespace Webkul\Security\Filament\Exports;

use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Webkul\Chatter\Models\Message;
use Webkul\Security\Filament\Clusters\Settings\Pages\ActivityLog;

/**
 * Company activity log export (CR-11, 2026-11-13), for auditors. Same labels
 * and summaries as the screen, through ActivityLog's own formatters, and the
 * rows the filtered table shows (ExportAction runs getTableQueryForExport()).
 */
class ActivityLogExporter extends Exporter
{
    protected static ?string $model = Message::class;

    public static function getColumns(): array
    {
        $lang = 'security::filament/clusters/activity-log.columns';

        return [
            ExportColumn::make('created_at')->label(__($lang.'.when')),
            ExportColumn::make('causer.name')->label(__($lang.'.who')),
            ExportColumn::make('messageable_type')
                ->label(__($lang.'.record'))
                ->state(fn (Message $record): string => ActivityLog::recordLabel($record)),
            ExportColumn::make('event')
                ->label(__($lang.'.action'))
                ->state(fn (Message $record): string => ActivityLog::eventLabel($record->event)),
            ExportColumn::make('properties')
                ->label(__($lang.'.changes'))
                ->state(fn (Message $record): string => ActivityLog::changesSummary($record)),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return __('security::filament/clusters/activity-log.export-complete', [
            'count' => number_format($export->successful_rows),
        ]);
    }
}
