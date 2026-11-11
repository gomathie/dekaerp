<?php

namespace Webkul\Invoice\Filament\Widgets;

use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Webkul\Invoice\Filament\Overview\OverviewRegistry;

/**
 * "At a glance": the first thing on the Overview (user, 2026-11-11: when
 * someone logs in, the figures from every part at a glance, without
 * scrolling; on a phone, only what is needed).
 *
 * One widget holding each visible section's headline figures, two per
 * section, so the whole row fits the first screen on desktop (three across)
 * and on a phone (two across). Each section computes its own figures in its
 * own plugin (a headline widget registered through OverviewRegistry), so the
 * numbers match its detailed widgets and Invoices never names Sales or
 * Purchases.
 */
class OverviewHeadlineWidget extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected static bool $isLazy = false;

    protected static bool $isDiscovered = false;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return OverviewRegistry::visible() !== [];
    }

    public function getHeading(): ?string
    {
        return __('invoices::filament/pages/overview.headline.heading');
    }

    protected function getColumns(): int|array
    {
        return ['default' => 2, 'lg' => 3];
    }

    protected function getStats(): array
    {
        $filters = $this->pageFilters ?? [];
        $chosen = (array) ($filters['sections'] ?? []);

        $stats = [];

        foreach (OverviewRegistry::visible() as $key => $section) {
            $class = $section['headline'] ?? null;

            if ($class === null || ($chosen !== [] && ! in_array($key, $chosen, true))) {
                continue;
            }

            array_push($stats, ...app($class)->headlineStats($filters));
        }

        return $stats;
    }
}
