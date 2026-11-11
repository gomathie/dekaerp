<?php

namespace Webkul\Support\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Webkul\Support\Filament\Concerns\BuildsDashboardStats;

abstract class DashboardStatsWidget extends StatsOverviewWidget
{
    // No 5-second refresh (Filament default): every open dashboard re-ran all
    // its queries 12 times a minute (Sentry DEKAERP-11). Filters reload it.
    protected ?string $pollingInterval = null;

    use BuildsDashboardStats;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';
}
