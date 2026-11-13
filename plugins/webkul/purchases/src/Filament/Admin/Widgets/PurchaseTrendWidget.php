<?php

namespace Webkul\Purchase\Filament\Admin\Widgets;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Carbon;
use Webkul\Purchase\Filament\Admin\Widgets\Concerns\GuardsPurchaseDashboardWidget;
use Webkul\Purchase\Filament\Admin\Widgets\Concerns\HasPurchaseDashboardFilters;
use Webkul\Support\Filament\Widgets\DashboardChartWidget;

class PurchaseTrendWidget extends DashboardChartWidget
{
    use GuardsPurchaseDashboardWidget, HasPurchaseDashboardFilters;

    protected const SERIES_COLORS = [
        'draft'    => 'gray',
        'sent'     => 'info',
        'purchase' => 'success',
        'done'     => 'warning',
        'canceled' => 'danger',
    ];

    protected static ?int $sort = 2;

    protected ?string $maxHeight = '240px';

    public function getHeading(): string|Htmlable|null
    {
        return __('purchases::filament/admin/widgets/purchase-dashboard.trend.heading');
    }

    protected function getData(): array
    {
        [$start, $end] = $this->periodRange();

        // Upstream loaded every order of the period and counted them per day and
        // state in PHP. One grouped query returns those counts instead
        // (docs/completed/dashboards-plan.md §2.4); toBase() keeps the company scope.
        $query = $this->purchaseOrders();

        $grammar = $query->getQuery()->getGrammar();

        $day = 'DATE('.$grammar->wrap($query->qualifyColumn('ordered_at')).')';

        $state = $grammar->wrap($query->qualifyColumn('state'));

        $counts = [];

        foreach ($query->toBase()
            ->selectRaw($day.' as bucket_day, '.$state.' as bucket_state, COUNT(*) as bucket_count')
            ->groupByRaw($day.', '.$state)
            ->get() as $row) {
            $counts[Carbon::parse($row->bucket_day)->format('Y-m-d')][(string) $row->bucket_state] = (int) $row->bucket_count;
        }

        $labels = [];
        $series = array_fill_keys(array_keys(self::SERIES_COLORS), []);

        for ($date = (clone $start)->startOfDay(); $date->lte($end); $date->addDay()) {
            $labels[] = $date->format('M d');

            foreach (array_keys(self::SERIES_COLORS) as $seriesState) {
                $series[$seriesState][] = $counts[$date->format('Y-m-d')][$seriesState] ?? 0;
            }
        }

        return [
            'datasets' => array_map(
                fn (string $state): array => [
                    'label'            => __('purchases::filament/admin/widgets/purchase-dashboard.trend.states.'.$state),
                    'data'             => $series[$state],
                    'borderColor'      => $this->brandColor(self::SERIES_COLORS[$state]),
                    'backgroundColor'  => $this->brandColor(self::SERIES_COLORS[$state], 500, 0.12),
                    'borderWidth'      => 2,
                    'pointRadius'      => 0,
                    'pointHoverRadius' => 4,
                    'tension'          => 0.3,
                ],
                array_keys(self::SERIES_COLORS)
            ),
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        $options = $this->lineChartOptions(12);

        $options['plugins']['legend'] = [
            'display'  => true,
            'position' => 'bottom',
            'labels'   => [
                'color'         => $this->brandColor('gray', 500),
                'boxWidth'      => 10,
                'boxHeight'     => 10,
                'usePointStyle' => true,
                'padding'       => 14,
            ],
        ];

        return $options;
    }

    protected function getType(): string
    {
        return 'line';
    }
}
