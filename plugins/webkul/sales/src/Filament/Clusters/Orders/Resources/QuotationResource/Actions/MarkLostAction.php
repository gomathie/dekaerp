<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Actions;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Enums\QuotationOutcome;
use Webkul\Sale\Models\Order;
use Webkul\Sale\Services\QuotationOutcomeMarker;

class MarkLostAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'orders.sales.mark-lost';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('sales::filament/clusters/orders/resources/quotation/actions/outcome.lost'))
            ->color('danger')
            ->visible(function (Order $record): bool {
                if ($record->outcome === QuotationOutcome::Lost) {
                    return false;
                }

                if ($record->state === OrderState::CANCEL) {
                    return true;
                }

                return in_array($record->state, [OrderState::DRAFT, OrderState::SENT], true)
                    && $record->validity_date !== null
                    && $record->validity_date->lt(today());
            })
            ->action(function (Order $record): void {
                app(QuotationOutcomeMarker::class)->markLost($record);

                Notification::make()
                    ->success()
                    ->title(__('sales::filament/clusters/orders/resources/quotation/actions/outcome.lost-saved'))
                    ->send();
            });
    }
}
