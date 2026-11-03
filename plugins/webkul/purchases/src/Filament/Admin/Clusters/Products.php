<?php

namespace Webkul\Purchase\Filament\Admin\Clusters;

use Filament\Clusters\Cluster;
use Webkul\Purchase\Support\PurchasesAccess;
use Webkul\Support\Enums\NavigationGroup;

class Products extends Cluster
{
    public static function canAccess(): bool
    {
        return PurchasesAccess::enabledForCurrent();
    }

    protected static ?string $slug = 'purchase/products';

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return __('purchases::filament/admin/clusters/products.navigation.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Purchase;
    }
}
