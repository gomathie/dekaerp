<?php

namespace Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Webkul\Logistics\Enums\ShipmentState;
use Webkul\Logistics\Models\Shipment;
use Webkul\Logistics\Services\DeliveryService;

class FailDeliveryAction extends Action
{
    protected string $lang = 'logistics::delivery.actions.fail';

    public static function getDefaultName(): ?string
    {
        return 'failDelivery';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__($this->lang.'.label'))
            ->icon('heroicon-o-exclamation-triangle')
            ->color('danger')
            ->schema([
                Textarea::make('reason')
                    ->label(__($this->lang.'.reason'))
                    ->required()
                    ->maxLength(2000)
                    ->rows(3),
            ])
            ->visible(fn (Shipment $record): bool => $record->state === ShipmentState::OUT_FOR_DELIVERY
                && (Auth::user()?->can('markDelivered', $record) ?? false))
            ->action(function (Shipment $record, array $data): void {
                app(DeliveryService::class)->fail($record, $data['reason']);

                Notification::make()->success()->title(__($this->lang.'.notification'))->send();
            });
    }
}
