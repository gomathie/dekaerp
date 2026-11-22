<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\OrderResource\Pages;

use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\InvoiceResource as CustomerInvoiceResource;
use Webkul\Sale\Enums\InvoiceStatus;
use Webkul\Sale\Filament\Clusters\Orders\Resources\OrderResource;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Pages\ListQuotations as BaseListOrders;
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
            'archived' => PresetView::make(__('sales::filament/clusters/orders/resources/order/pages/list-orders.tabs.archived'))
                ->icon('heroicon-s-archive-box')
                ->favorite()
                ->modifyQueryUsing(fn ($query) => $query->onlyTrashed()),
        ];
    }
}
