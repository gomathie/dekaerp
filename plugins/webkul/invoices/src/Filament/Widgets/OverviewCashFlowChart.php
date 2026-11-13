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

    // Every 5 minutes on the Overview (user, 2026-11-13); see OverviewHeadlineWidget.
    protected ?string $pollingInterval = '300s';

    public ?string $filter = 'current';

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

        $datasets = [
            $this->gradientLineDataset($in, __('invoices::filament/pages/overview.cash.in')),
            $outDataset,
        ];

        // "vs previous period" (approved mockup): the money in of the period
        // of the same length just before, day by day, as a faint line.
        if ($this->filter === 'compare') {
            // The invoice query takes its own date range: without it the
            // page period would filter the earlier dates away.
            $previous = $this->customerInvoices(...$this->previousPeriodRange());

            $datasets[] = [
                'label'       => __('invoices::filament/pages/overview.cash-compare.previous'),
                'data'        => $this->previousDailyPaid($previous->where($previous->qualifyColumn('payment_state'), 'paid'), count($labels)),
                'borderColor' => $this->brandColor('primary', 500, 0.45),
                'fill'        => false,
                'tension'     => 0.4,
                'borderWidth' => 1.5,
                'pointRadius' => 0,
            ];
        }

        return [
            'datasets' => $datasets,
            'labels'   => $labels,
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function getFilters(): ?array
    {
        return [
            'current' => __('invoices::filament/pages/overview.cash-compare.this-period'),
            'compare' => __('invoices::filament/pages/overview.cash-compare.vs-previous'),
        ];
    }

    /**
     * Daily paid totals of the previous period, aligned to this period's days
     * (day 1 against day 1), so the two lines share one axis.
     *
     * @return list<float>
     */
    protected function previousDailyPaid(\Illuminate\Database\Eloquent\Builder $query, int $days): array
    {
        [$start, $end] = $this->previousPeriodRange();

        $dateColumn = $query->qualifyColumn($this->dashboardDateColumn());
        $valueColumn = $query->qualifyColumn($this->dashboardValueColumn());
        $grammar = $query->getQuery()->getGrammar();
        $day = 'DATE('.$grammar->wrap($dateColumn).')';

        $byDay = $query->toBase()
            ->whereBetween($dateColumn, [$start, $end])
            ->selectRaw($day.' as bucket_day, SUM('.$grammar->wrap($valueColumn).') as bucket_value')
            ->groupByRaw($day)
            ->pluck('bucket_value', 'bucket_day')
            ->mapWithKeys(fn ($value, $key): array => [\Illuminate\Support\Carbon::parse($key)->format('Y-m-d') => abs((float) $value)]);

        $series = [];

        for ($i = 0, $date = (clone $start)->startOfDay(); $i < $days; $i++, $date->addDay()) {
            $series[] = round($byDay->get($date->format('Y-m-d'), 0.0), 2);
        }

        return $series;
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
