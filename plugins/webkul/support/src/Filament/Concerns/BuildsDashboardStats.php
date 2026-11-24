<?php

namespace Webkul\Support\Filament\Concerns;

use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\HtmlString;
use Webkul\Support\Filament\DashboardPeriod;

trait BuildsDashboardStats
{
    /**
     * Figures from another stats widget, for a headline row (DB-4,
     * 2026-11-23: each tab's four key figures in one row, and the separate
     * count tiles removed). The figures stay computed by the widget that
     * owns them, so nothing is calculated twice, and they show only to
     * someone that widget's own canView() allows: moving a figure must not
     * hand it to a role that could not see it before.
     *
     * @param  class-string  $widgetClass  a StatsOverviewWidget using the same page filters
     * @param  list<int>  $indexes  positions in that widget's getStats()
     * @return list<Stat>
     */
    protected function borrowedStats(string $widgetClass, array $indexes): array
    {
        if (! $widgetClass::canView()) {
            return [];
        }

        $widget = app($widgetClass);
        $widget->pageFilters = $this->pageFilters ?? [];

        $stats = (fn (): array => $this->getStats())->call($widget);

        return array_values(array_filter(array_map(
            fn (int $index): ?Stat => isset($stats[$index])
                ? $stats[$index]->extraAttributes(array_merge($stats[$index]->getExtraAttributes(), ['class' => 'overview-headline-stat']))
                : null,
            $indexes,
        )));
    }

    /**
     * A figure card's caption with its trend as a pill (DB-9, decided
     * 2026-11-24 from TRACE365's reference screens): "41 sales  [▲ 21%] vs …".
     * The arrow is part of the pill's text, so the direction never rests on
     * colour alone (DB-5). Every part is escaped; the colour is one of the
     * three statTrend() returns, mapped to a fixed class.
     */
    protected function trendDescription(?string $caption, string $change, string $icon, string $color, ?string $after = null): HtmlString
    {
        $arrow = match (true) {
            str_contains($icon, 'trending-up')   => '▲ ',
            str_contains($icon, 'trending-down') => '▼ ',
            default                              => '',
        };

        $tone = in_array($color, ['success', 'danger'], true) ? $color : 'gray';

        $parts = array_filter([
            filled($caption) ? '<span class="dashboard-trend-caption">'.e($caption).'</span>' : null,
            '<span class="dashboard-trend dashboard-trend--'.$tone.'">'.e($arrow.$change).'</span>',
            filled($after) ? '<span class="dashboard-trend-caption">'.e($after).'</span>' : null,
        ]);

        return new HtmlString(implode(' ', $parts));
    }

    /**
     * @param  list<float>  $series
     */
    protected function themedStat(string $label, string $icon, string $color, string $value, float $current, float $previous, array $series = [], ?string $title = null, bool $lowerIsBetter = false, ?float $previousRecords = null, bool $money = false, ?string $baseline = null): Stat
    {
        [$change, $trendIcon, $trendColor] = $this->statTrend($current, $previous, $lowerIsBetter, $previousRecords, $money);

        // Say what the change is measured against (DB-1, 2026-11-22): "+12%"
        // alone did not say compared with when.
        $description = $this->trendDescription(
            null,
            $change,
            $trendIcon,
            $trendColor,
            $baseline !== null ? __('support::support.trend.vs', ['range' => $baseline]) : null,
        );

        $attributes = [
            'class' => 'dashboard-stat dashboard-stat--'.$color,
        ];

        if ($title !== null) {
            $attributes['title'] = $title;
        }

        $stat = Stat::make($label, $value)
            ->description($description)
            ->chartColor($color)
            ->icon($icon)
            ->extraAttributes($attributes);

        return $series === [] ? $stat : $stat->chart($series);
    }

    /**
     * The period a trend compares with, for example "1 Oct – 31 Oct" (DB-1).
     * One format for every dashboard; each widget supplies its own
     * previousPeriodRange() (HasDashboardMetrics or a module's filters).
     */
    protected function baselineLabel(): ?string
    {
        if (! method_exists($this, 'previousPeriodRange')) {
            return null;
        }

        [$start, $end] = $this->previousPeriodRange();

        return DashboardPeriod::label($start, $end);
    }

    /**
     * The change against the previous period, honestly (dashboard plan DB-1,
     * decisions D-DB-1 and D-DB-2, 2026-11-22).
     *
     * - An empty previous period is "New", neutral. It used to read "+100%"
     *   in green, which on a first sale looked like a doubling.
     * - A small base (fewer than 5 records before) shows the real change,
     *   "+3", neutral: a percentage of 2 is noise. $previousRecords is the
     *   record count; for a count metric it defaults to the previous value,
     *   for a money metric pass it, or the percentage rule applies.
     * - $lowerIsBetter (overdue, costs, open bills, late shipments): a rise
     *   is red and a fall green.
     *
     * @return array{0: string, 1: string, 2: string}
     */
    protected function statTrend(float $current, float $previous, bool $lowerIsBetter = false, ?float $previousRecords = null, bool $money = false): array
    {
        if ($previous == 0.0 && $current == 0.0) {
            return ['0%', 'heroicon-m-minus', 'gray'];
        }

        if ($previous == 0.0) {
            return [__('support::support.trend.new'), 'heroicon-m-sparkles', 'gray'];
        }

        $direction = $current <=> $previous;
        $icon = match ($direction) {
            1       => 'heroicon-m-arrow-trending-up',
            -1      => 'heroicon-m-arrow-trending-down',
            default => 'heroicon-m-minus',
        };

        $records = $previousRecords ?? ($money ? null : $previous);

        if ($records !== null && $records < 5) {
            $delta = abs($current - $previous);
            $formatted = $money
                ? compact_money($delta, method_exists($this, 'dashboardCurrency') ? $this->dashboardCurrency() : null)
                : number_format($delta);

            return [match ($direction) {
                1       => '+'.$formatted,
                -1      => '−'.$formatted,
                default => '0',
            }, $icon, 'gray'];
        }

        $percentage = round((($current - $previous) / abs($previous)) * 100, 1);
        $label = abs($percentage).'%';

        if ($percentage == 0.0) {
            return [$label, 'heroicon-m-minus', 'gray'];
        }

        $good = ($percentage > 0) !== $lowerIsBetter;

        return [$label, $icon, $good ? 'success' : 'danger'];
    }
}
