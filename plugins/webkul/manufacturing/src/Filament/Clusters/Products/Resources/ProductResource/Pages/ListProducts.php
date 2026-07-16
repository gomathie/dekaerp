<?php

namespace Webkul\Manufacturing\Filament\Clusters\Products\Resources\ProductResource\Pages;

use Illuminate\Database\Eloquent\Builder;
use Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource\Pages\ListProducts as BaseListProducts;
use Webkul\Manufacturing\Filament\Clusters\Products\Resources\ProductResource;
use Webkul\TableViews\Filament\Components\PresetView;

class ListProducts extends BaseListProducts
{
    protected static string $resource = ProductResource::class;

    public function getPresetTableViews(): array
    {
        return array_merge(parent::getPresetTableViews(), [
            'components' => PresetView::make(__('manufacturing::filament/clusters/products/resources/product/pages/list-products.tabs.components'))
                ->icon('heroicon-s-puzzle-piece')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('billOfMaterialLines')),
        ]);
    }
}
