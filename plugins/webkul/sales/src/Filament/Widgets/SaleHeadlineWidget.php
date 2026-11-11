<?php

namespace Webkul\Sale\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Webkul\Sale\Filament\Pages\SalesDashboard;
use Webkul\Sale\Filament\Widgets\Concerns\HasSaleDashboardFilters;

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
    use HasSaleDashboardFilters;

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
    public function headlineStats(array $filters): array
    {
        $this->pageFilters = $filters;

        return $this->getStats();
    }

    protected function getStats(): array
    {
        $currency = current_company()?->currency?->name;

        $revenue = (float) ($this->saleOrders()
            ->selectRaw('SUM(CASE WHEN currency_rate > 0 THEN amount_total / currency_rate ELSE amount_total END) as cc_total')
            ->value('cc_total') ?? 0.0);

        $orders = $this->saleOrders()->count();

        return [
            Stat::make(__('invoices::filament/pages/overview.headline.revenue'), compact_money($revenue, $currency))
                ->description(trans_choice('invoices::filament/pages/overview.headline.orders', $orders, ['count' => $orders]))
                ->color('success')
                ->extraAttributes(['class' => 'overview-headline-stat']),

            Stat::make(__('invoices::filament/pages/overview.headline.open-quotations'), number_format($this->quotations()->count()))
                ->color('info')
                ->extraAttributes(['class' => 'overview-headline-stat']),
        ];
    }
}
