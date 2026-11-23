<?php

namespace Webkul\Invoice\Filament\Widgets;

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Webkul\Account\Enums\PaymentStatus;
use Webkul\Account\Enums\PaymentType;
use Webkul\Account\Models\Payment;
use Webkul\Support\Filament\DashboardPeriod;

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

        // Grouped by day, week or month for the period's length (DB-4).
        ['labels' => $labels, 'values' => $in] = DashboardPeriod::rollUp($this->dailyPayments(PaymentType::RECEIVE, $start, $end), $start, $end);
        $out = DashboardPeriod::rollUp($this->dailyPayments(PaymentType::SEND, $start, $end), $start, $end)['values'];

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
            [$previousStart, $previousEnd] = $this->previousPeriodRange();

            $datasets[] = [
                'label'       => __('invoices::filament/pages/overview.cash-compare.previous'),
                'data'        => array_slice(DashboardPeriod::rollUp($this->dailyPayments(PaymentType::RECEIVE, $previousStart, $previousEnd), $previousStart, $previousEnd)['values'], 0, count($labels)),
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
     * Money received (inbound) or paid out (outbound) per day, by the
     * payment's own date, in the company's currency, zero-filled.
     *
     * Read from payments, not paid invoices (TRACE365, 2026-11-19): an
     * invoice is dated when issued, so money received today against an
     * older invoice never showed on today. In process counts with paid:
     * both are recorded receipts, and a bank payment stays in process until
     * reconciled. The partner filters narrow payments; the salesperson,
     * product and category filters describe invoices and do not.
     *
     * @return list<float>
     */
    protected function dailyPayments(PaymentType $type, Carbon $start, Carbon $end): array
    {
        $query = Payment::query();
        $filters = $this->dashboardFilters();
        $partnerFilter = $type === PaymentType::RECEIVE ? 'customer_id' : 'vendor_id';

        $byDay = $query
            ->where($query->qualifyColumn('payment_type'), $type)
            ->whereIn($query->qualifyColumn('state'), [PaymentStatus::PAID, PaymentStatus::IN_PROCESS])
            ->whereBetween($query->qualifyColumn('date'), [$start->toDateString(), $end->toDateString()])
            ->when(! empty($filters[$partnerFilter]), fn (Builder $q) => $q->whereIn($q->qualifyColumn('partner_id'), (array) $filters[$partnerFilter]))
            ->toBase()
            ->selectRaw('date as bucket_day, SUM(ABS(amount_company_currency_signed)) as bucket_value')
            ->groupBy('date')
            ->pluck('bucket_value', 'bucket_day')
            ->mapWithKeys(fn ($value, $key): array => [Carbon::parse($key)->format('Y-m-d') => (float) $value]);

        $series = [];

        for ($date = (clone $start)->startOfDay(); $date->lte($end); $date->addDay()) {
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
