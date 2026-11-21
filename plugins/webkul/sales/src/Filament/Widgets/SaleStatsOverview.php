<?php

namespace Webkul\Sale\Filament\Widgets;

use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Webkul\Sale\Filament\Widgets\Concerns\GuardsSaleDashboardWidget;
use Webkul\Sale\Filament\Widgets\Concerns\HasSaleDashboardFilters;
use Webkul\Support\Filament\Widgets\DashboardStatsWidget;

class SaleStatsOverview extends DashboardStatsWidget
{
    use GuardsSaleDashboardWidget, HasSaleDashboardFilters;

    protected static ?int $sort = 1;

    public function getHeading(): ?string
    {
        return __('sales::filament/widgets/sales-dashboard.stats.heading');
    }

    protected function getStats(): array
    {
        [$start, $end] = $this->periodRange();

        [$previousStart, $previousEnd] = $this->previousPeriodRange();

        $currency = current_company()?->currency?->name;

        $quotations = $this->quotations()->count();
        $previousQuotations = $this->quotations($previousStart, $previousEnd)->count();

        // Sales are confirmed orders plus invoices made without one (user,
        // 2026-11-20: direct invoices must reflect in sales). Revenue is
        // invoiced sales (DB-8), and the average is that revenue per sale.
        $orders = $this->salesCount();
        $previousOrders = $this->salesCount($previousStart, $previousEnd);

        $revenue = $this->invoicedRevenue();
        $previousRevenue = $this->invoicedRevenue($previousStart, $previousEnd);

        $average = $orders > 0 ? $revenue / $orders : 0;
        $previousAverage = $previousOrders > 0 ? $previousRevenue / $previousOrders : 0;

        $dailySales = $this->dailySalesCounts($start, $end);
        $orderCountSeries = [];

        for ($date = $start->copy()->startOfDay(); $date->lte($end); $date->addDay()) {
            $orderCountSeries[] = (float) ($dailySales[$date->toDateString()] ?? 0);
        }

        $revenueSeries = $this->invoicedRevenueSeries($start, $end);

        $averageSeries = array_map(
            fn (float $revenue, float $count): float => $count > 0 ? round($revenue / $count, 2) : 0.0,
            $revenueSeries,
            $orderCountSeries,
        );

        return [
            $this->themedStat(
                __('sales::filament/widgets/sales-dashboard.stats.total-quotations'),
                'heroicon-o-document-text',
                'info',
                number_format($quotations),
                $quotations,
                $previousQuotations,
                $this->trendData($this->quotations(), 'COUNT', '*', $start, $end),
            ),
            $this->themedStat(
                __('sales::filament/widgets/sales-dashboard.stats.total-sales-orders'),
                'heroicon-o-shopping-cart',
                'primary',
                number_format($orders),
                $orders,
                $previousOrders,
                $orderCountSeries,
            ),
            $this->themedStat(
                __('sales::filament/widgets/sales-dashboard.stats.total-revenue'),
                'heroicon-o-banknotes',
                'success',
                compact_money($revenue, $currency),
                $revenue,
                $previousRevenue,
                $revenueSeries,
                money($revenue, $currency),
            ),
            $this->themedStat(
                __('sales::filament/widgets/sales-dashboard.stats.average-revenue'),
                'heroicon-o-chart-bar',
                'warning',
                compact_money($average, $currency),
                $average,
                $previousAverage,
                $averageSeries,
                money($average, $currency),
            ),
        ];
    }

    /**
     * Daily invoiced revenue, grouped by the invoice date itself.
     *
     * Not through Trend: it groups by the alias "date", and invoices have a
     * real `date` column, which PostgreSQL prefers over the alias - the query
     * failed on every load and blanked the Sales dashboard (Sentry DEKAERP-1X).
     *
     * @return array<int, float>
     */
    protected function invoicedRevenueSeries(Carbon $start, Carbon $end): array
    {
        $query = $this->invoicedSales($start, $end);
        $day = 'CAST('.$query->getQuery()->getGrammar()->wrap($query->qualifyColumn('invoice_date')).' AS date)';

        $totals = $query->toBase()
            ->selectRaw("{$day} as day, SUM(".$query->qualifyColumn('amount_total_signed').') as total')
            ->groupByRaw($day)
            ->pluck('total', 'day')
            ->mapWithKeys(fn ($total, $day): array => [Carbon::parse($day)->toDateString() => (float) $total]);

        $series = [];

        for ($date = $start->copy()->startOfDay(); $date->lte($end); $date->addDay()) {
            $series[] = round($totals[$date->toDateString()] ?? 0.0, 2);
        }

        return $series;
    }

    /**
     * @return array<int, float>
     */
    protected function trendData(Builder $query, string $aggregate, string $column, Carbon $start, Carbon $end, string $dateColumn = 'date_order'): array
    {
        return Trend::query($query)
            ->dateColumn($dateColumn)
            ->between(start: $start, end: $end)
            ->perDay()
            ->aggregate($column, $aggregate)
            ->map(fn (TrendValue $value) => round((float) $value->aggregate, 2))
            ->toArray();
    }
}
