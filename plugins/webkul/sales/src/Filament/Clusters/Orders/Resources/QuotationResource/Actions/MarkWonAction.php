<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Actions;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Enums\QuotationOutcome;
use Webkul\Sale\Models\Order;
use Webkul\Sale\Services\QuotationOutcomeMarker;

class MarkWonAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'orders.sales.mark-won';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('sales::filament/clusters/orders/resources/quotation/actions/outcome.won'))
            ->color('success')
            ->visible(fn (Order $record): bool => $record->state === OrderState::SALE
                && $record->outcome !== QuotationOutcome::Won)
            ->action(function (Order $record): void {
                app(QuotationOutcomeMarker::class)->markWon($record);

                Notification::make()
                    ->success()
                    ->title(__('sales::filament/clusters/orders/resources/quotation/actions/outcome.won-saved'))
                    ->send();
            });
    }
}
