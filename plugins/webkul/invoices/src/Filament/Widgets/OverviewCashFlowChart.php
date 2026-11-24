<?php

namespace Webkul\Invoice\Filament\Widgets;

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\HtmlString;
use Webkul\Account\Enums\PaymentStatus;
use Webkul\Account\Enums\PaymentType;
use Webkul\Account\Models\Payment;
use Webkul\Support\Filament\DashboardPeriod;

/**
 * The one chart on the Overview's All tab (user, 2026-11-13), and the main
 * chart of the Invoices tab: money received against money paid out, as
 * paired bars with both totals above them (DB-9, 2026-11-24).
 */
class OverviewCashFlowChart extends RevenueOverTimeWidget
{
    protected static bool $isDiscovered = false;

    // Every 5 minutes on the Overview (user, 2026-11-13); see OverviewHeadlineWidget.
    protected ?string $pollingInterval = '300s';

    /**
     * The grouping switch (DB-9, approved 2026-11-24): Automatic keeps the
     * period-length rule. *Superseded:* the select used to switch a faint
     * "previous period" line on (DB-4); the comparison is now the two
     * totals with their trend pills in the description, as on TRACE365's
     * reference screens.
     */
    public ?string $filter = 'auto';

    protected int|string|array $columnSpan = 'full';

    /**
     * Daily series already read in this request, keyed by type and dates:
     * the description and the bars use the same four series.
     *
     * @var array<string, list<float>>
     */
    protected array $paymentSeries = [];

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

    /**
     * Both totals above the bars, each with its change against the previous
     * period (DB-9): "Money in GH₵12.4k [▲ 8%] · Money out GH₵3.1k [▼ 2%]
     * vs 1 Jan – 24 Nov". Rendered by Blade on every update, so it follows
     * the page filters; chart options would not (Filament sends only data).
     * Money out uses lowerIsBetter: spending less is the good direction.
     */
    public function getDescription(): string|Htmlable|null
    {
        [$start, $end] = $this->periodRange();
        [$previousStart, $previousEnd] = $this->previousPeriodRange();
        $currency = $this->dashboardCurrency();

        $parts = [];

        foreach (['in' => PaymentType::RECEIVE, 'out' => PaymentType::SEND] as $key => $type) {
            $total = array_sum($this->dailyPayments($type, $start, $end));
            $previous = array_sum($this->dailyPayments($type, $previousStart, $previousEnd));

            [$change, $icon, $color] = $this->statTrend($total, $previous, lowerIsBetter: $key === 'out', money: true);

            $parts[] = (string) $this->trendDescription(
                __('invoices::filament/pages/overview.cash.'.$key).' '.compact_money($total, $currency),
                $change,
                $icon,
                $color,
            );
        }

        return new HtmlString(implode(' · ', $parts).' <span class="dashboard-trend-caption">'.e(__('support::support.trend.vs', ['range' => $this->baselineLabel()])).'</span>');
    }

    protected function getData(): array
    {
        [$start, $end] = $this->periodRange();

        // Grouped by the switch, or by the period's length (DB-4, DB-9).
        ['labels' => $labels, 'values' => $in] = DashboardPeriod::rollUp($this->dailyPayments(PaymentType::RECEIVE, $start, $end), $start, $end, $this->filter);
        $out = DashboardPeriod::rollUp($this->dailyPayments(PaymentType::SEND, $start, $end), $start, $end, $this->filter)['values'];

        // Paired bars (DB-9 board): money in in the brand colour, money out
        // in grey, as in the references.
        return [
            'datasets' => [
                $this->cashBarDataset($in, __('invoices::filament/pages/overview.cash.in'), $this->brandColor('primary', 500)),
                $this->cashBarDataset($out, __('invoices::filament/pages/overview.cash.out'), $this->brandColor('gray', 400)),
            ],
            'labels'   => $labels,
        ];
    }

    /**
     * @param  list<float>  $values
     * @return array<string, mixed>
     */
    protected function cashBarDataset(array $values, string $label, string $color): array
    {
        return [
            'label'              => $label,
            'data'               => $values,
            'backgroundColor'    => $color,
            'borderRadius'       => 6,
            'borderSkipped'      => 'bottom',
            'maxBarThickness'    => 28,
            'categoryPercentage' => 0.7,
            'barPercentage'      => 0.9,
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function getFilters(): ?array
    {
        return DashboardPeriod::groupingOptions();
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
        $cacheKey = $type->value.'|'.$start->toDateString().'|'.$end->toDateString();

        return $this->paymentSeries[$cacheKey] ??= $this->readDailyPayments($type, $start, $end);
    }

    /**
     * @return list<float>
     */
    protected function readDailyPayments(PaymentType $type, Carbon $start, Carbon $end): array
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

    /**
     * Bars with a legend naming the two series. No traffic-light gradient:
     * it reads "high is good" and would colour spending green.
     */
    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'interaction'         => $this->chartInteraction('index'),
            'plugins'             => [
                'entrance' => ['type' => 'rising-bars', 'duration' => 700, 'stagger' => 40],

                'legend'  => ['display' => true, 'position' => 'bottom', 'labels' => ['usePointStyle' => true, 'pointStyle' => 'rectRounded', 'boxWidth' => 8]],
                'tooltip' => $this->chartTooltip(),
            ],
            'scales'              => $this->cartesianScales(12),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
