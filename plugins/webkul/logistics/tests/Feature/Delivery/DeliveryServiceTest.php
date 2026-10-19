<?php

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Webkul\Logistics\Enums\ShipmentState;
use Webkul\Logistics\Enums\StopState;
use Webkul\Logistics\Exceptions\InvalidShipmentTransition;
use Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\Actions\DeliverAction;
use Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\Actions\FailDeliveryAction;
use Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\Actions\PickupAction;
use Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\RelationManagers\DeliveryProofsRelationManager;
use Webkul\Logistics\Models\CompanySetting;
use Webkul\Logistics\Models\Stop;
use Webkul\Logistics\Services\DeliveryService;
use Webkul\Logistics\Services\PodData;

require_once __DIR__.'/../../Helpers/LogisticsHelper.php';

beforeEach(function () {
    LogisticsHelper::install();
});

function podImage(string $name = 'evidence.png'): UploadedFile
{
    return UploadedFile::fake()->createWithContent(
        $name,
        base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII='),
    );
}

it('refuses delivery without the proof required by the shipment company', function () {
    $currentCompany = LogisticsHelper::enable(LogisticsHelper::company());
    $shipmentCompany = LogisticsHelper::enable(LogisticsHelper::company());
    $shipment = LogisticsHelper::shipment($shipmentCompany, ['state' => ShipmentState::OUT_FOR_DELIVERY]);

    CompanySetting::forCompany($currentCompany->id)->update([
        'require_pod_for_delivery' => false,
        'require_pod_photo'        => false,
    ]);
    CompanySetting::forCompany($shipmentCompany->id)->update([
        'require_pod_for_delivery' => true,
        'require_pod_photo'        => true,
    ]);

    FilamentHelper::actingAsCompanyUser([$currentCompany, $shipmentCompany], [
        'mark_delivered_logistics_shipment',
        'capture_pod_logistics_shipment',
    ]);

    $service = app(DeliveryService::class);

    expect(fn () => $service->deliver($shipment))->toThrow(ValidationException::class);

    expect(fn () => $service->deliver($shipment, new PodData(
        recipientName: 'Receiving Clerk',
        receivedAt: now(),
    )))->toThrow(ValidationException::class);

    expect(fn () => $service->deliver($shipment, new PodData(
        recipientName: 'Receiving Clerk',
        receivedAt: now(),
        photo: UploadedFile::fake()->create('forged.jpg', 10, 'text/plain'),
    )))->toThrow(ValidationException::class);

    expect($shipment->refresh()->state)->toBe(ShipmentState::OUT_FOR_DELIVERY)
        ->and($shipment->deliveryProofs()->count())->toBe(0);
});

it('moves a failed delivery through retry to delivered', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());
    $shipment = LogisticsHelper::shipment($company, ['state' => ShipmentState::OUT_FOR_DELIVERY]);
    $completedStop = Stop::factory()->create([
        'shipment_id'         => $shipment->id,
        'sequence'            => 1,
        'state'               => StopState::DEPARTED,
        'actual_arrival_at'   => now()->subHour(),
        'actual_departure_at' => now()->subHour(),
    ]);
    $retryStop = Stop::factory()->create(['shipment_id' => $shipment->id, 'sequence' => 2]);

    CompanySetting::forCompany($company->id)->update(['require_pod_for_delivery' => false]);

    FilamentHelper::actingAsCompanyUser($company, ['mark_delivered_logistics_shipment']);

    $service = app(DeliveryService::class);

    $service->fail($shipment, 'Recipient unavailable');
    expect($shipment->refresh()->state)->toBe(ShipmentState::FAILED_DELIVERY)
        ->and($completedStop->refresh()->state)->toBe(StopState::DEPARTED)
        ->and($retryStop->refresh()->state)->toBe(StopState::ARRIVED);

    $service->retry($shipment);
    expect($shipment->refresh()->state)->toBe(ShipmentState::OUT_FOR_DELIVERY)
        ->and($completedStop->refresh()->state)->toBe(StopState::DEPARTED)
        ->and($retryStop->refresh()->state)->toBe(StopState::PENDING);

    $service->deliver($shipment);

    expect($shipment->refresh()->state)->toBe(ShipmentState::DELIVERED)
        ->and($shipment->events()->where('type', 'delivery_failed')->exists())->toBeTrue()
        ->and($shipment->events()->where('type', 'delivery_retried')->exists())->toBeTrue()
        ->and($shipment->events()->where('type', 'delivered')->exists())->toBeTrue();
});

it('does not serve a POD file to a user from another company', function () {
    Storage::fake('public');
    Storage::fake('s3');

    $ownerCompany = LogisticsHelper::enable(LogisticsHelper::company());
    $otherCompany = LogisticsHelper::enable(LogisticsHelper::company());
    $shipment = LogisticsHelper::shipment($ownerCompany, ['state' => ShipmentState::OUT_FOR_DELIVERY]);

    FilamentHelper::actingAsCompanyUser([$otherCompany, $ownerCompany], [
        'mark_delivered_logistics_shipment',
        'capture_pod_logistics_shipment',
    ]);

    $service = app(DeliveryService::class);
    $service->deliver($shipment, new PodData(
        recipientName: 'Store Manager',
        receivedAt: now(),
        photo: podImage('customer-supplied-name.png'),
    ));

    $proof = $shipment->deliveryProofs()->firstOrFail();

    // Derived from the same code the UI links with, not hand-built. A literal
    // string here would still pass if objectKey() and the storage path drifted
    // apart - the file present, the link pointing elsewhere, and nobody the
    // wiser until a customer asks for proof of delivery.
    $objectKey = DeliveryProofsRelationManager::objectKey($proof, (string) $proof->photo_path);

    Storage::disk('s3')->put($objectKey, Storage::disk('public')->get($proof->photo_path));

    expect($proof->company_id)->toBe($ownerCompany->id)
        ->and(current_company_id())->toBe($otherCompany->id)
        ->and($proof->photo_path)->not->toContain('customer-supplied-name');

    FilamentHelper::actingAsCompanyUser($otherCompany);
    $this->get('/secure-storage/'.$objectKey)->assertNotFound();

    FilamentHelper::actingAsCompanyUser($ownerCompany);
    $response = $this->get('/secure-storage/'.$objectKey)->assertOk();

    expect($response->headers->get('Cache-Control'))
        ->toContain('private')
        ->toContain('max-age=3600');
});

it('updates stop actual times alongside shipment transitions', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());
    $shipment = LogisticsHelper::shipment($company, ['state' => ShipmentState::AWAITING_PICKUP]);
    $pickup = Stop::factory()->pickup()->create(['shipment_id' => $shipment->id]);
    $firstDelivery = Stop::factory()->create(['shipment_id' => $shipment->id, 'sequence' => 1]);
    $finalDelivery = Stop::factory()->create(['shipment_id' => $shipment->id, 'sequence' => 2]);

    CompanySetting::forCompany($company->id)->update(['require_pod_for_delivery' => false]);

    FilamentHelper::actingAsCompanyUser($company, [
        'mark_picked_up_logistics_shipment',
        'mark_delivered_logistics_shipment',
    ]);

    $service = app(DeliveryService::class);

    $service->markPickedUp($shipment);

    expect($pickup->refresh()->state)->toBe(StopState::ARRIVED)
        ->and($pickup->actual_arrival_at)->not->toBeNull();

    $service->markInTransit($shipment);
    $service->markOutForDelivery($shipment);
    $service->deliver($shipment);

    expect($pickup->refresh()->state)->toBe(StopState::DEPARTED)
        ->and($pickup->actual_departure_at)->not->toBeNull()
        ->and($firstDelivery->refresh()->state)->toBe(StopState::DEPARTED)
        ->and($firstDelivery->actual_arrival_at)->not->toBeNull()
        ->and($firstDelivery->actual_departure_at)->not->toBeNull()
        ->and($finalDelivery->refresh()->state)->toBe(StopState::PENDING)
        ->and($shipment->refresh()->actual_pickup_at)->not->toBeNull()
        ->and($shipment->actual_delivery_at)->toBeNull()
        ->and($shipment->state)->toBe(ShipmentState::OUT_FOR_DELIVERY);

    $service->deliver($shipment);

    expect($finalDelivery->refresh()->state)->toBe(StopState::DEPARTED)
        ->and($finalDelivery->actual_arrival_at)->not->toBeNull()
        ->and($finalDelivery->actual_departure_at)->not->toBeNull()
        ->and($shipment->refresh()->actual_delivery_at)->not->toBeNull()
        ->and($shipment->state)->toBe(ShipmentState::DELIVERED);
});

it('rejects a shipment model held before the active company changed', function () {
    $ownerCompany = LogisticsHelper::enable(LogisticsHelper::company());
    $otherCompany = LogisticsHelper::enable(LogisticsHelper::company());
    $shipment = LogisticsHelper::shipment($ownerCompany, ['state' => ShipmentState::OUT_FOR_DELIVERY]);

    CompanySetting::forCompany($ownerCompany->id)->update(['require_pod_for_delivery' => false]);

    FilamentHelper::actingAsCompanyUser($otherCompany, ['mark_delivered_logistics_shipment']);

    expect(fn () => app(DeliveryService::class)->deliver($shipment))
        ->toThrow(ModelNotFoundException::class);

    FilamentHelper::actingAsCompanyUser($ownerCompany);

    expect($shipment->refresh()->state)->toBe(ShipmentState::OUT_FOR_DELIVERY)
        ->and($shipment->deliveryProofs()->count())->toBe(0);
});

it('refuses an intermediate delivery unless the shipment is out for delivery', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());
    $shipment = LogisticsHelper::shipment($company, ['state' => ShipmentState::CONFIRMED]);
    $firstDelivery = Stop::factory()->create(['shipment_id' => $shipment->id, 'sequence' => 1]);
    $finalDelivery = Stop::factory()->create(['shipment_id' => $shipment->id, 'sequence' => 2]);

    CompanySetting::forCompany($company->id)->update(['require_pod_for_delivery' => false]);

    FilamentHelper::actingAsCompanyUser($company, ['mark_delivered_logistics_shipment']);

    expect(fn () => app(DeliveryService::class)->deliver($shipment))
        ->toThrow(InvalidShipmentTransition::class);

    expect($shipment->refresh()->state)->toBe(ShipmentState::CONFIRMED)
        ->and($firstDelivery->refresh()->state)->toBe(StopState::PENDING)
        ->and($finalDelivery->refresh()->state)->toBe(StopState::PENDING)
        ->and($shipment->deliveryProofs()->count())->toBe(0);
});

it('returns a failed shipment without reopening completed delivery stops', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());
    $shipment = LogisticsHelper::shipment($company, ['state' => ShipmentState::FAILED_DELIVERY]);
    $completedStop = Stop::factory()->create([
        'shipment_id'         => $shipment->id,
        'sequence'            => 1,
        'state'               => StopState::DEPARTED,
        'actual_arrival_at'   => now()->subHour(),
        'actual_departure_at' => now()->subHour(),
    ]);
    $remainingStop = Stop::factory()->create([
        'shipment_id'       => $shipment->id,
        'sequence'          => 2,
        'state'             => StopState::ARRIVED,
        'actual_arrival_at' => now(),
    ]);

    FilamentHelper::actingAsCompanyUser($company, ['mark_delivered_logistics_shipment']);

    app(DeliveryService::class)->return($shipment);

    expect($shipment->refresh()->state)->toBe(ShipmentState::RETURNED)
        ->and($completedStop->refresh()->state)->toBe(StopState::DEPARTED)
        ->and($remainingStop->refresh()->state)->toBe(StopState::SKIPPED)
        ->and($remainingStop->actual_departure_at)->not->toBeNull()
        ->and($shipment->events()->where('type', 'returned')->exists())->toBeTrue();
});

it('builds the workflow actions without shipment page wiring', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());
    $awaitingPickup = LogisticsHelper::shipment($company, ['state' => ShipmentState::AWAITING_PICKUP]);
    $inTransit = LogisticsHelper::shipment($company, ['state' => ShipmentState::IN_TRANSIT]);
    $failed = LogisticsHelper::shipment($company, ['state' => ShipmentState::FAILED_DELIVERY]);

    FilamentHelper::actingAsCompanyUser($company, [
        'mark_picked_up_logistics_shipment',
        'mark_delivered_logistics_shipment',
    ]);

    $pickupAction = PickupAction::make()->record($awaitingPickup);
    $deliverAction = DeliverAction::make()->record($inTransit);
    $failAction = FailDeliveryAction::make()->record($failed);

    expect($pickupAction->getName())->toBe('markPickedUp')
        ->and($pickupAction->isVisible())->toBeTrue()
        ->and($deliverAction->getName())->toBe('deliverShipment')
        ->and($deliverAction->isVisible())->toBeTrue()
        ->and($failAction->getName())->toBe('failDelivery')
        ->and($failAction->isVisible())->toBeTrue();
});
