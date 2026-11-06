<?php

namespace Webkul\Support\Filament\Concerns;

use Closure;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * Ported from upstream (aureus/master c31a0db21) for the module dashboards;
 * see docs/dashboards-plan.md. dailyBuckets() deliberately differs.
 *
 * Query factories passed to buildStat() must return an unselected builder:
 * dailyBuckets() adds its own select.
 */
trait HasDashboardMetrics
{
    use BuildsDashboardStats;
    use InteractsWithPageFilters;

    protected function dashboardDateColumn(): string
    {
        return 'created_at';
    }

    protected function dashboardValueColumn(): string
    {
        return 'amount_total';
    }

    protected function buildStat(string $label, string $icon, string $color, Closure $queryFactory, bool $money = false, bool $abs = false): Stat
    {
        [$start, $end] = $this->periodRange();

        [$previousStart, $previousEnd] = $this->previousPeriodRange();

        $current = $this->aggregate($queryFactory($start, $end), $money);

        $previous = $this->aggregate($queryFactory($previousStart, $previousEnd), $money);

        $series = $this->dailyBuckets($queryFactory($start, $end), $money);

        if ($abs) {
            $current = abs($current);

            $previous = abs($previous);

            $series = array_map('abs', $series);
        }

        $value = $money ? compact_money($current, $this->dashboardCurrency()) : number_format($current);

        $title = $money ? money($current, $this->dashboardCurrency()) : null;

        return $this->themedStat($label, $icon, $color, $value, $current, $previous, $series, $title);
    }

    protected function aggregate(Builder $query, bool $money): float
    {
        return $money
            ? (float) $query->sum($query->qualifyColumn($this->dashboardValueColumn()))
            : (float) $query->count();
    }

    /**
     * One value per day of the period, zero-filled.
     *
     * Upstream loaded every row of the period and grouped them in PHP, which
     * pulls a whole period of invoices into memory on every dashboard refresh.
     * This asks the database for one grouped row per day instead. toBase()
     * keeps the model's global scopes, so the company boundary still applies.
     *
     * @return list<float>
     */
    protected function dailyBuckets(Builder $query, bool $money): array
    {
        [$start, $end] = $this->periodRange();

        $dateColumn = $query->qualifyColumn($this->dashboardDateColumn());

        $valueColumn = $query->qualifyColumn($this->dashboardValueColumn());

        $grammar = $query->getQuery()->getGrammar();

        $day = 'DATE('.$grammar->wrap($dateColumn).')';

        $aggregate = $money ? 'SUM('.$grammar->wrap($valueColumn).')' : 'COUNT(*)';

        $byDay = $query->toBase()
            ->whereBetween($dateColumn, [$start, $end])
            ->selectRaw($day.' as bucket_day, '.$aggregate.' as bucket_value')
            ->groupByRaw($day)
            ->pluck('bucket_value', 'bucket_day')
            ->mapWithKeys(fn ($value, $key): array => [Carbon::parse($key)->format('Y-m-d') => (float) $value]);

        $series = [];

        for ($date = (clone $start)->startOfDay(); $date->lte($end); $date->addDay()) {
            $series[] = round($byDay->get($date->format('Y-m-d'), 0.0), 2);
        }

        return $series;
    }

    /**
     * @return array<string, mixed>
     */
    protected function dashboardFilters(): array
    {
        return $this->pageFilters ?? [];
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    protected function periodRange(): array
    {
        $filters = $this->dashboardFilters();

        $start = ! empty($filters['start_date'])
            ? Carbon::parse($filters['start_date'])->startOfDay()
            : now()->subMonth()->startOfDay();

        $end = ! empty($filters['end_date'])
            ? Carbon::parse($filters['end_date'])->endOfDay()
            : now()->endOfDay();

        return [$start, $end];
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    protected function previousPeriodRange(): array
    {
        [$start, $end] = $this->periodRange();

        // The period runs start-of-day to end-of-day, so diffInDays() is just
        // under a whole number (30.99… for 31 days): floor it, then add the
        // first day back. Rounding would add a day to every period.
        $length = (int) floor($start->diffInDays($end)) + 1;

        $previousEnd = (clone $start)->subDay()->endOfDay();

        return [(clone $previousEnd)->subDays($length - 1)->startOfDay(), $previousEnd];
    }

    protected function dashboardCurrency(): ?string
    {
        return current_company()?->currency?->name;
    }
}
