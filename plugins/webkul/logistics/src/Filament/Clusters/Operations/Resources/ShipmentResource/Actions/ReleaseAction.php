<?php

namespace Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\Actions;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Webkul\Logistics\Enums\ShipmentState;
use Webkul\Logistics\Models\Shipment;
use Webkul\Logistics\Services\ShipmentWorkflow;

class ReleaseAction extends Action
{
    protected string $lang = 'logistics::filament/clusters/operations/resources/shipment.actions.release';

    public static function getDefaultName(): ?string
    {
        return 'releaseShipment';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__($this->lang.'.label'))
            ->icon('heroicon-o-play-circle')
            ->color('primary')
            ->requiresConfirmation()
            ->modalHeading(__($this->lang.'.heading'))
            ->modalDescription(__($this->lang.'.description'))
            ->visible(fn (Shipment $record): bool => $record->state === ShipmentState::ON_HOLD
                && (Auth::user()?->can('update', $record) ?? false))
            ->action(function (Shipment $record): void {
                app(ShipmentWorkflow::class)->release($record);

                Notification::make()
                    ->success()
                    ->title(__($this->lang.'.notification'))
                    ->send();
            });
    }
}
