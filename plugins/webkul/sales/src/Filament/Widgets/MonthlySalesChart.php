<?php

namespace Webkul\Sale\Filament\Widgets;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Carbon;
use Webkul\Sale\Filament\Widgets\Concerns\GuardsSaleDashboardWidget;
use Webkul\Sale\Filament\Widgets\Concerns\HasSaleDashboardFilters;
use Webkul\Support\Filament\Widgets\DashboardChartWidget;

class MonthlySalesChart extends DashboardChartWidget
{
    use GuardsSaleDashboardWidget, HasSaleDashboardFilters;

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function getHeading(): string|Htmlable|null
    {
        return __('sales::filament/widgets/sales-dashboard.monthly-sales.heading');
    }

    /**
     * Invoiced revenue per month, the same figure as the Revenue tile above
     * it (DB-2, 2026-11-23), beside the same months of the compared period
     * (DB-4 mockup). It used to add confirmed orders to invoices made
     * without one, so an order not yet invoiced was in the chart but not in
     * the tile, dated by the order rather than the invoice.
     */
    protected function getData(): array
    {
        [$start, $end] = $this->periodRange();
        [$previousStart, $previousEnd] = $this->previousPeriodRange();

        $current = $this->monthlyRevenue($start, $end);
        $previous = $this->monthlyRevenue($previousStart, $previousEnd);

        $format = $start->year === $end->year ? 'M' : 'M Y';

        $labels = [];
        $data = [];
        $previousData = [];

        $cursor = $start->copy()->startOfMonth();
        $previousCursor = $previousStart->copy()->startOfMonth();

        while ($cursor <= $end) {
            $labels[] = $cursor->translatedFormat($format);
            $data[] = round($current[$cursor->format('Y-m')] ?? 0.0, 2);
            $previousData[] = round($previous[$previousCursor->format('Y-m')] ?? 0.0, 2);

            $cursor->addMonth();
            $previousCursor->addMonth();
        }

        return [
            'datasets' => [
                $this->gradientLineDataset($data, __('sales::filament/widgets/sales-dashboard.monthly-sales.revenue')),
                [
                    'label'       => __('sales::filament/widgets/sales-dashboard.monthly-sales.previous'),
                    'data'        => $previousData,
                    'borderColor' => $this->brandColor('gray', 400),
                    'borderDash'  => [4, 4],
                    'fill'        => false,
                    'tension'     => 0.4,
                    'borderWidth' => 1.5,
                    'pointRadius' => 0,
                ],
            ],
            'labels' => $labels,
        ];
    }

    /**
     * @return array<string, float> keyed Y-m
     */
    protected function monthlyRevenue(Carbon $start, Carbon $end): array
    {
        $query = $this->invoicedSales($start, $end);
        $bucket = db_dialect()->monthBucket($query->qualifyColumn('invoice_date'));

        return $query->toBase()
            ->selectRaw("{$bucket} as period, SUM(".$query->qualifyColumn('amount_total_signed').') as revenue')
            ->groupByRaw($bucket)
            ->pluck('revenue', 'period')
            ->map(fn ($revenue): float => (float) $revenue)
            ->all();
    }

    protected function getOptions(): array
    {
        $options = $this->lineChartOptions(12);

        $options['plugins']['lineGradient'] = $this->revenueLineGradient();

        // Two lines now: say which is which.
        $options['plugins']['legend'] = ['display' => true, 'position' => 'bottom', 'labels' => ['usePointStyle' => true, 'boxWidth' => 8]];

        return $options;
    }

    protected function getType(): string
    {
        return 'line';
    }
}
