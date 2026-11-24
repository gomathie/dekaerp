<?php

namespace Webkul\Sale\Filament\Widgets;

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Webkul\Sale\Enums\InvoiceStatus;
use Webkul\Sale\Enums\OrderDeliveryStatus;
use Webkul\Sale\Filament\Clusters\Orders\Resources\OrderResource;
use Webkul\Sale\Filament\Widgets\Concerns\GuardsSaleDashboardWidget;
use Webkul\Sale\Filament\Widgets\Concerns\HasSaleDashboardFilters;

/**
 * "Order activity" (DB-9, approved 2026-11-24, from TRACE365's reference
 * screens): of the orders confirmed in the period, how many are still to
 * deliver, delivered, still to invoice and invoiced, each opening the order
 * list on the matching view. The period and filters are the page's, like
 * every other figure on it.
 */
class OrderActivityWidget extends StatsOverviewWidget
{
    use GuardsSaleDashboardWidget, HasSaleDashboardFilters;

    /** Delivery states that still have goods to send. */
    public const TO_DELIVER = [OrderDeliveryStatus::PENDING, OrderDeliveryStatus::STARTED, OrderDeliveryStatus::PARTIAL];

    // No 5-second refresh (Sentry DEKAERP-11). Filters reload it.
    protected ?string $pollingInterval = null;

    protected static bool $isLazy = false;

    protected static bool $isDiscovered = false;

    protected int|string|array $columnSpan = 'full';

    /**
     * The order counts are the Key Metrics widget's figures split by state,
     * so they follow its Shield permission. A permission of its own would
     * reach only the Admin role through erp:sync-permissions, and
     * GuardsDashboardWidget refuses a widget Shield cannot name.
     */
    protected static function getWidgetPermission(): ?string
    {
        $widget = FilamentShield::getWidgets()[SaleStatsOverview::class] ?? null;

        return $widget ? array_key_first($widget['permissions']) : null;
    }

    public function getHeading(): ?string
    {
        return __('sales::filament/widgets/sales-dashboard.order-activity.heading');
    }

    public function getDescription(): ?string
    {
        return __('sales::filament/widgets/sales-dashboard.order-activity.description');
    }

    protected function getColumns(): int
    {
        return 4;
    }

    protected function getStats(): array
    {
        $total = $this->saleOrders()->count();

        $lang = 'sales::filament/widgets/sales-dashboard.order-activity';

        return [
            $this->activityStat(__($lang.'.to-deliver'), 'heroicon-o-truck', 'warning', $this->saleOrders()->whereIn('delivery_status', self::TO_DELIVER)->count(), $total, 'to_deliver'),
            $this->activityStat(__($lang.'.delivered'), 'heroicon-o-check-circle', 'success', $this->saleOrders()->where('delivery_status', OrderDeliveryStatus::FULL)->count(), $total, 'delivered'),
            $this->activityStat(__($lang.'.to-invoice'), 'heroicon-o-receipt-percent', 'info', $this->saleOrders()->where('invoice_status', InvoiceStatus::TO_INVOICE)->count(), $total, 'to_invoice'),
            $this->activityStat(__($lang.'.invoiced'), 'heroicon-o-document-check', 'primary', $this->saleOrders()->where('invoice_status', InvoiceStatus::INVOICED)->count(), $total, 'invoiced'),
        ];
    }

    protected function activityStat(string $label, string $icon, string $color, int $count, int $total, string $view): Stat
    {
        $percent = $total > 0 ? (int) round($count / $total * 100) : 0;

        return Stat::make($label, number_format($count))
            ->icon($icon)
            ->description(__('sales::filament/widgets/sales-dashboard.order-activity.share', ['percent' => $percent, 'total' => number_format($total)]))
            // Only for someone who may open the orders list.
            ->url(OrderResource::canViewAny() ? OrderResource::getUrl('index', ['activeTableView' => $view]) : null)
            ->extraAttributes(['class' => 'dashboard-stat dashboard-stat--'.$color]);
    }
}
