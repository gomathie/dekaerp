<?php

namespace Webkul\Sale\Filament\Exports;

use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Webkul\Sale\Filament\Clusters\Reporting\Pages\SalesByProduct;
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
            ExportColumn::make('product.name')->label(__($lang.'.product')),
            ExportColumn::make('product.category.name')->label(__($lang.'.category')),
            ExportColumn::make('order.name')->label(__($lang.'.order')),
            ExportColumn::make('order.date_order')->label(__($lang.'.date')),
            ExportColumn::make('order.partner.name')->label(__($lang.'.customer')),
            ExportColumn::make('product_uom_qty')->label(__($lang.'.quantity')),
            ExportColumn::make('price_subtotal')->label(__($lang.'.untaxed'))
                ->state(fn (OrderLine $record): float => SalesByProduct::inCompanyCurrency($record)),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return __('sales::filament/clusters/reporting.export-complete', ['count' => number_format($export->successful_rows)]);
    }
}
