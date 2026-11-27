<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\OrderResource\Pages;

use Filament\Actions\Action;
use Illuminate\Support\Facades\Gate;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Filament\Clusters\Orders\Resources\OrderResource;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Pages\ViewQuotation as BaseViewOrders;
use Webkul\Sale\Services\OrderFromInvoice;
use Webkul\Sale\Support\SalesAccess;

class ViewOrder extends BaseViewOrders
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('deliver')
                ->label(__('sales::filament/contributions/invoice.delivery.mark-delivered'))
                ->icon('heroicon-o-truck')
                // Opens the delivery. Validating it stays on that page, so
                // this does not move stock a second way.
                ->visible(function (): bool {
                    $order = $this->getRecord();

                    return $order->state === OrderState::SALE
                        && SalesAccess::enabledForCompany((int) $order->company_id)
                        && app(OrderFromInvoice::class)->pendingDeliveries($order) > 0
                        && Gate::allows('update', $order);
                })
                ->url(fn (): string => OrderResource::getUrl('operations', ['record' => $this->getRecord()])),
            ...parent::getHeaderActions(),
        ];
    }
}
