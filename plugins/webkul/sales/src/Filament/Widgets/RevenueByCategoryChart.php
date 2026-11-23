<?php

namespace Webkul\Sale\Filament\Widgets;

use Webkul\Sale\Filament\Widgets\Concerns\GuardsSaleDashboardWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\DB;
use Webkul\Sale\Filament\Widgets\Concerns\HasSaleDashboardFilters;
use Webkul\Support\Filament\Widgets\DashboardChartWidget;

class RevenueByCategoryChart extends DashboardChartWidget
{
    // DB-4 layout on the Overview's six-column grid (2026-11-23).
    protected int|string|array $columnSpan = ['default' => 'full', 'md' => 1, 'xl' => 2];

    use HasSaleDashboardFilters, GuardsSaleDashboardWidget;

    protected const TOP_SLICES = 3;

    protected static ?int $sort = 3;

    /**
     * @var array{labels: list<string>, data: list<float>}|null
     */
    protected ?array $resolvedData = null;

    public function getHeading(): string|Htmlable|null
    {
        return __('sales::filament/widgets/sales-dashboard.revenue-by-category.heading');
    }

    /**
     * @return array{labels: list<string>, data: list<float>}
     */
    protected function resolveChartData(): array
    {
        if ($this->resolvedData !== null) {
            return $this->resolvedData;
        }

        $revenue = 'SUM(sales_lines.revenue)';

        // Orders and direct invoices together; see productSaleRows().
        $rows = DB::query()
            ->fromSub($this->productSaleRows(), 'sales_lines')
            ->leftJoin('products_products', 'products_products.id', '=', 'sales_lines.product_id')
            ->leftJoin('products_categories', 'products_categories.id', '=', 'products_products.category_id')
            ->groupBy('products_categories.id', 'products_categories.name')
            ->addSelect('products_categories.name as name')
            ->selectRaw("{$revenue} as revenue")
            ->havingRaw("{$revenue} > 0")
            ->orderByDesc('revenue')
            ->get();

        $labels = [];
        $data = [];

        foreach ($rows->take(self::TOP_SLICES) as $row) {
            $labels[] = $row->name ?: __('sales::filament/widgets/sales-dashboard.unknown');
            $data[] = round((float) $row->revenue, 2);
        }

        $other = round((float) $rows->skip(self::TOP_SLICES)->sum('revenue'), 2);

        if ($other > 0) {
            $labels[] = __('sales::filament/widgets/sales-dashboard.other');
            $data[] = $other;
        }

        return $this->resolvedData = ['labels' => $labels, 'data' => $data];
    }

    protected function getData(): array
    {
        ['labels' => $labels, 'data' => $data] = $this->resolveChartData();

        return [
            'datasets' => [
                [
                    'label'           => __('sales::filament/widgets/sales-dashboard.revenue-by-category.revenue'),
                    'data'            => $data,
                    'backgroundColor' => array_slice($this->chartRamp(), 0, max(count($data), 1)),
                    'borderWidth'     => 0,
                    'hoverOffset'     => 0,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        $total = array_sum($this->resolveChartData()['data']);

        return $this->doughnutChartOptions(
            compact_money($total, current_company()?->currency?->name),
            __('sales::filament/widgets/sales-dashboard.revenue-by-category.revenue'),
        );
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
