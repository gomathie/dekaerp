<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\OrderResource\Pages;

use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\InvoiceResource as CustomerInvoiceResource;
use Webkul\Sale\Enums\InvoiceStatus;
use Webkul\Sale\Enums\OrderDeliveryStatus;
use Webkul\Sale\Filament\Clusters\Orders\Resources\OrderResource;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Pages\ListQuotations as BaseListOrders;
use Webkul\Sale\Filament\Widgets\OrderActivityWidget;
use Webkul\Sale\Filament\Widgets\SaleStatsOverview;
use Webkul\Support\Filament\DashboardAccess;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

class ListOrders extends BaseListOrders
{
    use HasTableViews;

    protected static string $resource = OrderResource::class;

    /**
     * "New direct sale" here too (user, 2026-11-22): Orders is where people
     * look for a sale. It opens the invoice form, which creates the order,
     * delivery and payment in one save; Sales has no second sale path.
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('newDirectSale')
                ->label(__('accounts::filament/resources/invoice/pages/create-invoice.direct-sale.button'))
                ->icon('heroicon-o-banknotes')
                ->color('gray')
                // canAccess() carries the Invoices module switch; canCreate()
                // the permission. A link the company cannot open is not shown.
                ->visible(fn (): bool => CustomerInvoiceResource::canAccess() && CustomerInvoiceResource::canCreate())
                ->url(fn (): string => CustomerInvoiceResource::getUrl('create', ['direct_sale' => 1])),
            ...parent::getHeaderActions(),
        ];
    }

    /**
     * This month's sales figures above the orders list (plan #9 R-9), like
     * the invoice list. It is the Sales dashboard's own widget and queries,
     * so the list and the dashboard cannot disagree, and its guard (widget
     * permission, Sales module) decides who sees it.
     *
     * The money-figures switch is checked here because the widget's guard
     * does not: only the dashboard pages read it, so without this line a
     * user with money figures off saw the month's revenue here (as on the
     * invoice, bill and purchase order lists).
     */
    public function getHeaderWidgets(): array
    {
        if (! DashboardAccess::mayViewMoney()) {
            return [];
        }

        return [
            SaleStatsOverview::make([
                'pageFilters'   => ['startDate' => now()->startOfMonth()->toDateString(), 'endDate' => now()->toDateString()],
                'periodHeading' => __('sales::filament/widgets/sales-dashboard.stats.this-month'),
            ]),
        ];
    }

    public function getPresetTableViews(): array
    {
        return [
            'my_orders' => PresetView::make(__('sales::filament/clusters/orders/resources/order/pages/list-orders.tabs.my-orders'))
                ->icon('heroicon-s-shopping-bag')
                ->favorite()
                // Not the default (user, 2026-11-22): it showed "No Orders" to
                // everyone who is not the salesperson, admins included, so the
                // list looked empty. Opening on all orders is what people expect.
                ->modifyQueryUsing(fn (Builder $query) => $query->where('user_id', Auth::id())),
            'to_invoice' => PresetView::make(__('sales::filament/clusters/orders/resources/order/pages/list-orders.tabs.to-invoice'))
                ->icon('heroicon-s-receipt-percent')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('invoice_status', InvoiceStatus::TO_INVOICE)),
            'up_selling' => PresetView::make(__('sales::filament/clusters/orders/resources/order/pages/list-orders.tabs.up-selling'))
                ->icon('heroicon-s-receipt-refund')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('invoice_status', InvoiceStatus::UP_SELLING)),
            // Opened by the Sales dashboard's order activity tiles (DB-9,
            // 2026-11-24); not favourites, so the tab row stays as it was.
            'to_deliver' => PresetView::make(__('sales::filament/clusters/orders/resources/order/pages/list-orders.tabs.to-deliver'))
                ->icon('heroicon-s-truck')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('delivery_status', OrderActivityWidget::TO_DELIVER)),
            'delivered' => PresetView::make(__('sales::filament/clusters/orders/resources/order/pages/list-orders.tabs.delivered'))
                ->icon('heroicon-s-check-circle')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('delivery_status', OrderDeliveryStatus::FULL)),
            'invoiced' => PresetView::make(__('sales::filament/clusters/orders/resources/order/pages/list-orders.tabs.invoiced'))
                ->icon('heroicon-s-document-check')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('invoice_status', InvoiceStatus::INVOICED)),
            'archived' => PresetView::make(__('sales::filament/clusters/orders/resources/order/pages/list-orders.tabs.archived'))
                ->icon('heroicon-s-archive-box')
                ->favorite()
                ->modifyQueryUsing(fn ($query) => $query->onlyTrashed()),
        ];
    }
}
