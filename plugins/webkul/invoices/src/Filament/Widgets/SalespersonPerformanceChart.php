<?php

namespace Webkul\Invoice\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\DB;
use Webkul\Invoice\Filament\Widgets\Concerns\GuardsInvoiceDashboardWidget;
use Webkul\Invoice\Filament\Widgets\Concerns\HasInvoiceDashboardFilters;
use Webkul\Support\Filament\Concerns\HasChartTheme;

class SalespersonPerformanceChart extends ChartWidget
{
    // DB-4 layout on the Overview's six-column grid (2026-11-23).
    protected int|string|array $columnSpan = ['default' => 'full', 'md' => 1, 'xl' => 2];

    // No 5-second refresh (Filament default): every open dashboard re-ran all
    // its queries 12 times a minute (Sentry DEKAERP-11). Filters reload it.
    protected ?string $pollingInterval = null;

    use GuardsInvoiceDashboardWidget, HasChartTheme, HasInvoiceDashboardFilters;

    protected static ?int $sort = 6;

    protected static bool $isLazy = true;

    protected ?string $maxHeight = '240px';

    public function getHeading(): string|Htmlable|null
    {
        return __('invoices::filament/widgets/invoice-dashboard.salespersons.heading');
    }

    protected function getData(): array
    {
        // An invoice with no salesperson counts for whoever created it: before
        // 2026-11-20 the invoice form left the salesperson empty, so most
        // invoices would otherwise count for nobody. Net of credit notes, like
        // the "Total invoiced" tile (DB-2, 2026-11-23), so a refunded sale
        // does not stay on its salesperson's bar.
        $rows = $this->customerInvoicesNet()
            ->join('users', 'users.id', '=', DB::raw('COALESCE(accounts_account_moves.invoice_user_id, accounts_account_moves.creator_id)'))
            ->groupBy('users.id', 'users.name')
            ->addSelect('users.name as name')
            ->selectRaw('SUM(accounts_account_moves.amount_total_signed) as total_billed')
            ->havingRaw('SUM(accounts_account_moves.amount_total_signed) > 0')
            ->orderByDesc('total_billed')
            ->limit(6)
            ->get();

        $values = $rows->map(fn ($row): float => round((float) $row->total_billed, 2))->all();

        $ramp = $this->chartRamp();

        $colors = array_map(fn (int $index): string => $ramp[$index % count($ramp)], array_keys($values));

        return [
            'datasets' => [
                [
                    'label'           => '',
                    'data'            => $values,
                    'backgroundColor' => $colors,
                    'borderRadius'    => 8,
                    'borderSkipped'   => 'bottom',
                    'maxBarThickness' => 34,
                ],
            ],
            'labels' => $rows
                ->map(fn ($row): string => $row->name ?: __('invoices::filament/widgets/invoice-dashboard.unknown'))
                ->all(),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'interaction'         => $this->chartInteraction('index'),
            'plugins'             => [
                'entrance' => ['type' => 'rising-bars', 'duration' => 700, 'stagger' => 90],

                'legend'  => ['display' => false],
                'tooltip' => $this->chartTooltip(),
            ],
            'scales'              => $this->cartesianScales(6, false),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
