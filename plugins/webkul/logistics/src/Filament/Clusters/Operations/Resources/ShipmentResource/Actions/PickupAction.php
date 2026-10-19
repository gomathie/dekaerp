<?php

namespace Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\Actions;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Webkul\Logistics\Enums\ShipmentState;
use Webkul\Logistics\Models\Shipment;
use Webkul\Logistics\Services\DeliveryService;

class PickupAction extends Action
{
    protected string $lang = 'logistics::delivery.actions.pickup';

    public static function getDefaultName(): ?string
    {
        return 'markPickedUp';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(fn (?Shipment $record): string => __($this->lang.($record?->state === ShipmentState::PICKED_UP ? '.transit-label' : '.label')))
            ->icon(fn (?Shipment $record): string => $record?->state === ShipmentState::PICKED_UP ? 'heroicon-o-play' : 'heroicon-o-truck')
            ->color('primary')
            ->requiresConfirmation()
            ->visible(fn (Shipment $record): bool => in_array($record->state, [ShipmentState::AWAITING_PICKUP, ShipmentState::PICKED_UP], true)
                && (Auth::user()?->can('markPickedUp', $record) ?? false))
            ->action(function (Shipment $record): void {
                $startsTransit = $record->state === ShipmentState::PICKED_UP;
                $service = app(DeliveryService::class);

                if ($startsTransit) {
                    $service->markInTransit($record);
                } else {
                    $service->markPickedUp($record);
                }

                Notification::make()
                    ->success()
                    ->title(__($this->lang.($startsTransit ? '.transit-notification' : '.notification')))
                    ->send();
            });
    }
}
