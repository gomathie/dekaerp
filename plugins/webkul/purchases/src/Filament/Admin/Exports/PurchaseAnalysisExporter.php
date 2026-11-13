<?php

namespace Webkul\Purchase\Filament\Admin\Exports;

use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Webkul\Purchase\Models\PurchaseOrder;

/**
 * Purchase analysis export (reports plan R2): the filtered rows of the page,
 * in company currency.
 */
class PurchaseAnalysisExporter extends Exporter
{
    protected static ?string $model = PurchaseOrder::class;

    public static function getColumns(): array
    {
        $lang = 'purchases::filament/admin/clusters/reporting.analysis.columns';

        return [
            ExportColumn::make('name')->label(__($lang.'.reference')),
            ExportColumn::make('ordered_at')->label(__($lang.'.date')),
            ExportColumn::make('partner.name')->label(__($lang.'.supplier')),
            ExportColumn::make('user.name')->label(__($lang.'.buyer')),
            ExportColumn::make('untaxed_amount')->label(__($lang.'.untaxed'))
                ->state(fn (PurchaseOrder $record): float => (float) $record->currency_rate > 0
                    ? round((float) $record->untaxed_amount / (float) $record->currency_rate, 2)
                    : (float) $record->untaxed_amount),
            ExportColumn::make('total_cc_amount')->label(__($lang.'.total')),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return __('purchases::filament/admin/clusters/reporting.export-complete', ['count' => number_format($export->successful_rows)]);
    }
}
