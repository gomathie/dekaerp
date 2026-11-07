<?php

namespace Webkul\Recruitment\Filament\Widgets\Concerns;

use Webkul\Support\Filament\Concerns\GuardsDashboardWidget;

/**
 * Recruitment dashboard widgets require the Recruitments module and their own
 * Shield permission; the rule itself lives in GuardsDashboardWidget. Before
 * 2026-11-07 ApplicantChartWidget had no check at all.
 */
trait GuardsRecruitmentDashboardWidget
{
    use GuardsDashboardWidget;

    protected static function dashboardPluginName(): string
    {
        return 'recruitments';
    }
}
