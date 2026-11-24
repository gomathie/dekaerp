<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseOrderResource\Pages;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Webkul\Purchase\Enums\OrderState;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\OrderResource\Pages\ListOrders;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseOrderResource;
use Webkul\Purchase\Filament\Admin\Widgets\PurchaseStatsWidget;
use Webkul\Support\Filament\DashboardAccess;
use Webkul\TableViews\Filament\Components\PresetView;

class ListPurchaseOrders extends ListOrders
{
    protected static string $resource = PurchaseOrderResource::class;

    /**
     * This month's purchase figures above the list (plan #9 R-9, TRACE365's
     * reference screens). It is the Purchases dashboard's own widget and
     * queries, so the list and the dashboard cannot disagree, and its guard
     * (widget permission, module, "See dashboard") decides who sees it.
     *
     * The money-figures switch is checked here because the widget's guard
     * does not: only the dashboard pages read it, so without this line a
     * user with money figures off saw the month's purchase value here.
     */
    public function getHeaderWidgets(): array
    {
        if (! DashboardAccess::mayViewMoney()) {
            return [];
        }

        return [
            PurchaseStatsWidget::make([
                'pageFilters'   => ['start_date' => now()->startOfMonth()->toDateString(), 'end_date' => now()->toDateString()],
                'periodHeading' => __('purchases::filament/admin/widgets/purchase-dashboard.stats.this-month'),
            ]),
        ];
    }

    public function getPresetTableViews(): array
    {
        return [
            'my_orders' => PresetView::make(__('purchases::filament/admin/clusters/orders/resources/purchase-order/pages/list-purchase-orders.tabs.my-orders'))
                ->icon('heroicon-o-shopping-cart')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('user_id', Auth::id())),

            'starred_orders' => PresetView::make(__('purchases::filament/admin/clusters/orders/resources/purchase-order/pages/list-purchase-orders.tabs.starred'))
                ->icon('heroicon-s-star')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('priority', true)),

            'waiting-bills' => PresetView::make(__('purchases::filament/admin/clusters/orders/resources/purchase-order/pages/list-purchase-orders.tabs.waiting-bills'))
                ->icon('heroicon-o-clock')
                ->favorite()
                ->modifyQueryUsing(function (Builder $query) {
                    return $query
                        ->where('state', OrderState::SENT)
                        ->where('ordered_at', '<', now());
                }),

            'received-bills' => PresetView::make(__('purchases::filament/admin/clusters/orders/resources/purchase-order/pages/list-purchase-orders.tabs.received-bills'))
                ->icon('heroicon-o-document-check')
                ->favorite()
                ->modifyQueryUsing(function (Builder $query) {
                    return $query
                        ->whereIn('state', [OrderState::PURCHASE, OrderState::DONE])
                        ->where('invoice_status', 'invoiced');
                }),
        ];
    }
}
