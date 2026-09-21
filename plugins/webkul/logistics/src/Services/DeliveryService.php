<?php

namespace Webkul\Logistics\Services;

use Carbon\CarbonInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Throwable;
use Webkul\Logistics\Enums\ProofCaptureChannel;
use Webkul\Logistics\Enums\ShipmentEventType;
use Webkul\Logistics\Enums\ShipmentState;
use Webkul\Logistics\Enums\StopState;
use Webkul\Logistics\Enums\StopType;
use Webkul\Logistics\Models\CompanySetting;
use Webkul\Logistics\Models\DeliveryProof;
use Webkul\Logistics\Models\Shipment;
use Webkul\Logistics\Models\Stop;
use Webkul\Logistics\Support\LogisticsAccess;
use Webkul\Support\Services\CompanyContext;

final readonly class PodData
{
    public function __construct(
        public string $recipientName,
        public CarbonInterface $receivedAt,
        public ?int $stopId = null,
        public ?string $reference = null,
        public ?string $notes = null,
        public ?UploadedFile $photo = null,
        public ?UploadedFile $signature = null,
        public ProofCaptureChannel $capturedVia = ProofCaptureChannel::OFFICE,
        public ?float $latitude = null,
        public ?float $longitude = null,
        public ?float $accuracyM = null,
    ) {}
}

class DeliveryService
{
    public function __construct(protected ShipmentWorkflow $workflow) {}

    public function markPickedUp(Shipment $shipment): Shipment
    {
        return $this->transitionWithStops(
            $shipment,
            ShipmentState::PICKED_UP,
            'markPickedUp',
            function (Shipment $shipment, CarbonInterface $occurredAt): void {
                $this->stops($shipment, StopType::PICKUP)->each(function (Stop $stop) use ($occurredAt): void {
                    $stop->forceFill([
                        'state'             => StopState::ARRIVED,
                        'actual_arrival_at' => $stop->actual_arrival_at ?? $occurredAt,
                    ])->save();
                });
            },
        );
    }

    public function markInTransit(Shipment $shipment): Shipment
    {
        return $this->transitionWithStops(
            $shipment,
            ShipmentState::IN_TRANSIT,
            'markPickedUp',
            function (Shipment $shipment, CarbonInterface $occurredAt): void {
                $this->stops($shipment, StopType::PICKUP)->each(function (Stop $stop) use ($occurredAt): void {
                    $stop->forceFill([
                        'state'               => StopState::DEPARTED,
                        'actual_arrival_at'   => $stop->actual_arrival_at ?? $occurredAt,
                        'actual_departure_at' => $stop->actual_departure_at ?? $occurredAt,
                    ])->save();
                });
            },
        );
    }

    public function markOutForDelivery(Shipment $shipment): Shipment
    {
        return $this->transitionWithStops($shipment, ShipmentState::OUT_FOR_DELIVERY, 'markDelivered');
    }

    public function deliver(Shipment $shipment, ?PodData $podData = null): Shipment
    {
        $this->guard($shipment, 'markDelivered');

        $settings = CompanySetting::forCompany((int) $shipment->company_id);
        $stop = $this->deliveryStop($shipment, $podData?->stopId);

        if ($settings->require_pod_for_delivery && $podData === null) {
            throw ValidationException::withMessages([
                'pod' => __('logistics::delivery.validation.pod-required'),
            ]);
        }

        if ($podData === null) {
            return $this->completeDelivery($shipment, $stop, null, []);
        }

        Gate::authorize('capturePod', $shipment);
        $this->validatePod($podData, $settings->require_pod_photo);

        $storedPaths = $this->storePodFiles($shipment, $podData);

        try {
            return $this->completeDelivery($shipment, $stop, $podData, $storedPaths);
        } catch (Throwable $exception) {
            $this->deletePodFiles($shipment, $storedPaths);

            throw $exception;
        }
    }

    public function fail(Shipment $shipment, string $reason): Shipment
    {
        Validator::make(['reason' => $reason], [
            'reason' => ['required', 'string', 'max:2000'],
        ])->validate();

        return $this->transitionWithStops(
            $shipment,
            ShipmentState::FAILED_DELIVERY,
            'markDelivered',
            function (Shipment $shipment, CarbonInterface $occurredAt): void {
                $this->stops($shipment, StopType::DELIVERY)->each(function (Stop $stop) use ($occurredAt): void {
                    $stop->forceFill([
                        'state'             => StopState::ARRIVED,
                        'actual_arrival_at' => $stop->actual_arrival_at ?? $occurredAt,
                    ])->save();
                });
            },
            ['notes' => $reason],
        );
    }

    public function retry(Shipment $shipment): Shipment
    {
        return DB::transaction(function () use ($shipment): Shipment {
            $result = $this->transitionWithStops(
                $shipment,
                ShipmentState::OUT_FOR_DELIVERY,
                'markDelivered',
                function (Shipment $shipment): void {
                    $this->stops($shipment, StopType::DELIVERY)->each(function (Stop $stop): void {
                        $stop->forceFill([
                            'state'               => StopState::PENDING,
                            'actual_arrival_at'   => null,
                            'actual_departure_at' => null,
                        ])->save();
                    });
                },
            );

            $this->workflow->recordEvent($result, ShipmentEventType::DELIVERY_RETRIED);

            return $result;
        });
    }

    public function return(Shipment $shipment): Shipment
    {
        return $this->transitionWithStops(
            $shipment,
            ShipmentState::RETURNED,
            'markDelivered',
            function (Shipment $shipment, CarbonInterface $occurredAt): void {
                $this->stops($shipment, StopType::DELIVERY)->each(function (Stop $stop) use ($occurredAt): void {
                    $stop->forceFill([
                        'state'               => StopState::SKIPPED,
                        'actual_departure_at' => $stop->actual_departure_at ?? $occurredAt,
                    ])->save();
                });
            },
        );
    }

    protected function completeDelivery(Shipment $shipment, ?Stop $stop, ?PodData $podData, array $storedPaths): Shipment
    {
        return DB::transaction(function () use ($shipment, $stop, $podData, $storedPaths): Shipment {
            $occurredAt = $podData?->receivedAt ?? now();

            if ($stop) {
                $stop->forceFill([
                    'state'               => StopState::DEPARTED,
                    'actual_arrival_at'   => $stop->actual_arrival_at ?? $occurredAt,
                    'actual_departure_at' => $stop->actual_departure_at ?? $occurredAt,
                ])->save();
            }

            if ($podData) {
                DeliveryProof::create([
                    'shipment_id'    => $shipment->id,
                    'stop_id'        => $stop?->id,
                    'recipient_name' => $podData->recipientName,
                    'received_at'    => $podData->receivedAt,
                    'reference'      => $podData->reference,
                    'notes'          => $podData->notes,
                    'photo_path'     => $storedPaths['photo'] ?? null,
                    'signature_path' => $storedPaths['signature'] ?? null,
                    'captured_via'   => $podData->capturedVia,
                    'captured_by_id' => Auth::id(),
                    'latitude'       => $podData->latitude,
                    'longitude'      => $podData->longitude,
                    'accuracy_m'     => $podData->accuracyM,
                ]);
            }

            $result = $this->workflow->transition($shipment, ShipmentState::DELIVERED, [
                'ability'     => 'markDelivered',
                'occurred_at' => $occurredAt,
                'stop_id'     => $stop?->id,
            ]);

            if ($podData) {
                $this->workflow->recordEvent($result, ShipmentEventType::POD_CAPTURED, [
                    'occurred_at' => $occurredAt,
                    'stop_id'     => $stop?->id,
                    'latitude'    => $podData->latitude,
                    'longitude'   => $podData->longitude,
                ]);
            }

            return $result;
        });
    }

    protected function transitionWithStops(
        Shipment $shipment,
        ShipmentState $state,
        string $ability,
        ?callable $updateStops = null,
        array $context = [],
    ): Shipment {
        $this->guard($shipment, $ability);

        return DB::transaction(function () use ($shipment, $state, $ability, $updateStops, $context): Shipment {
            $occurredAt = now();

            $result = $this->workflow->transition($shipment, $state, $context + [
                'ability'     => $ability,
                'occurred_at' => $occurredAt,
            ]);

            $updateStops?->call($this, $result, $occurredAt);

            return $result;
        });
    }

    protected function guard(Shipment $shipment, string $ability): void
    {
        LogisticsAccess::ensureEnabled((int) $shipment->company_id);
        Gate::authorize($ability, $shipment);
    }

    protected function deliveryStop(Shipment $shipment, ?int $stopId): ?Stop
    {
        $query = $shipment->stops()->where('type', StopType::DELIVERY);

        if ($stopId) {
            return $query->whereKey($stopId)->firstOrFail();
        }

        $stops = $query->limit(2)->get();

        if ($stops->count() > 1) {
            throw ValidationException::withMessages([
                'stop_id' => __('logistics::delivery.validation.stop-required'),
            ]);
        }

        return $stops->first();
    }

    protected function validatePod(PodData $podData, bool $photoRequired): void
    {
        Validator::make([
            'recipient_name' => $podData->recipientName,
            'received_at'    => $podData->receivedAt,
            'photo'          => $podData->photo,
            'signature'      => $podData->signature,
        ], [
            'recipient_name' => ['required', 'string', 'max:255'],
            'received_at'    => ['required', 'date'],
            'photo'          => [$photoRequired ? 'required' : 'nullable', 'file', 'mimetypes:image/jpeg,image/png,image/webp', 'max:5120'],
            'signature'      => ['nullable', 'file', 'mimetypes:image/jpeg,image/png,image/webp', 'max:5120'],
        ])->validate();
    }

    protected function storePodFiles(Shipment $shipment, PodData $podData): array
    {
        return $this->withShipmentDisk($shipment, function () use ($shipment, $podData): array {
            $directory = 'logistics/delivery-proofs/'.$shipment->getKey();

            return array_filter([
                'photo'     => $podData->photo?->store($directory, 'public'),
                'signature' => $podData->signature?->store($directory, 'public'),
            ]);
        });
    }

    protected function deletePodFiles(Shipment $shipment, array $paths): void
    {
        $this->withShipmentDisk($shipment, function () use ($paths): void {
            Storage::disk('public')->delete(array_values($paths));
        });
    }

    protected function withShipmentDisk(Shipment $shipment, callable $callback): mixed
    {
        $context = app(CompanyContext::class);
        $activeIds = $context->activeIds();
        $currentId = $context->currentId();
        $shipmentCompanyId = (int) $shipment->company_id;

        $context->setActive(array_values(array_unique([...$activeIds, $shipmentCompanyId])), $shipmentCompanyId);
        Storage::forgetDisk('public');

        try {
            return $callback();
        } finally {
            Storage::forgetDisk('public');
            $context->setActive($activeIds, $currentId);
            Storage::forgetDisk('public');
        }
    }

    /**
     * @return Collection<int, Stop>
     */
    protected function stops(Shipment $shipment, StopType $type): Collection
    {
        return $shipment->stops()->where('type', $type)->get();
    }
}
