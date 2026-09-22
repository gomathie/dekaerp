<?php

namespace Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\Actions;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Webkul\Logistics\Exceptions\NothingToInvoice;
use Webkul\Logistics\Models\Shipment;
use Webkul\Logistics\Services\ShipmentInvoicer;

class CreateInvoiceAction extends Action
{
    protected string $lang = 'logistics::invoicing.actions.create-invoice';

    public static function getDefaultName(): ?string
    {
        return 'createShipmentInvoice';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__($this->lang.'.label'))
            ->icon('heroicon-o-document-currency-dollar')
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading(__($this->lang.'.heading'))
            ->modalDescription(__($this->lang.'.description'))
            // Filament actions carry no policy check of their own, and
            // authorize() takes ability names rather than closures, so the
            // ability is checked here; ShipmentInvoicer authorises again and
            // re-reads the shipment under the company scope.
            ->visible(fn (Shipment $record): bool => Auth::user()?->can('createInvoice', $record) ?? false)
            ->action(function (Shipment $record): void {
                try {
                    $invoice = app(ShipmentInvoicer::class)->createInvoice($record);
                } catch (NothingToInvoice $e) {
                    // Expected, not exceptional: the user asked to invoice a
                    // shipment whose charges are already invoiced. Say so
                    // plainly instead of surfacing an error page.
                    Notification::make()
                        ->warning()
                        ->title(__($this->lang.'.nothing-to-invoice'))
                        ->body($e->getMessage())
                        ->send();

                    return;
                }

                Notification::make()
                    ->success()
                    ->title(__($this->lang.'.notification'))
                    ->body(__($this->lang.'.notification-body', [
                        'invoice' => $invoice->name ?? '#'.$invoice->getKey(),
                    ]))
                    ->send();
            });
    }
}
