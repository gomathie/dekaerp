<?php

namespace Webkul\Sale\Filament\Widgets;

use Webkul\Sale\Filament\Widgets\Concerns\GuardsSaleDashboardWidget;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Sale\Filament\Widgets\Concerns\HasSaleDashboardFilters;
use Webkul\Support\Filament\Widgets\DashboardChartWidget;

class SalesPersonPerformanceChart extends DashboardChartWidget
{
    use HasSaleDashboardFilters, GuardsSaleDashboardWidget;

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    public function getHeading(): string|Htmlable|null
    {
        return __('sales::filament/widgets/sales-dashboard.sales-persons.heading');
    }

    protected function getData(): array
    {
        $revenue = 'SUM(CASE WHEN sales_orders.currency_rate > 0 THEN sales_orders.amount_total / sales_orders.currency_rate ELSE sales_orders.amount_total END)';

        $rows = $this->saleOrders()
            ->whereNotNull('sales_orders.user_id')
            ->join('users', 'users.id', '=', 'sales_orders.user_id')
            ->groupBy('users.id', 'users.name')
            ->addSelect('users.id as user_id', 'users.name as name')
            ->selectRaw("{$revenue} as revenue")
            ->get();

        // Invoices made without an order count for their salesperson too
        // (directInvoices()). Merged per user, then the top six.
        $invoices = $this->directInvoices();

        $invoiceRows = $invoices
            ->whereNotNull($invoices->qualifyColumn('invoice_user_id'))
            ->join('users', 'users.id', '=', $invoices->qualifyColumn('invoice_user_id'))
            ->groupBy('users.id', 'users.name')
            ->select('users.id as user_id', 'users.name as name')
            ->selectRaw('SUM('.$invoices->qualifyColumn('amount_total_signed').') as revenue')
            ->get();

        $rows = $rows->concat($invoiceRows)
            ->groupBy('user_id')
            ->map(fn ($group): object => (object) ['name' => $group->first()->name, 'revenue' => $group->sum(fn ($row): float => (float) $row->revenue)])
            ->filter(fn (object $row): bool => $row->revenue > 0)
            ->sortByDesc('revenue')
            ->take(6)
            ->values();

        $values = $rows->map(fn ($row): float => round((float) $row->revenue, 2))->all();

        $ramp = $this->chartRamp();

        $colors = array_map(fn (int $index): string => $ramp[$index % count($ramp)], array_keys($values));

        return [
            'datasets' => [
                [
                    'label'           => __('sales::filament/widgets/sales-dashboard.sales-persons.revenue'),
                    'data'            => $values,
                    'backgroundColor' => $colors,
                    'borderRadius'    => 8,
                    'borderSkipped'   => 'bottom',
                    'maxBarThickness' => 34,
                ],
            ],
            'labels' => $rows
                ->map(fn ($row): string => $row->name ?: __('sales::filament/widgets/sales-dashboard.unknown'))
                ->all(),
        ];
    }

    protected function getOptions(): array
    {
        return $this->barChartOptions(6);
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
