<?php

namespace Webkul\Sale\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Webkul\Sale\Filament\Clusters\Orders\Resources\OrderResource;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource;
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
    // No 5-second refresh (Filament default): every open dashboard re-ran all
    // its queries 12 times a minute (Sentry DEKAERP-11). Filters reload it.
    protected ?string $pollingInterval = null;

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
                ->url(OrderResource::canViewAny() ? OrderResource::getUrl('index') : null)
                ->icon('heroicon-o-banknotes')
                ->description(trans_choice('invoices::filament/pages/overview.headline.orders', $orders, ['count' => $orders]))
                ->chart($this->dailyRevenue())
                ->color('success')
                ->extraAttributes(['class' => 'overview-headline-stat']),

            Stat::make(__('invoices::filament/pages/overview.headline.open-quotations'), number_format($this->quotations()->count()))
                ->url(QuotationResource::canViewAny() ? QuotationResource::getUrl('index') : null)
                ->icon('heroicon-o-document-text')
                ->color('info')
                ->extraAttributes(['class' => 'overview-headline-stat']),
        ];
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
        $query = $this->saleOrders();
        $day = 'DATE('.$query->qualifyColumn('date_order').')';

        $series = $query->toBase()
            ->selectRaw($day.' as day, SUM(CASE WHEN currency_rate > 0 THEN amount_total / currency_rate ELSE amount_total END) as total')
            ->groupByRaw($day)
            ->orderByRaw($day)
            ->pluck('total')
            ->map(fn ($total): float => round((float) $total, 2))
            ->all();

        return count($series) > 1 ? $series : null;
    }
}
