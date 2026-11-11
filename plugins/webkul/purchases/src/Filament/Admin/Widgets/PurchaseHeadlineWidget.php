<?php

namespace Webkul\Purchase\Filament\Admin\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Purchase\Enums\OrderState;
use Webkul\Purchase\Filament\Admin\Pages\Purchases;
use Webkul\Purchase\Filament\Admin\Widgets\Concerns\HasPurchaseDashboardFilters;

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

    use HasPurchaseDashboardFilters;

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

        $openRequests = $this->purchaseOrders()
            ->whereIn('state', [OrderState::DRAFT->value, OrderState::SENT->value, OrderState::TO_APPROVE->value])
            ->count();

        return [
            Stat::make(__('invoices::filament/pages/overview.headline.spend'), compact_money($spend, $currency))
                ->icon('heroicon-o-shopping-cart')
                ->description(trans_choice('invoices::filament/pages/overview.headline.orders', $orders, ['count' => $orders]))
                ->chart($this->dailySpend(clone $confirmed))
                ->color('primary')
                ->extraAttributes(['class' => 'overview-headline-stat']),

            Stat::make(__('invoices::filament/pages/overview.headline.open-rfqs'), number_format($openRequests))
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
