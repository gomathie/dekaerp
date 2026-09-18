<?php

namespace Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Webkul\Logistics\Enums\ShipmentState;
use Webkul\Logistics\Models\Shipment;
use Webkul\Logistics\Services\ShipmentWorkflow;

class HoldAction extends Action
{
    protected string $lang = 'logistics::filament/clusters/operations/resources/shipment.actions.hold';

    public static function getDefaultName(): ?string
    {
        return 'logistics.shipment.hold';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__($this->lang.'.label'))
            ->icon('heroicon-o-pause-circle')
            ->color('warning')
            ->modalHeading(__($this->lang.'.heading'))
            ->schema([
                Textarea::make('notes')
                    ->label(__($this->lang.'.reason'))
                    ->required()
                    ->rows(3),
            ])
            ->visible(fn (Shipment $record): bool => $record->state->canTransitionTo(ShipmentState::ON_HOLD))
            ->authorize(fn (Shipment $record): bool => auth()->user()?->can('update', $record) ?? false)
            ->action(function (Shipment $record, array $data): void {
                app(ShipmentWorkflow::class)->hold($record, ['notes' => $data['notes']]);

                Notification::make()
                    ->success()
                    ->title(__($this->lang.'.notification'))
                    ->send();
            });
    }
}
