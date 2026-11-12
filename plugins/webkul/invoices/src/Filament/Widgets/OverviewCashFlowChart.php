<?php

namespace Webkul\Invoice\Filament\Widgets;

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Illuminate\Contracts\Support\Htmlable;

/**
 * The one chart on the Overview's All tab (user, 2026-11-13): money in
 * (paid invoices) against money out (paid bills) per day, two thirds wide
 * beside Needs attention. The module tabs keep their full chart sets.
 */
class OverviewCashFlowChart extends RevenueOverTimeWidget
{
    protected static bool $isDiscovered = false;

    protected int|string|array $columnSpan = ['default' => 'full', 'md' => 2, 'xl' => 2];

    /**
     * Seen by whoever may see "Revenue over time": the same module switch and
     * that widget's own Shield permission. A permission of its own would
     * reach only the Admin role through erp:sync-permissions, and
     * GuardsDashboardWidget refuses a widget Shield cannot name.
     */
    protected static function getWidgetPermission(): ?string
    {
        $widget = FilamentShield::getWidgets()[RevenueOverTimeWidget::class] ?? null;

        return $widget ? array_key_first($widget['permissions']) : null;
    }

    public function getHeading(): string|Htmlable|null
    {
        return __('invoices::filament/pages/overview.cash.heading');
    }

    public function getDescription(): string|Htmlable|null
    {
        return __('invoices::filament/pages/overview.cash.description');
    }

    protected function getData(): array
    {
        [$start, $end] = $this->periodRange();

        $invoices = $this->customerInvoices();
        $bills = $this->vendorBills();

        $in = $this->dailyBuckets($invoices->where($invoices->qualifyColumn('payment_state'), 'paid'), true);
        $out = array_map('abs', $this->dailyBuckets($bills->where($bills->qualifyColumn('payment_state'), 'paid'), true));

        $labels = [];

        for ($date = (clone $start)->startOfDay(); $date->lte($end); $date->addDay()) {
            $labels[] = $date->format('M d');
        }

        $outDataset = array_merge($this->gradientLineDataset($out, __('invoices::filament/pages/overview.cash.out')), [
            'borderColor'          => $this->brandColor('gray', 400),
            'backgroundColor'      => $this->brandColor('gray', 400, 0.10),
            'pointBackgroundColor' => $this->brandColor('gray', 400),
            'borderDash'           => [4, 4],
        ]);

        return [
            'datasets' => [
                $this->gradientLineDataset($in, __('invoices::filament/pages/overview.cash.in')),
                $outDataset,
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        $options = parent::getOptions();

        // The traffic-light gradient reads "high is good" and only suits a
        // single revenue line; with money out beside it, it would colour
        // spending green. A legend names the two lines instead.
        unset($options['plugins']['lineGradient']);

        $options['plugins']['legend'] = ['display' => true, 'position' => 'bottom', 'labels' => ['usePointStyle' => true, 'boxWidth' => 8]];

        return $options;
    }
}
