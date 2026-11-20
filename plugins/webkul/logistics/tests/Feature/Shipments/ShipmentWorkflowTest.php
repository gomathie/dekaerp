<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Webkul\Logistics\Enums\ShipmentEventSource;
use Webkul\Logistics\Enums\ShipmentEventType;
use Webkul\Logistics\Enums\ShipmentState;
use Webkul\Logistics\Exceptions\InvalidShipmentTransition;
use Webkul\Logistics\Exceptions\LogisticsNotEnabledException;
use Webkul\Logistics\Models\ShipmentLine;
use Webkul\Logistics\Services\ShipmentTotals;
use Webkul\Logistics\Services\ShipmentWorkflow;

require_once __DIR__.'/../../Helpers/LogisticsHelper.php';

beforeEach(function () {
    LogisticsHelper::install();
});

it('moves a shipment through every valid transition and records each one', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());
    $shipment = LogisticsHelper::shipment($company);
    $workflow = app(ShipmentWorkflow::class);

    $path = [
        ShipmentState::CONFIRMED,
        ShipmentState::AWAITING_PICKUP,
        ShipmentState::PICKED_UP,
        ShipmentState::IN_TRANSIT,
        ShipmentState::OUT_FOR_DELIVERY,
        ShipmentState::DELIVERED,
    ];

    foreach ($path as $state) {
        $workflow->transition($shipment, $state);
    }

    $shipment->refresh();

    expect($shipment->state)->toBe(ShipmentState::DELIVERED)
        ->and($shipment->actual_pickup_at)->not->toBeNull()
        ->and($shipment->actual_delivery_at)->not->toBeNull()
        ->and($shipment->events()->count())->toBe(count($path))
        // Ordered by id: a whole path runs inside one second, so occurred_at ties.
        ->and($shipment->events()->orderByDesc('id')->first()->type)->toBe(ShipmentEventType::DELIVERED);
});

it('refuses a transition the state machine does not allow', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());
    $shipment = LogisticsHelper::shipment($company);

    expect(fn () => app(ShipmentWorkflow::class)->transition($shipment, ShipmentState::DELIVERED))
        ->toThrow(InvalidShipmentTransition::class);

    expect($shipment->refresh()->state)->toBe(ShipmentState::DRAFT)
        ->and($shipment->events()->count())->toBe(0);
});

it('refuses any transition for a company that has not enabled Logistics', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());
    $shipment = LogisticsHelper::shipment($company);

    LogisticsHelper::disable($company);

    expect(fn () => app(ShipmentWorkflow::class)->confirm($shipment))
        ->toThrow(LogisticsNotEnabledException::class);
});

it('refuses to confirm for a user without the confirm permission', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());
    $shipment = LogisticsHelper::shipment($company);

    CompanyHelper::actingAsCompanyUser($company, ['view_any_logistics_shipment']);

    expect(fn () => app(ShipmentWorkflow::class)->confirm($shipment))
        ->toThrow(AuthorizationException::class);

    expect($shipment->refresh()->state)->toBe(ShipmentState::DRAFT);
});

it('lets a permitted user confirm, hold, release and cancel', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());
    $shipment = LogisticsHelper::shipment($company);
    $workflow = app(ShipmentWorkflow::class);

    CompanyHelper::actingAsCompanyUser($company, [
        'confirm_logistics_shipment',
        'update_logistics_shipment',
        'cancel_logistics_shipment',
    ]);

    $workflow->confirm($shipment);
    expect($shipment->refresh()->state)->toBe(ShipmentState::CONFIRMED);

    $workflow->hold($shipment, ['notes' => 'Customer asked to wait']);
    expect($shipment->refresh()->state)->toBe(ShipmentState::ON_HOLD);

    $workflow->release($shipment);
    expect($shipment->refresh()->state)->toBe(ShipmentState::CONFIRMED);

    $workflow->cancel($shipment);
    expect($shipment->refresh()->state)->toBe(ShipmentState::CANCELLED)
        ->and($shipment->events()->where('type', ShipmentEventType::CANCELLED)->exists())->toBeTrue()
        ->and($shipment->events()->first()->user_id)->toBe(Auth::id());
});

it('records system events without a user and without authorising', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());
    $shipment = LogisticsHelper::shipment($company);

    app(ShipmentWorkflow::class)->transition($shipment, ShipmentState::CONFIRMED, [
        'ability' => 'confirm',
        'source'  => ShipmentEventSource::SYSTEM,
    ]);

    $event = $shipment->events()->first();

    expect($shipment->refresh()->state)->toBe(ShipmentState::CONFIRMED)
        ->and($event->source)->toBe(ShipmentEventSource::SYSTEM);
});

it('recalculates cargo totals from the lines', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());
    $shipment = LogisticsHelper::shipment($company);

    ShipmentLine::create(['shipment_id' => $shipment->id, 'description' => 'Pallets', 'quantity' => 3, 'weight_kg' => 120.5, 'volume_m3' => 1.25]);
    ShipmentLine::create(['shipment_id' => $shipment->id, 'description' => 'Cartons', 'quantity' => 7, 'weight_kg' => 40.25, 'volume_m3' => 0.75]);

    app(ShipmentTotals::class)->recalculate($shipment);

    expect((int) $shipment->total_packages)->toBe(10)
        ->and((float) $shipment->total_weight_kg)->toBe(160.75)
        ->and((float) $shipment->total_volume_m3)->toBe(2.0);
});
