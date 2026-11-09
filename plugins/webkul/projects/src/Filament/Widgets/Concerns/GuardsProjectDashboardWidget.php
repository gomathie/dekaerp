<?php

namespace Webkul\Project\Filament\Widgets\Concerns;

use Webkul\Support\Filament\Concerns\GuardsDashboardWidget;

/**
 * Project dashboard widgets require the Projects module and their own Shield
 * permission; the rule itself lives in GuardsDashboardWidget. Before
 * 2026-11-09 they used Shield's trait alone, which ignores the module switch.
 */
trait GuardsProjectDashboardWidget
{
    use GuardsDashboardWidget;

    protected static function dashboardPluginName(): string
    {
        return 'projects';
    }
}
