<?php

namespace Webkul\Purchase\Filament\Admin\Clusters;

use Filament\Clusters\Cluster;
use Webkul\Purchase\Support\PurchasesAccess;
use Webkul\Support\Enums\NavigationGroup;

class Configurations extends Cluster
{
    public static function canAccess(): bool
    {
        return PurchasesAccess::enabledForCurrent();
    }

    protected static ?string $slug = 'purchase/configurations';

    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string
    {
        return __('purchases::filament/admin/clusters/configurations.navigation.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Purchase;
    }
}
