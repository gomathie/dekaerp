<?php

namespace Webkul\Sale\Filament\Widgets\Concerns;

use Webkul\Support\Filament\Concerns\GuardsDashboardWidget;

/**
 * Sales dashboard widgets require the Sales module and their own Shield
 * permission; the rule itself lives in GuardsDashboardWidget.
 */
trait GuardsSaleDashboardWidget
{
    use GuardsDashboardWidget;

    protected static function dashboardPluginName(): string
    {
        return 'sales';
    }
}
