<?php

namespace Webkul\Purchase\Filament\Admin\Clusters;

use Filament\Clusters\Cluster;
use Webkul\Purchase\Support\PurchasesAccess;
use Webkul\Support\Enums\NavigationGroup;

/**
 * Purchase reports (reports plan R2, 2026-11-13). Named `Reporting` like every
 * module's report section, so the Reports page finds its pages by itself.
 */
class Reporting extends Cluster
{
    protected static ?string $slug = 'purchases/reporting';

    protected static ?int $navigationSort = 90;

    /** Hidden for companies without the Purchases module. */
    public static function canAccess(): bool
    {
        return PurchasesAccess::enabledForCurrent();
    }

    public static function getNavigationLabel(): string
    {
        return __('purchases::filament/admin/clusters/reporting.navigation');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Purchase;
    }
}
