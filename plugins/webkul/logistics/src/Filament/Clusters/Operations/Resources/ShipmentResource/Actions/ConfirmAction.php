<?php

namespace Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\Actions;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Webkul\Logistics\Enums\ShipmentState;
use Webkul\Logistics\Models\Shipment;
use Webkul\Logistics\Services\ShipmentWorkflow;

class ConfirmAction extends Action
{
    protected string $lang = 'logistics::filament/clusters/operations/resources/shipment.actions.confirm';

    public static function getDefaultName(): ?string
    {
        return 'confirmShipment';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__($this->lang.'.label'))
            ->icon('heroicon-o-check-circle')
            ->color('primary')
            ->requiresConfirmation()
            ->modalHeading(__($this->lang.'.heading'))
            // Filament actions carry no policy check of their own, so the ability is
            // checked here; ShipmentWorkflow authorises again server-side.
            ->visible(fn (Shipment $record): bool => $record->state === ShipmentState::DRAFT
                && (Auth::user()?->can('confirm', $record) ?? false))
            ->action(function (Shipment $record): void {
                app(ShipmentWorkflow::class)->confirm($record);

                Notification::make()
                    ->success()
                    ->title(__($this->lang.'.notification'))
                    ->send();
            });
    }
}
