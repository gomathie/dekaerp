<?php

use Webkul\Logistics\Enums\ShipmentEventType;
use Webkul\Logistics\Exceptions\CompanyMismatchException;
use Webkul\Logistics\Models\Expense;
use Webkul\Logistics\Models\ShipmentEvent;
use Webkul\Logistics\Models\ShipmentLine;
use Webkul\Logistics\Models\Stop;
use Webkul\Logistics\Models\Trip;

require_once __DIR__.'/../../Helpers/LogisticsHelper.php';

beforeEach(function () {
    LogisticsHelper::install();
});

it('numbers shipments and trips per company, each starting at one', function () {
    $a = LogisticsHelper::enable(LogisticsHelper::company());
    $b = LogisticsHelper::enable(LogisticsHelper::company());
    $year = now()->format('Y');

    $a1 = LogisticsHelper::shipment($a);
    $a2 = LogisticsHelper::shipment($a);
    $b1 = LogisticsHelper::shipment($b);

    expect($a1->name)->toBe("SHP/{$year}/00001")
        ->and($a2->name)->toBe("SHP/{$year}/00002")
        ->and($b1->name)->toBe("SHP/{$year}/00001")
        ->and(Trip::factory()->create(['company_id' => $a->id])->name)->toBe("TRP/{$year}/00001");
});

it('gives child records the company of their shipment, not the session company', function () {
    $a = LogisticsHelper::enable(LogisticsHelper::company());
    $b = LogisticsHelper::enable(LogisticsHelper::company());
    $shipment = LogisticsHelper::shipment($b);

    // The session is in company A while a company-B shipment is being filled in.
    CompanyHelper::actingAsCompanyUser([$a, $b], [], [$a->id, $b->id]);

    $line = ShipmentLine::create(['shipment_id' => $shipment->id, 'description' => 'Pallets']);
    $stop = Stop::factory()->create(['shipment_id' => $shipment->id]);

    expect($line->company_id)->toBe($b->id)
        ->and($stop->company_id)->toBe($b->id);
});

it('refuses a child record whose company differs from its shipment', function () {
    $a = LogisticsHelper::enable(LogisticsHelper::company());
    $b = LogisticsHelper::enable(LogisticsHelper::company());
    $shipment = LogisticsHelper::shipment($b);

    expect(fn () => ShipmentLine::create([
        'shipment_id' => $shipment->id,
        'description' => 'Pallets',
        'company_id'  => $a->id,
    ]))->toThrow(CompanyMismatchException::class);
});

it('refuses an expense linked to another company’s shipment', function () {
    $a = LogisticsHelper::enable(LogisticsHelper::company());
    $b = LogisticsHelper::enable(LogisticsHelper::company());
    $shipment = LogisticsHelper::shipment($b);

    expect(fn () => Expense::factory()->create([
        'company_id'  => $a->id,
        'shipment_id' => $shipment->id,
    ]))->toThrow(CompanyMismatchException::class);
});

it('keeps shipment events append-only', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());
    $shipment = LogisticsHelper::shipment($company);

    $event = ShipmentEvent::create([
        'shipment_id' => $shipment->id,
        'type'        => ShipmentEventType::CREATED,
    ]);

    expect($event->company_id)->toBe($company->id)
        ->and($event->occurred_at)->not->toBeNull()
        ->and(fn () => $event->update(['notes' => 'changed']))->toThrow(LogicException::class);
});
