<?php

namespace Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Webkul\Logistics\Enums\ShipmentState;
use Webkul\Logistics\Models\Shipment;
use Webkul\Logistics\Services\ShipmentWorkflow;

class CancelAction extends Action
{
    protected string $lang = 'logistics::filament/clusters/operations/resources/shipment.actions.cancel';

    public static function getDefaultName(): ?string
    {
        return 'logistics.shipment.cancel';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__($this->lang.'.label'))
            ->icon('heroicon-o-x-circle')
            ->color('danger')
            ->modalHeading(__($this->lang.'.heading'))
            ->modalDescription(__($this->lang.'.description'))
            ->schema([
                Textarea::make('notes')
                    ->label(__($this->lang.'.reason'))
                    ->rows(3),
            ])
            ->visible(fn (Shipment $record): bool => $record->state->canTransitionTo(ShipmentState::CANCELLED))
            ->authorize(fn (Shipment $record): bool => auth()->user()?->can('cancel', $record) ?? false)
            ->action(function (Shipment $record, array $data): void {
                app(ShipmentWorkflow::class)->cancel($record, ['notes' => $data['notes'] ?? null]);

                Notification::make()
                    ->success()
                    ->title(__($this->lang.'.notification'))
                    ->send();
            });
    }
}
