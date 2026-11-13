<?php

namespace Webkul\Purchase\Filament\Admin\Exports;

use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Webkul\Purchase\Filament\Admin\Clusters\Reporting\Pages\PurchasesByProduct;
use Webkul\Purchase\Models\OrderLine;

/**
 * Purchases by product export (reports plan R2): the filtered rows of the
 * page, in company currency.
 */
class PurchasesByProductExporter extends Exporter
{
    protected static ?string $model = OrderLine::class;

    public static function getColumns(): array
    {
        $lang = 'purchases::filament/admin/clusters/reporting.by-product.columns';

        return [
            ExportColumn::make('product.name')->label(__($lang.'.product')),
            ExportColumn::make('product.category.name')->label(__($lang.'.category')),
            ExportColumn::make('order.name')->label(__($lang.'.order')),
            ExportColumn::make('order.ordered_at')->label(__($lang.'.date')),
            ExportColumn::make('order.partner.name')->label(__($lang.'.supplier')),
            ExportColumn::make('product_qty')->label(__($lang.'.ordered')),
            ExportColumn::make('qty_received')->label(__($lang.'.received')),
            ExportColumn::make('price_subtotal')->label(__($lang.'.untaxed'))
                ->state(fn (OrderLine $record): float => PurchasesByProduct::inCompanyCurrency($record)),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return __('purchases::filament/admin/clusters/reporting.export-complete', ['count' => number_format($export->successful_rows)]);
    }
}
