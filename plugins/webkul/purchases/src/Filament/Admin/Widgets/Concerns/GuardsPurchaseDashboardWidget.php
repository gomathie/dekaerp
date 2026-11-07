<?php

namespace Webkul\Purchase\Filament\Admin\Widgets\Concerns;

use Webkul\Support\Filament\Concerns\GuardsDashboardWidget;

/**
 * Purchase dashboard widgets require the Purchases module and their own Shield
 * permission; the rule itself lives in GuardsDashboardWidget. Upstream left
 * TopRfqsWidget and TopVendorsWidget without any check at all.
 */
trait GuardsPurchaseDashboardWidget
{
    use GuardsDashboardWidget;

    protected static function dashboardPluginName(): string
    {
        return 'purchases';
    }
}
