<?php

namespace Webkul\Sale\Filament\Widgets;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\DB;
use Webkul\Sale\Filament\Widgets\Concerns\GuardsSaleDashboardWidget;
use Webkul\Sale\Filament\Widgets\Concerns\HasSaleDashboardFilters;
use Webkul\Support\Filament\Widgets\DashboardChartWidget;

class RevenueByCategoryChart extends DashboardChartWidget
{
    // DB-4 layout on the Overview's six-column grid (2026-11-23).
    protected int|string|array $columnSpan = ['default' => 'full', 'md' => 1, 'xl' => 2];

    use GuardsSaleDashboardWidget, HasSaleDashboardFilters;

    protected const TOP_SLICES = 3;

    protected static ?int $sort = 3;

    // Room for the legend rows below the ring (DB-9).
    protected ?string $maxHeight = '300px';

    /**
     * @var array{labels: list<string>, data: list<float>}|null
     */
    protected ?array $resolvedData = null;

    public function getHeading(): string|Htmlable|null
    {
        return __('sales::filament/widgets/sales-dashboard.revenue-by-category.heading');
    }

    /**
     * Says what this counts (DB-2, 2026-11-23): sales value, not the
     * invoiced Revenue figure, so the two differ by orders not invoiced yet.
     */
    public function getDescription(): string|Htmlable|null
    {
        return __('sales::filament/widgets/sales-dashboard.sales-value');
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
                    // Centre total and "amount · %" legend rows (DB-9).
                    ...$this->ringDetails($data, current_company()?->currency?->name, __('sales::filament/widgets/sales-dashboard.revenue-by-category.revenue')),
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return $this->ringChartOptions();
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
