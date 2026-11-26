<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Actions;

use Closure;
use Exception;
use Filament\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Facades\FilamentView;
use Illuminate\Support\Arr;
use Livewire\Component;
use Webkul\Sale\Enums\AdvancedPayment;
use Webkul\Sale\Enums\InvoiceStatus;
use Webkul\Sale\Facades\SaleOrder as SalesFacade;
use Webkul\Sale\Filament\Clusters\Orders\Resources\OrderInvoiceResource;
use Webkul\Sale\Models\Order;

class CreateInvoiceAction extends Action
{
    protected bool|Closure $hasDatabaseTransactions = true;

    public static function getDefaultName(): ?string
    {
        return 'orders.sales.create-invoice';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->color(function (Order $record): string {
                if ($record->invoice_status != InvoiceStatus::TO_INVOICE) {
                    return 'gray';
                }

                return 'primary';
            })
            ->label(__('sales::filament/clusters/orders/resources/quotation/actions/create-invoice.title'))
            ->schema([
                Radio::make('advance_payment_method')
                    ->inline(false)
                    ->label(__('sales::filament/clusters/orders/resources/quotation/actions/create-invoice.form.fields.create-invoice'))
                    ->options(function () {
                        $options = AdvancedPayment::options();

                        return Arr::only($options, [
                            AdvancedPayment::DELIVERED->value,
                        ]);
                    })
                    ->default(AdvancedPayment::DELIVERED->value)
                    ->live(),
                Group::make()
                    ->columns(2)
                    ->schema([
                        TextInput::make('amount')
                            ->visible(fn (Get $get) => $get('advance_payment_method') == AdvancedPayment::PERCENTAGE->value)
                            ->rules('required', 'numeric')
                            ->default(0.00)
                            ->suffix('%'),
                        TextInput::make('amount')
                            ->visible(fn (Get $get) => $get('advance_payment_method') == AdvancedPayment::FIXED->value)
                            ->rules('required', 'numeric')
                            ->default(0.00)
                            ->prefix(fn ($record) => $record->currency->symbol),
                    ]),
            ])
            ->hidden(fn ($record) => $record->invoice_status != InvoiceStatus::TO_INVOICE)
            ->action(function (Order $record, $data, Component $livewire) {
                if ($record->qty_to_invoice == 0) {
                    Notification::make()
                        ->title(__('sales::filament/clusters/orders/resources/quotation/actions/create-invoice.notification.no-invoiceable-lines.title'))
                        ->body(__('sales::filament/clusters/orders/resources/quotation/actions/create-invoice.notification.no-invoiceable-lines.body'))
                        ->warning()
                        ->send();

                    return;
                }

                try {
                    SalesFacade::createInvoice($record, $data);
                } catch (Exception $e) {
                    Notification::make()
                        ->danger()
                        ->body($e->getMessage())
                        ->send();

                    $this->halt(shouldRollBackDatabaseTransaction: true);

                    return;
                }

                Notification::make()
                    ->title(__('sales::filament/clusters/orders/resources/quotation/actions/create-invoice.notification.invoice-created.title'))
                    ->body(__('sales::filament/clusters/orders/resources/quotation/actions/create-invoice.notification.invoice-created.body'))
                    ->success()
                    ->send();

                // TRACE365 (user, 2026-11-25): the user stayed on the order
                // and had to find the new invoice to confirm it. A regular
                // run makes exactly one invoice, so open it.
                static::openLatestInvoice($record, $livewire);
            });
    }

    /**
     * Opens the order's newest invoice - the one a regular invoicing run has
     * just attached - on its edit page under the order, where the Invoices
     * tab of the order links too. Read through the scoped relation: if the
     * user may not see it, they stay where they are rather than land on a 404.
     */
    public static function openLatestInvoice(Order $order, Component $livewire): void
    {
        $invoice = $order->invoices()->orderByDesc('accounts_account_moves.id')->first();

        if (! $invoice) {
            return;
        }

        $livewire->redirect(
            OrderInvoiceResource::getUrl('edit', ['sales_order' => $order, 'record' => $invoice]),
            navigate: FilamentView::hasSpaMode(),
        );
    }
}
