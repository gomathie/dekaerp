<?php

namespace Webkul\Invoice\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Invoice\Filament\Widgets\Concerns\GuardsInvoiceDashboardWidget;
use Webkul\Invoice\Filament\Widgets\Concerns\HasInvoiceDashboardFilters;
use Webkul\Support\Filament\Concerns\HasChartTheme;

class RevenueOverTimeWidget extends ChartWidget
{
    // No 5-second refresh (Filament default): every open dashboard re-ran all
    // its queries 12 times a minute (Sentry DEKAERP-11). Filters reload it.
    protected ?string $pollingInterval = null;

    use HasChartTheme, HasInvoiceDashboardFilters, GuardsInvoiceDashboardWidget;

    protected static ?int $sort = 3;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 1;

    protected ?string $maxHeight = '300px';

    public function getHeading(): string|Htmlable|null
    {
        return __('invoices::filament/widgets/invoice-dashboard.revenue-over-time.heading');
    }

    public function getDescription(): string|Htmlable|null
    {
        return __('invoices::filament/widgets/invoice-dashboard.revenue-over-time.description');
    }

    protected function getData(): array
    {
        [$start, $end] = $this->periodRange();

        $query = $this->customerInvoices();

        // Upstream loaded every paid invoice of the period to add them up per
        // day in PHP. dailyBuckets() asks the database for one total per day
        // (docs/dashboards-plan.md §2.4), with the company scope intact.
        $data = $this->dailyBuckets($query->where($query->qualifyColumn('payment_state'), 'paid'), true);

        $labels = [];

        for ($date = (clone $start)->startOfDay(); $date->lte($end); $date->addDay()) {
            $labels[] = $date->format('M d');
        }

        return [
            'datasets' => [
                $this->gradientLineDataset($data),
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'interaction'         => $this->chartInteraction('index'),
            'plugins'             => [
                'entrance' => ['type' => 'progressive-line', 'duration' => 700, 'stagger' => 22],

                'lineGradient' => $this->revenueLineGradient(),

                'legend'  => ['display' => false],
                'tooltip' => $this->chartTooltip(),
            ],
            'scales'              => $this->cartesianScales(12),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
