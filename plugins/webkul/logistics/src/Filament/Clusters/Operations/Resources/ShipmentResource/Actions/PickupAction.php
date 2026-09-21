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

        $this->label(__($this->lang.'.label'))
            ->icon('heroicon-o-truck')
            ->color('primary')
            ->requiresConfirmation()
            ->visible(fn (Shipment $record): bool => $record->state === ShipmentState::AWAITING_PICKUP
                && (Auth::user()?->can('markPickedUp', $record) ?? false))
            ->action(function (Shipment $record): void {
                app(DeliveryService::class)->markPickedUp($record);

                Notification::make()->success()->title(__($this->lang.'.notification'))->send();
            });
    }
}
