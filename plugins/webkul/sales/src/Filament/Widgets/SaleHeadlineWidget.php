<?php

namespace Webkul\Sale\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Models\Move;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Filament\Clusters\Orders\Resources\OrderResource;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource;
use Webkul\Sale\Filament\Pages\SalesDashboard;
use Webkul\Sale\Filament\Widgets\Concerns\HasSaleDashboardFilters;
use Webkul\Sale\Models\Order;
use Webkul\Support\Filament\Concerns\BuildsDashboardStats;

/**
 * The Sales card in the Overview's "At a glance" row (user, 2026-11-11).
 * Confirmed revenue and open quotations for the chosen period, computed with
 * the same filters and currency conversion as SaleStatsOverview.
 *
 * Visible exactly when the Sales section is; not a Shield widget (see
 * InvoiceHeadlineWidget for why).
 */
class SaleHeadlineWidget extends StatsOverviewWidget
{
    // No 5-second refresh (Filament default): every open dashboard re-ran all
    // its queries 12 times a minute (Sentry DEKAERP-11). Filters reload it.
    protected ?string $pollingInterval = null;

    use BuildsDashboardStats, HasSaleDashboardFilters;

    protected static bool $isLazy = false;

    protected static bool $isDiscovered = false;

    protected int|string|array $columnSpan = ['default' => 'full', 'lg' => 1];

    public static function canView(): bool
    {
        return SalesDashboard::canAccess();
    }

    public function getHeading(): ?string
    {
        return __('invoices::filament/pages/overview.headline.sales');
    }

    protected function getColumns(): int
    {
        return 2;
    }

    /**
     * This section's figures for the Overview's combined "At a glance"
     * row, computed with the page filters handed in.
     *
     * @param  array<string, mixed>  $filters
     * @return array<int, Stat>
     */
    /**
     * Overview "Needs attention" (2026-11-13): quotations sent more than a
     * week ago and still unanswered, today's state, not the date filter.
     *
     * @return list<array{label: string, count: int, url: ?string, tone: string}>
     */
    public static function attention(): array
    {
        $waiting = Order::query()
            ->where('state', OrderState::SENT)
            ->where('date_order', '<', now()->subDays(7))
            ->count();

        return [[
            'label' => trans_choice('invoices::filament/pages/overview.attention.old-quotations', $waiting, ['count' => $waiting]),
            'count' => $waiting,
            'url'   => QuotationResource::canViewAny() ? QuotationResource::getUrl('index') : null,
            'tone'  => 'info',
        ]];
    }

    /**
     * @return list<array{label: string, url: string, icon: string}>
     */
    public static function actions(): array
    {
        return QuotationResource::canCreate() ? [[
            'label' => __('invoices::filament/pages/overview.actions.new-quotation'),
            'url'   => QuotationResource::getUrl('create'),
            'icon'  => 'heroicon-m-document-text',
        ]] : [];
    }

    public function headlineStats(array $filters): array
    {
        $this->pageFilters = $filters;

        return $this->getStats();
    }

    /**
     * The date of the latest sale or quotation before the period, when the
     * period itself has none (U69, 2026-11-23). Orders and invoices made
     * without an order both count, like the Revenue figure.
     *
     * @param  array<string, mixed>  $filters
     */
    public function latestBeforePeriod(array $filters): ?Carbon
    {
        $this->pageFilters = $filters;

        // A confirmed order not invoiced yet is still activity in the period,
        // although salesCount() (invoiced sales, 2026-11-25) leaves it out.
        if ($this->salesCount() > 0 || $this->saleOrders()->exists() || $this->quotations()->exists()) {
            return null;
        }

        [$start] = $this->periodRange();

        $orders = Order::query();
        $latestOrder = $orders->whereDate($orders->qualifyColumn('date_order'), '<', $start)->max($orders->qualifyColumn('date_order'));

        $invoices = Move::query();
        $latestInvoice = $invoices
            ->where($invoices->qualifyColumn('move_type'), MoveType::OUT_INVOICE)
            ->where($invoices->qualifyColumn('state'), MoveState::POSTED)
            ->whereDate($invoices->qualifyColumn('invoice_date'), '<', $start)
            ->max($invoices->qualifyColumn('invoice_date'));

        $dates = array_filter([$latestOrder, $latestInvoice]);

        return $dates === [] ? null : collect($dates)->map(fn ($date): Carbon => Carbon::parse($date))->max();
    }

    protected function getStats(): array
    {
        $currency = current_company()?->currency?->name;

        $revenue = $this->revenueBetween();

        // Trend against the period of the same length just before (user,
        // 2026-11-13: every card on finance dashboards shows direction).
        [$previousStart, $previousEnd] = $this->previousPeriodRange();
        [$change, $trendIcon, $trendColor] = $this->statTrend($revenue, $this->revenueBetween($previousStart, $previousEnd));

        $orders = $this->salesCount();

        // DB-4 (approved mockup, 2026-11-23): Revenue, Sales, Average sale,
        // Open quotations. The middle two come from SaleStatsOverview, which
        // owns them, and only for someone that widget admits.
        $middle = $this->borrowedStats(SaleStatsOverview::class, [1, 3]);

        return [
            Stat::make(__('invoices::filament/pages/overview.headline.revenue'), compact_money($revenue, $currency))
                ->url(OrderResource::canViewAny() ? OrderResource::getUrl('index') : null)
                ->icon('heroicon-o-banknotes')
                // The trend as a pill with its arrow (DB-9, 2026-11-24).
                ->description($this->trendDescription(
                    trans_choice('invoices::filament/pages/overview.headline.orders', $orders, ['count' => $orders]),
                    $change,
                    $trendIcon,
                    $trendColor,
                    trim(__('invoices::filament/pages/overview.trend', ['change' => ''])),
                ))
                ->chart($this->dailyRevenue())
                ->color('success')
                ->extraAttributes(['class' => 'overview-headline-stat']),

            ...$middle,

            Stat::make(__('invoices::filament/pages/overview.headline.open-quotations'), number_format($this->quotations()->count()))
                ->url(QuotationResource::canViewAny() ? QuotationResource::getUrl('index') : null)
                ->icon('heroicon-o-document-text')
                ->color('info')
                ->extraAttributes(['class' => 'overview-headline-stat']),
        ];
    }

    /**
     * The Sales panel on the Overview's All tab (mockup approved 2026-11-13):
     * revenue with its daily line, orders with a daily bar strip, and the
     * share of the period's quotations that became orders, each with its
     * change against the previous period of the same length. Plain values;
     * the Overview board renders them.
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function panel(array $filters): array
    {
        $this->pageFilters = $filters;

        [$previousStart, $previousEnd] = $this->previousPeriodRange();

        $revenue = $this->revenueBetween();
        $orders = $this->salesCount();
        $previousOrders = $this->salesCount($previousStart, $previousEnd);

        $conversion = $this->conversionRate();
        $previousConversion = $this->conversionRate($previousStart, $previousEnd);

        $lang = 'invoices::filament/pages/overview.board';

        return [
            'label'   => __('invoices::filament/pages/overview.sections.sales'),
            'url'     => OrderResource::canViewAny() ? OrderResource::getUrl('index') : null,
            'metrics' => [
                [
                    'label'    => __('invoices::filament/pages/overview.headline.revenue'),
                    'value'    => compact_money($revenue, current_company()?->currency?->name),
                    'trend'    => $this->trendOf($revenue, $this->revenueBetween($previousStart, $previousEnd)),
                    'viz'      => ['type' => 'line', 'values' => $this->dailyRevenue() ?? []],
                ],
                [
                    'label' => __($lang.'.orders'),
                    'value' => number_format($orders),
                    'trend' => $this->trendOf((float) $orders, (float) $previousOrders),
                    'viz'   => ['type' => 'bars', 'values' => $this->dailyOrders()],
                ],
                [
                    'label' => __($lang.'.conversion'),
                    'value' => $conversion === null ? '—' : $conversion.'%',
                    'trend' => $conversion === null || $previousConversion === null ? null : $this->trendOf((float) $conversion, (float) $previousConversion),
                    'viz'   => ['type' => 'gauge', 'percent' => $conversion ?? 0],
                ],
            ],
        ];
    }

    /**
     * @return array{text: string, direction: string}
     */
    protected function trendOf(float $current, float $previous): array
    {
        [$text, $icon] = $this->statTrend($current, $previous);

        return ['text' => $text, 'direction' => str_contains($icon, 'down') ? 'down' : (str_contains($icon, 'up') ? 'up' : 'flat')];
    }

    /**
     * Confirmed orders as a share of all quotations dated in the period
     * (orders plus those still open). Null when there were none, so an empty
     * month reads "—" rather than 0%.
     */
    protected function conversionRate(?Carbon $start = null, ?Carbon $end = null): ?int
    {
        $orders = $this->saleOrders($start, $end)->count();
        $open = $this->quotations($start, $end)->count();

        return ($orders + $open) > 0 ? (int) round($orders / ($orders + $open) * 100) : null;
    }

    /**
     * Sales per day (confirmed orders and invoices made without one), for
     * the bar strip.
     *
     * @return list<int>
     */
    protected function dailyOrders(): array
    {
        return array_values($this->dailySalesCounts());
    }

    /** Confirmed revenue in company currency; the page period when no dates are given. */
    protected function revenueBetween(?Carbon $start = null, ?Carbon $end = null): float
    {
        // Invoiced, not ordered (DB-8): a sale invoiced without an order counts.
        return $this->invoicedRevenue($start, $end);
    }

    /**
     * Revenue per day over the filtered period, for the card's sparkline
     * (user, 2026-11-11: modern cards show direction, not only a total). One
     * grouped query over the same rows as the total, so the two agree. Fewer
     * than two days of sales draws no line.
     *
     * @return array<int, float>|null
     */
    protected function dailyRevenue(): ?array
    {
        $query = $this->invoicedSales();
        $day = 'DATE('.$query->qualifyColumn('invoice_date').')';

        $series = $query->toBase()
            ->selectRaw($day.' as day, SUM('.$query->qualifyColumn('amount_total_signed').') as total')
            ->groupByRaw($day)
            ->orderByRaw($day)
            ->pluck('total')
            ->map(fn ($total): float => round((float) $total, 2))
            ->all();

        return count($series) > 1 ? $series : null;
    }
}
