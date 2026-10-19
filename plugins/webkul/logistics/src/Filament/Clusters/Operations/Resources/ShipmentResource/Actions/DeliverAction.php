<?php

namespace Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Webkul\Logistics\Enums\ShipmentState;
use Webkul\Logistics\Enums\StopState;
use Webkul\Logistics\Enums\StopType;
use Webkul\Logistics\Models\CompanySetting;
use Webkul\Logistics\Models\Shipment;
use Webkul\Logistics\Services\DeliveryService;
use Webkul\Logistics\Services\PodData;

class DeliverAction extends Action
{
    protected string $lang = 'logistics::delivery.actions.deliver';

    public static function getDefaultName(): ?string
    {
        return 'deliverShipment';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(fn (?Shipment $record): string => __($this->lang.($record?->state === ShipmentState::IN_TRANSIT ? '.out-for-delivery-label' : '.label')))
            ->icon(fn (?Shipment $record): string => $record?->state === ShipmentState::IN_TRANSIT ? 'heroicon-o-map-pin' : 'heroicon-o-check-badge')
            ->color(fn (?Shipment $record): string => $record?->state === ShipmentState::IN_TRANSIT ? 'warning' : 'success')
            ->requiresConfirmation(fn (Shipment $record): bool => $record->state === ShipmentState::IN_TRANSIT)
            ->schema(function (Shipment $record): array {
                if ($record->state === ShipmentState::IN_TRANSIT) {
                    return [];
                }

                $settings = CompanySetting::forCompany((int) $record->company_id);
                $requiresPod = (bool) $settings->require_pod_for_delivery;
                $canCapturePod = Auth::user()?->can('capturePod', $record) ?? false;
                $capturesPod = fn (Get $get): bool => $requiresPod || (bool) $get('capture_pod');

                return [
                    Toggle::make('capture_pod')
                        ->label(__($this->lang.'.fields.capture-pod'))
                        ->default($requiresPod)
                        ->live()
                        ->visible(! $requiresPod && $canCapturePod),
                    Select::make('stop_id')
                        ->label(__($this->lang.'.fields.stop'))
                        ->options(fn (): array => $record->stops()
                            ->where('type', StopType::DELIVERY)
                            ->whereNotIn('state', [StopState::DEPARTED->value, StopState::SKIPPED->value])
                            ->get()
                            ->mapWithKeys(fn ($stop): array => [$stop->id => '#'.$stop->sequence.' '.($stop->address?->name ?? $stop->contact_name)])
                            ->all())
                        ->visible(fn (Get $get): bool => $capturesPod($get))
                        ->required(fn (Get $get): bool => $capturesPod($get) && $record->stops()
                            ->where('type', StopType::DELIVERY)
                            ->whereNotIn('state', [StopState::DEPARTED->value, StopState::SKIPPED->value])
                            ->count() > 1),
                    TextInput::make('recipient_name')
                        ->label(__($this->lang.'.fields.recipient-name'))
                        ->visible(fn (Get $get): bool => $capturesPod($get))
                        ->required(fn (Get $get): bool => $capturesPod($get))
                        ->maxLength(255),
                    DateTimePicker::make('received_at')
                        ->label(__($this->lang.'.fields.received-at'))
                        ->default(now())
                        ->visible(fn (Get $get): bool => $capturesPod($get))
                        ->required(fn (Get $get): bool => $capturesPod($get))
                        ->seconds(false),
                    TextInput::make('reference')
                        ->label(__($this->lang.'.fields.reference'))
                        ->visible(fn (Get $get): bool => $capturesPod($get))
                        ->maxLength(255),
                    Textarea::make('notes')
                        ->label(__($this->lang.'.fields.notes'))
                        ->visible(fn (Get $get): bool => $capturesPod($get))
                        ->rows(3),
                    FileUpload::make('photo')
                        ->label(__($this->lang.'.fields.photo'))
                        ->image()
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                        ->maxSize(5120)
                        ->storeFiles(false)
                        ->visible(fn (Get $get): bool => $capturesPod($get))
                        ->required(fn (Get $get): bool => $capturesPod($get) && (bool) $settings->require_pod_photo),
                    FileUpload::make('signature')
                        ->label(__($this->lang.'.fields.signature'))
                        ->image()
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                        ->maxSize(5120)
                        ->storeFiles(false)
                        ->visible(fn (Get $get): bool => $capturesPod($get)),
                ];
            })
            ->visible(function (Shipment $record): bool {
                if (! in_array($record->state, [ShipmentState::IN_TRANSIT, ShipmentState::OUT_FOR_DELIVERY], true)) {
                    return false;
                }

                if (! (Auth::user()?->can('markDelivered', $record) ?? false)) {
                    return false;
                }

                return $record->state === ShipmentState::IN_TRANSIT
                    || ! CompanySetting::forCompany((int) $record->company_id)->require_pod_for_delivery
                    || (Auth::user()?->can('capturePod', $record) ?? false);
            })
            ->action(function (Shipment $record, array $data): void {
                $service = app(DeliveryService::class);

                if ($record->state === ShipmentState::IN_TRANSIT) {
                    $service->markOutForDelivery($record);

                    Notification::make()
                        ->success()
                        ->title(__($this->lang.'.out-for-delivery-notification'))
                        ->send();

                    return;
                }

                $settings = CompanySetting::forCompany((int) $record->company_id);
                $capturesPod = (bool) $settings->require_pod_for_delivery || (bool) ($data['capture_pod'] ?? false);

                $podData = $capturesPod
                    ? new PodData(
                        recipientName: (string) ($data['recipient_name'] ?? ''),
                        receivedAt: Carbon::parse($data['received_at'] ?? null),
                        stopId: isset($data['stop_id']) ? (int) $data['stop_id'] : null,
                        reference: $data['reference'] ?? null,
                        notes: $data['notes'] ?? null,
                        photo: $data['photo'] ?? null,
                        signature: $data['signature'] ?? null,
                    )
                    : null;

                $shipment = $service->deliver($record, $podData);

                Notification::make()
                    ->success()
                    ->title(__($this->lang.($shipment->state === ShipmentState::DELIVERED ? '.notification' : '.stop-notification')))
                    ->send();
            });
    }
}
