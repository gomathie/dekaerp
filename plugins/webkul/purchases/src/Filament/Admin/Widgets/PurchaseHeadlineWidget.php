<?php

namespace Webkul\Purchase\Filament\Admin\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
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

        $spend = (float) ((clone $confirmed)
            ->selectRaw('SUM(CASE WHEN currency_rate > 0 THEN amount_total / currency_rate ELSE amount_total END) as cc_total')
            ->value('cc_total') ?? 0.0);

        $orders = (clone $confirmed)->count();

        $openRequests = $this->purchaseOrders()
            ->whereIn('state', [OrderState::DRAFT->value, OrderState::SENT->value, OrderState::TO_APPROVE->value])
            ->count();

        return [
            Stat::make(__('invoices::filament/pages/overview.headline.spend'), compact_money($spend, $currency))
                ->description(trans_choice('invoices::filament/pages/overview.headline.orders', $orders, ['count' => $orders]))
                ->color('primary')
                ->extraAttributes(['class' => 'overview-headline-stat']),

            Stat::make(__('invoices::filament/pages/overview.headline.open-rfqs'), number_format($openRequests))
                ->color('info')
                ->extraAttributes(['class' => 'overview-headline-stat']),
        ];
    }
}
