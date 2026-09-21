<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Webkul\Logistics\Enums\ShipmentState;
use Webkul\Logistics\Enums\StopState;
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
    $company = LogisticsHelper::enable(LogisticsHelper::company());
    $shipment = LogisticsHelper::shipment($company, ['state' => ShipmentState::OUT_FOR_DELIVERY]);

    CompanySetting::forCompany($company->id)->update([
        'require_pod_for_delivery' => true,
        'require_pod_photo'        => true,
    ]);

    FilamentHelper::actingAsCompanyUser($company, [
        'mark_delivered_logistics_shipment',
        'capture_pod_logistics_shipment',
    ]);

    $service = app(DeliveryService::class);

    expect(fn () => $service->deliver($shipment))->toThrow(ValidationException::class);

    expect(fn () => $service->deliver($shipment, new PodData(
        recipientName: 'Receiving Clerk',
        receivedAt: now(),
    )))->toThrow(ValidationException::class);

    expect($shipment->refresh()->state)->toBe(ShipmentState::OUT_FOR_DELIVERY)
        ->and($shipment->deliveryProofs()->count())->toBe(0);
});

it('moves a failed delivery through retry to delivered', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());
    $shipment = LogisticsHelper::shipment($company, ['state' => ShipmentState::OUT_FOR_DELIVERY]);

    CompanySetting::forCompany($company->id)->update(['require_pod_for_delivery' => false]);

    FilamentHelper::actingAsCompanyUser($company, ['mark_delivered_logistics_shipment']);

    $service = app(DeliveryService::class);

    $service->fail($shipment, 'Recipient unavailable');
    expect($shipment->refresh()->state)->toBe(ShipmentState::FAILED_DELIVERY);

    $service->retry($shipment);
    expect($shipment->refresh()->state)->toBe(ShipmentState::OUT_FOR_DELIVERY);

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

    FilamentHelper::actingAsCompanyUser($ownerCompany, [
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
    $objectKey = "companies/{$ownerCompany->id}/{$proof->photo_path}";

    Storage::disk('s3')->put($objectKey, Storage::disk('public')->get($proof->photo_path));

    expect($proof->photo_path)->not->toContain('customer-supplied-name');

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
    $delivery = Stop::factory()->create(['shipment_id' => $shipment->id]);

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
        ->and($delivery->refresh()->state)->toBe(StopState::DEPARTED)
        ->and($delivery->actual_arrival_at)->not->toBeNull()
        ->and($delivery->actual_departure_at)->not->toBeNull()
        ->and($shipment->refresh()->actual_pickup_at)->not->toBeNull()
        ->and($shipment->actual_delivery_at)->not->toBeNull();
});
