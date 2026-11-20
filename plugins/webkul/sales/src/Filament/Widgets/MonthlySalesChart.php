<?php

namespace Webkul\Sale\Filament\Widgets;

use Webkul\Sale\Filament\Widgets\Concerns\GuardsSaleDashboardWidget;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Sale\Filament\Widgets\Concerns\HasSaleDashboardFilters;
use Webkul\Support\Filament\Widgets\DashboardChartWidget;

class MonthlySalesChart extends DashboardChartWidget
{
    use HasSaleDashboardFilters, GuardsSaleDashboardWidget;

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 1;

    public function getHeading(): string|Htmlable|null
    {
        return __('sales::filament/widgets/sales-dashboard.monthly-sales.heading');
    }

    protected function getData(): array
    {
        $query = $this->saleOrders();

        $bucket = db_dialect()->monthBucket($query->qualifyColumn('date_order'));

        $revenues = $query
            ->selectRaw("{$bucket} as period, SUM(CASE WHEN currency_rate > 0 THEN amount_total / currency_rate ELSE amount_total END) as revenue")
            ->groupByRaw($bucket)
            ->pluck('revenue', 'period');

        // Invoices made without an order count in their invoice month
        // (directInvoices(); company currency, credit notes negative).
        $invoices = $this->directInvoices();
        $invoiceBucket = db_dialect()->monthBucket($invoices->qualifyColumn('invoice_date'));

        $invoiceRevenues = $invoices
            ->selectRaw("{$invoiceBucket} as period, SUM(".$invoices->qualifyColumn('amount_total_signed').') as revenue')
            ->groupByRaw($invoiceBucket)
            ->pluck('revenue', 'period');

        [$start, $end] = $this->periodRange();

        $labels = [];
        $data = [];
        $cursor = $start->copy()->startOfMonth();

        while ($cursor <= $end) {
            $labels[] = $cursor->translatedFormat('M Y');
            $data[] = round((float) ($revenues[$cursor->format('Y-m')] ?? 0) + (float) ($invoiceRevenues[$cursor->format('Y-m')] ?? 0), 2);
            $cursor->addMonth();
        }

        return [
            'datasets' => [
                $this->gradientLineDataset($data, __('sales::filament/widgets/sales-dashboard.monthly-sales.revenue')),
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        $options = $this->lineChartOptions(12);

        $options['plugins']['lineGradient'] = $this->revenueLineGradient();

        return $options;
    }

    protected function getType(): string
    {
        return 'line';
    }
}
