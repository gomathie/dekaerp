<?php

namespace Webkul\Sale\Filament\Exports;

use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Webkul\Sale\Models\OrderLine;

/**
 * Sales by product export (reports plan R2): the filtered rows of the page.
 */
class SalesByProductExporter extends Exporter
{
    protected static ?string $model = OrderLine::class;

    public static function getColumns(): array
    {
        $lang = 'sales::filament/clusters/reporting.by-product.columns';

        return [
            // The page's rows (SalesByProduct::rowsQuery()): order lines and
            // lines of invoices made without an order, amounts already in
            // company currency.
            ExportColumn::make('product_name')->label(__($lang.'.product')),
            ExportColumn::make('category_name')->label(__($lang.'.category')),
            ExportColumn::make('document')->label(__($lang.'.order')),
            ExportColumn::make('date')->label(__($lang.'.date')),
            ExportColumn::make('customer')->label(__($lang.'.customer')),
            ExportColumn::make('quantity')->label(__($lang.'.quantity')),
            ExportColumn::make('untaxed')->label(__($lang.'.untaxed'))
                ->state(fn (OrderLine $record): float => round((float) $record->untaxed, 2)),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return __('sales::filament/clusters/reporting.export-complete', ['count' => number_format($export->successful_rows)]);
    }
}
