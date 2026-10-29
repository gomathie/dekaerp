<?php

namespace Webkul\Product\Observers;

use Exception;
use Webkul\PluginManager\Package;
use Webkul\Product\Models\Product;
use Webkul\Support\Models\UOM;

class UOMObserver
{
    public function deleting(UOM $uom): void
    {
        if (! Package::isPluginInstalled('products')) {
            return;
        }

        // UOMs are shared setup data; deleting one must detect a product from
        // any company instead of treating a hidden product as unused.
        $product = Product::withoutGlobalScopes()->withTrashed()
            ->where(fn ($query) => $query->where('uom_id', $uom->id)->orWhere('uom_po_id', $uom->id))
            ->first();

        if (! $product) {
            return;
        }

        throw new Exception(__('products::observers/uom.in-use', [
            'uom'     => $uom->name,
            'product' => $product->name,
        ]));
    }
}
