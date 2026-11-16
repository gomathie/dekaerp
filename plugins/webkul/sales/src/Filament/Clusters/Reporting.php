<?php

namespace Webkul\Sale\Filament\Clusters;

use Filament\Clusters\Cluster;
use Webkul\PluginManager\Support\PluginEntitlement;
use Webkul\Support\Enums\NavigationGroup;

/**
 * Sales reports (reports plan R2, 2026-11-13). Named `Reporting` like every
 * module's report section, so the Reports page finds its pages by itself.
 */
class Reporting extends Cluster
{
    protected static ?string $slug = 'sales/reporting';

    protected static ?int $navigationSort = 90;

    /** Hidden for companies without the Sales module. */
    public static function canAccess(): bool
    {
        return PluginEntitlement::enabledForCurrent('sales');
    }

    public static function getNavigationLabel(): string
    {
        return __('sales::filament/clusters/reporting.navigation');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Sale;
    }
}
