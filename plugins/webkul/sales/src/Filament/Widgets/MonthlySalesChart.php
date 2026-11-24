<?php

namespace Webkul\Sale\Filament\Widgets;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Carbon;
use Webkul\Sale\Filament\Widgets\Concerns\GuardsSaleDashboardWidget;
use Webkul\Sale\Filament\Widgets\Concerns\HasSaleDashboardFilters;
use Webkul\Support\Filament\DashboardPeriod;
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
     * The grouping switch (DB-9, 2026-11-24): Automatic keeps the period-length
     * rule, which is by month for the year to date the dashboard opens on.
     */
    public ?string $filter = 'auto';

    /**
     * @return array<string, string>
     */
    protected function getFilters(): ?array
    {
        return DashboardPeriod::groupingOptions();
    }

    /**
     * Invoiced revenue over the period, the same figure as the Revenue tile
     * above it (DB-2, 2026-11-23), beside the same dates of the compared
     * period (DB-4 mockup). It used to add confirmed orders to invoices made
     * without one, so an order not yet invoiced was in the chart but not in
     * the tile, dated by the order rather than the invoice.
     */
    protected function getData(): array
    {
        [$start, $end] = $this->periodRange();
        [$previousStart, $previousEnd] = $this->previousPeriodRange();

        // One total per day, grouped by the switch (DB-9). It used to group
        // by month in SQL, so a week or a day could not be shown at all.
        ['labels' => $labels, 'values' => $data] = DashboardPeriod::rollUp($this->dailyRevenueSeries($start, $end), $start, $end, $this->filter);

        // The compared period is grouped the same way and laid over the same
        // positions; a shorter one (29 February) is padded with nothing sold.
        $previousData = array_pad(
            array_slice(DashboardPeriod::rollUp($this->dailyRevenueSeries($previousStart, $previousEnd), $previousStart, $previousEnd, $this->filter)['values'], 0, count($labels)),
            count($labels),
            0.0,
        );

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
     * Invoiced revenue per day from $start to $end, zero-filled, in company
     * currency: one grouped query over the same rows as the Revenue figure.
     *
     * @return list<float>
     */
    protected function dailyRevenueSeries(Carbon $start, Carbon $end): array
    {
        $query = $this->invoicedSales($start, $end);
        $day = 'CAST('.$query->qualifyColumn('invoice_date').' AS date)';

        $totals = [];

        foreach ($query->toBase()
            ->selectRaw("{$day} as day, SUM(".$query->qualifyColumn('amount_total_signed').') as revenue')
            ->groupByRaw($day)
            ->pluck('revenue', 'day') as $date => $revenue) {
            $totals[Carbon::parse($date)->toDateString()] = (float) $revenue;
        }

        $series = [];

        for ($date = $start->copy()->startOfDay(); $date->lte($end); $date->addDay()) {
            $series[] = $totals[$date->toDateString()] ?? 0.0;
        }

        return $series;
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
