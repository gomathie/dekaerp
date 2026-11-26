<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Actions;

use Closure;
use Exception;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Facades\FilamentView;
use Livewire\Component;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Facades\SaleOrder;
use Webkul\Sale\Filament\Clusters\Orders\Resources\OrderResource;
use Webkul\Sale\Models\Order;

class ConfirmAction extends Action
{
    protected bool|Closure $hasDatabaseTransactions = true;

    public static function getDefaultName(): ?string
    {
        return 'orders.sales.confirm';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->color(fn (): string => $this->getRecord()->state === OrderState::DRAFT ? 'gray' : 'primary')
            ->label(__('sales::filament/clusters/orders/resources/quotation/actions/confirm.title'))
            ->hidden(fn (Order $record, Component $livewire): bool => in_array($record->state, [OrderState::SALE, OrderState::CANCEL])
                || ! static::mayConfirm($record, $livewire))
            ->action(function ($record, $livewire) {
                try {
                    $record = SaleOrder::confirmSaleOrder($record);
                } catch (Exception $e) {
                    Notification::make()
                        ->danger()
                        ->title(__('sales::filament/clusters/orders/resources/quotation/actions/confirm.notification.error.title'))
                        ->body($e->getMessage())
                        ->send();

                    $this->halt(shouldRollBackDatabaseTransaction: true);

                    return;
                }

                $livewire->refreshFormData(['state']);

                $livewire->redirect(OrderResource::getUrl('edit', ['record' => $record]), navigate: FilamentView::hasSpaMode());

                Notification::make()
                    ->success()
                    ->title(__('sales::filament/clusters/orders/resources/quotation/actions/confirm.notification.confirmed.title'))
                    ->body(__('sales::filament/clusters/orders/resources/quotation/actions/confirm.notification.confirmed.body'))
                    ->send();
            });
    }

    /**
     * Confirming changes the quotation, so it needs the right to edit it
     * (user, 2026-11-26). The edit page already required that, but the view
     * page offered Confirm to anyone who could open the quotation. A hidden
     * action cannot be called, so this also refuses a crafted request.
     * Convert to invoice uses the same rule for its first half.
     */
    public static function mayConfirm(Order $record, Component $livewire): bool
    {
        return $livewire::getResource()::canEdit($record);
    }
}
