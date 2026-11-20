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

        $orders = $this->saleOrders()->count();
        $previousOrders = $this->saleOrders($previousStart, $previousEnd)->count();

        $ccExpr = 'SUM(CASE WHEN currency_rate > 0 THEN amount_total / currency_rate ELSE amount_total END) as cc_total';

        // Revenue is invoiced sales, with or without an order (DB-8). The
        // average stays per sales order, so it keeps reading order values.
        $revenue = $this->invoicedRevenue();
        $previousRevenue = $this->invoicedRevenue($previousStart, $previousEnd);

        $orderRevenue = (float) ($this->saleOrders()->selectRaw($ccExpr)->value('cc_total') ?? 0.0);
        $previousOrderRevenue = (float) ($this->saleOrders($previousStart, $previousEnd)->selectRaw($ccExpr)->value('cc_total') ?? 0.0);

        $average = $orders > 0 ? $orderRevenue / $orders : 0;
        $previousAverage = $previousOrders > 0 ? $previousOrderRevenue / $previousOrders : 0;

        $revenueColumn = 'CASE WHEN currency_rate > 0 THEN amount_total / currency_rate ELSE amount_total END';

        $orderCountSeries = $this->trendData($this->saleOrders(), 'COUNT', '*', $start, $end);

        $orderRevenueSeries = $this->trendData($this->saleOrders(), 'SUM', $revenueColumn, $start, $end);

        $revenueSeries = $this->trendData($this->invoicedSales(), 'SUM', 'amount_total_signed', $start, $end, 'invoice_date');

        $averageSeries = array_map(
            fn (float $revenue, float $count): float => $count > 0 ? round($revenue / $count, 2) : 0.0,
            $orderRevenueSeries,
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
