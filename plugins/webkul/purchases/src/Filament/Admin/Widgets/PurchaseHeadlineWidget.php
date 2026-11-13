<?php

namespace Webkul\Purchase\Filament\Admin\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Purchase\Enums\OrderState;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseOrderResource;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\QuotationResource;
use Webkul\Purchase\Filament\Admin\Pages\Purchases;
use Webkul\Purchase\Filament\Admin\Widgets\Concerns\HasPurchaseDashboardFilters;
use Webkul\Purchase\Models\PurchaseOrder;
use Webkul\Support\Filament\Concerns\BuildsDashboardStats;

/**
 * The Purchases card in the Overview's "At a glance" row (user, 2026-11-11).
 * Confirmed spend and open requests for quotation for the chosen period, with
 * the same filters and currency conversion as the Purchases dashboard.
 *
 * Visible exactly when the Purchases section is; not a Shield widget (see
 * the Invoices headline widget for why).
 */
class PurchaseHeadlineWidget extends StatsOverviewWidget
{
    // No 5-second refresh (Filament default): every open dashboard re-ran all
    // its queries 12 times a minute (Sentry DEKAERP-11). Filters reload it.
    protected ?string $pollingInterval = null;

    use BuildsDashboardStats, HasPurchaseDashboardFilters;

    protected static bool $isLazy = false;

    protected static bool $isDiscovered = false;

    protected int|string|array $columnSpan = ['default' => 'full', 'lg' => 1];

    public static function canView(): bool
    {
        return Purchases::canAccess();
    }

    public function getHeading(): ?string
    {
        return __('invoices::filament/pages/overview.headline.purchases');
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
    public function headlineStats(array $filters): array
    {
        $this->pageFilters = $filters;

        return $this->getStats();
    }

    /**
     * Overview "Needs attention" (2026-11-13): purchases waiting for approval.
     *
     * @return list<array{label: string, count: int, url: ?string, tone: string}>
     */
    public static function attention(): array
    {
        $toApprove = PurchaseOrder::query()->where('state', OrderState::TO_APPROVE)->count();

        return [[
            'label' => trans_choice('invoices::filament/pages/overview.attention.rfqs-to-approve', $toApprove, ['count' => $toApprove]),
            'count' => $toApprove,
            'url'   => QuotationResource::canViewAny() ? QuotationResource::getUrl('index') : null,
            'tone'  => 'warning',
        ]];
    }

    /**
     * @return list<array{label: string, url: string, icon: string}>
     */
    public static function actions(): array
    {
        return QuotationResource::canCreate() ? [[
            'label' => __('invoices::filament/pages/overview.actions.new-rfq'),
            'url'   => QuotationResource::getUrl('create'),
            'icon'  => 'heroicon-m-clipboard-document-list',
        ]] : [];
    }

    protected function getStats(): array
    {
        $currency = $this->dashboardCurrency();

        $confirmed = $this->purchaseOrders()
            ->whereIn('state', [OrderState::PURCHASE->value, OrderState::DONE->value]);

        // total_cc_amount is already in company currency, and is what
        // PurchaseStatsWidget sums. purchases_orders has no amount_total
        // (that is Sales): using it 500'd the Overview (Sentry DEKAERP-12).
        $spend = (float) (clone $confirmed)->sum('total_cc_amount');

        $orders = (clone $confirmed)->count();

        // Trend against the period of the same length just before (user,
        // 2026-11-13). The same states as $confirmed, on the earlier dates.
        [$previousStart, $previousEnd] = $this->previousPeriodRange();

        $previousSpend = (float) $this->purchaseOrders($previousStart, $previousEnd)
            ->whereIn('state', [OrderState::PURCHASE->value, OrderState::DONE->value])
            ->sum('total_cc_amount');

        [$change, $trendIcon, $trendColor] = $this->statTrend($spend, $previousSpend);

        $openRequests = $this->purchaseOrders()
            ->whereIn('state', [OrderState::DRAFT->value, OrderState::SENT->value, OrderState::TO_APPROVE->value])
            ->count();

        return [
            Stat::make(__('invoices::filament/pages/overview.headline.spend'), compact_money($spend, $currency))
                ->url(PurchaseOrderResource::canViewAny() ? PurchaseOrderResource::getUrl('index') : null)
                ->icon('heroicon-o-shopping-cart')
                ->description(trans_choice('invoices::filament/pages/overview.headline.orders', $orders, ['count' => $orders])
                    .' · '.__('invoices::filament/pages/overview.trend', ['change' => $change]))
                ->descriptionIcon($trendIcon)
                ->descriptionColor($trendColor)
                ->chart($this->dailySpend(clone $confirmed))
                ->color('primary')
                ->extraAttributes(['class' => 'overview-headline-stat']),

            Stat::make(__('invoices::filament/pages/overview.headline.open-rfqs'), number_format($openRequests))
                ->url(QuotationResource::canViewAny() ? QuotationResource::getUrl('index') : null)
                ->icon('heroicon-o-clipboard-document-list')
                ->color('info')
                ->extraAttributes(['class' => 'overview-headline-stat']),
        ];
    }

    /**
     * Confirmed spend per day over the filtered period, for the card's
     * sparkline (user, 2026-11-11). Same rows and column as the total, in one
     * grouped query. Fewer than two days of orders draws no line.
     *
     * @return array<int, float>|null
     */
    protected function dailySpend(Builder $confirmed): ?array
    {
        $day = 'DATE('.$confirmed->qualifyColumn('ordered_at').')';

        $series = $confirmed->toBase()
            ->selectRaw($day.' as day, SUM('.$confirmed->qualifyColumn('total_cc_amount').') as total')
            ->groupByRaw($day)
            ->orderByRaw($day)
            ->pluck('total')
            ->map(fn ($total): float => round((float) $total, 2))
            ->all();

        return count($series) > 1 ? $series : null;
    }
}
