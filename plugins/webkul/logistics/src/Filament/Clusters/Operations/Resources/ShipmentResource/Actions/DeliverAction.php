<?php

namespace Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Webkul\Logistics\Enums\ShipmentState;
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

        $this->label(__($this->lang.'.label'))
            ->icon('heroicon-o-check-badge')
            ->color('success')
            ->schema([
                Select::make('stop_id')
                    ->label(__($this->lang.'.fields.stop'))
                    ->options(fn (Shipment $record): array => $record->stops()
                        ->where('type', StopType::DELIVERY)
                        ->get()
                        ->mapWithKeys(fn ($stop): array => [$stop->id => '#'.$stop->sequence.' '.($stop->address?->name ?? $stop->contact_name)])
                        ->all())
                    ->required(fn (Shipment $record): bool => $record->stops()->where('type', StopType::DELIVERY)->count() > 1),
                TextInput::make('recipient_name')
                    ->label(__($this->lang.'.fields.recipient-name'))
                    ->required()
                    ->maxLength(255),
                DateTimePicker::make('received_at')
                    ->label(__($this->lang.'.fields.received-at'))
                    ->default(now())
                    ->required()
                    ->seconds(false),
                TextInput::make('reference')
                    ->label(__($this->lang.'.fields.reference'))
                    ->maxLength(255),
                Textarea::make('notes')
                    ->label(__($this->lang.'.fields.notes'))
                    ->rows(3),
                FileUpload::make('photo')
                    ->label(__($this->lang.'.fields.photo'))
                    ->image()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->maxSize(5120)
                    ->storeFiles(false)
                    ->required(fn (Shipment $record): bool => CompanySetting::forCompany((int) $record->company_id)->require_pod_photo),
                FileUpload::make('signature')
                    ->label(__($this->lang.'.fields.signature'))
                    ->image()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->maxSize(5120)
                    ->storeFiles(false),
            ])
            ->visible(fn (Shipment $record): bool => $record->state === ShipmentState::OUT_FOR_DELIVERY
                && (Auth::user()?->can('markDelivered', $record) ?? false)
                && (Auth::user()?->can('capturePod', $record) ?? false))
            ->action(function (Shipment $record, array $data): void {
                $service = app(DeliveryService::class);

                $service->deliver($record, new PodData(
                    recipientName: $data['recipient_name'],
                    receivedAt: Carbon::parse($data['received_at']),
                    stopId: isset($data['stop_id']) ? (int) $data['stop_id'] : null,
                    reference: $data['reference'] ?? null,
                    notes: $data['notes'] ?? null,
                    photo: $data['photo'] ?? null,
                    signature: $data['signature'] ?? null,
                ));

                Notification::make()->success()->title(__($this->lang.'.notification'))->send();
            });
    }
}
