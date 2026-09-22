<?php

namespace Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
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

        $this->label(fn (?Shipment $record): string => __($this->lang.($record?->state === ShipmentState::FAILED_DELIVERY ? '.resolve-label' : '.label')))
            ->icon(fn (?Shipment $record): string => $record?->state === ShipmentState::FAILED_DELIVERY ? 'heroicon-o-arrow-path' : 'heroicon-o-exclamation-triangle')
            ->color(fn (?Shipment $record): string => $record?->state === ShipmentState::FAILED_DELIVERY ? 'warning' : 'danger')
            ->schema(fn (Shipment $record): array => $record->state === ShipmentState::FAILED_DELIVERY
                ? [
                    Select::make('resolution')
                        ->label(__($this->lang.'.resolution'))
                        ->options([
                            'retry'  => __($this->lang.'.resolutions.retry'),
                            'return' => __($this->lang.'.resolutions.return'),
                        ])
                        ->required()
                        ->native(false),
                ]
                : [
                    Textarea::make('reason')
                        ->label(__($this->lang.'.reason'))
                        ->required()
                        ->maxLength(2000)
                        ->rows(3),
                ])
            ->visible(fn (Shipment $record): bool => in_array($record->state, [ShipmentState::OUT_FOR_DELIVERY, ShipmentState::FAILED_DELIVERY], true)
                && (Auth::user()?->can('markDelivered', $record) ?? false))
            ->action(function (Shipment $record, array $data): void {
                $service = app(DeliveryService::class);

                if ($record->state === ShipmentState::FAILED_DELIVERY) {
                    $resolution = $data['resolution'] ?? null;

                    if ($resolution === 'retry') {
                        $service->retry($record);
                        $notification = $this->lang.'.retry-notification';
                    } elseif ($resolution === 'return') {
                        $service->return($record);
                        $notification = $this->lang.'.return-notification';
                    } else {
                        throw ValidationException::withMessages([
                            'resolution' => __('logistics::delivery.validation.resolution-required'),
                        ]);
                    }

                    Notification::make()->success()->title(__($notification))->send();

                    return;
                }

                $service->fail($record, (string) ($data['reason'] ?? ''));

                Notification::make()->success()->title(__($this->lang.'.notification'))->send();
            });
    }
}
