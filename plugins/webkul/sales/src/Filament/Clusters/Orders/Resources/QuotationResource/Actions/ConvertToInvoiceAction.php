<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Actions;

use Closure;
use Exception;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Facades\FilamentView;
use Livewire\Component;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\InvoiceResource;
use Webkul\Sale\Enums\AdvancedPayment;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Facades\SaleOrder;
use Webkul\Sale\Filament\Clusters\Orders\Resources\OrderResource;
use Webkul\Sale\Models\Order;

/**
 * "Convert to invoice" (TRACE365, user 2026-11-25: quotation to confirmed
 * invoice took about ten clicks on three screens). One click confirms the
 * quotation and creates its regular invoice, through exactly the paths the
 * Confirm and Create invoice buttons use, then opens the draft invoice so it
 * can still be changed before it is confirmed. Down payments stay on
 * Create invoice.
 */
class ConvertToInvoiceAction extends Action
{
    protected bool|Closure $hasDatabaseTransactions = true;

    public static function getDefaultName(): ?string
    {
        return 'orders.sales.convert-to-invoice';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->color('primary')
            ->label(__('sales::filament/clusters/orders/resources/quotation/actions/convert-to-invoice.title'))
            // Quotations only: a confirmed order already has Create invoice,
            // which opens the new invoice too.
            ->hidden(fn (Order $record, Component $livewire): bool => in_array($record->state, [OrderState::SALE, OrderState::CANCEL])
                || ! static::mayConvert($record, $livewire))
            ->action(function (Order $record, Component $livewire): void {
                try {
                    $record = SaleOrder::confirmSaleOrder($record)->refresh();

                    // Products invoiced on delivered quantities have nothing to
                    // invoice until the goods leave. The confirmation stands
                    // (it is what Confirm would have done) and no empty invoice
                    // is made; the user is told (Confirm's and Create invoice's
                    // own messages) and taken to the order.
                    if ($record->qty_to_invoice == 0) {
                        Notification::make()
                            ->success()
                            ->title(__('sales::filament/clusters/orders/resources/quotation/actions/confirm.notification.confirmed.title'))
                            ->body(__('sales::filament/clusters/orders/resources/quotation/actions/confirm.notification.confirmed.body'))
                            ->send();

                        Notification::make()
                            ->warning()
                            ->title(__('sales::filament/clusters/orders/resources/quotation/actions/create-invoice.notification.no-invoiceable-lines.title'))
                            ->body(__('sales::filament/clusters/orders/resources/quotation/actions/create-invoice.notification.no-invoiceable-lines.body'))
                            ->persistent()
                            ->send();

                        $livewire->redirect(OrderResource::getUrl('edit', ['record' => $record]), navigate: FilamentView::hasSpaMode());

                        return;
                    }

                    SaleOrder::createInvoice($record, [
                        'advance_payment_method' => AdvancedPayment::DELIVERED->value,
                    ]);
                } catch (Exception $e) {
                    // Rolls back the confirmation too: never a confirmed order
                    // whose invoice silently failed.
                    Notification::make()
                        ->danger()
                        ->title(__('sales::filament/clusters/orders/resources/quotation/actions/confirm.notification.error.title'))
                        ->body($e->getMessage())
                        ->send();

                    $this->halt(shouldRollBackDatabaseTransaction: true);

                    return;
                }

                Notification::make()
                    ->success()
                    ->title(__('sales::filament/clusters/orders/resources/quotation/actions/convert-to-invoice.notification.converted.title'))
                    ->body(__('sales::filament/clusters/orders/resources/quotation/actions/convert-to-invoice.notification.converted.body'))
                    ->send();

                CreateInvoiceAction::openLatestInvoice($record, $livewire);
            });
    }

    /**
     * Both halves of the click must be allowed: confirming (Confirm's own
     * rule) and creating a customer invoice of the Invoices module (its
     * permission and the company's module switch). Hidden actions cannot be
     * called, so this also refuses a crafted request.
     */
    protected static function mayConvert(Order $record, Component $livewire): bool
    {
        return ConfirmAction::mayConfirm($record, $livewire)
            && InvoiceResource::canCreate();
    }
}
