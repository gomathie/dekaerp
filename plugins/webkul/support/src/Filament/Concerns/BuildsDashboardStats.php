<?php

namespace Webkul\Support\Filament\Concerns;

use Filament\Widgets\StatsOverviewWidget\Stat;
use Webkul\Support\Filament\DashboardPeriod;

trait BuildsDashboardStats
{
    /**
     * @param  list<float>  $series
     */
    protected function themedStat(string $label, string $icon, string $color, string $value, float $current, float $previous, array $series = [], ?string $title = null, bool $lowerIsBetter = false, ?float $previousRecords = null, bool $money = false, ?string $baseline = null): Stat
    {
        [$description, $descriptionIcon, $trendColor] = $this->statTrend($current, $previous, $lowerIsBetter, $previousRecords, $money);

        // Say what the change is measured against (DB-1, 2026-11-22): "+12%"
        // alone did not say compared with when.
        if ($baseline !== null) {
            $description .= ' '.__('support::support.trend.vs', ['range' => $baseline]);
        }

        $attributes = [
            'class' => 'dashboard-stat dashboard-stat--'.$color,
        ];

        if ($title !== null) {
            $attributes['title'] = $title;
        }

        $stat = Stat::make($label, $value)
            ->description($description)
            ->descriptionIcon($descriptionIcon)
            ->descriptionColor($trendColor)
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
