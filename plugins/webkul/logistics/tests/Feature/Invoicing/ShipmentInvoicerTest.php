<?php

use Webkul\Account\Enums\AmountType;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\TaxIncludeOverride;
use Webkul\Account\Enums\TypeTaxUse;
use Webkul\Account\Models\Tax;
use Webkul\Logistics\Exceptions\NothingToInvoice;
use Webkul\Logistics\Models\Shipment;
use Webkul\Logistics\Models\ShipmentCharge;
use Webkul\Logistics\Services\ShipmentInvoicer;

require_once __DIR__.'/../../Helpers/LogisticsHelper.php';

beforeEach(function () {
    LogisticsHelper::install();
});

/**
 * A billable charge on a shipment, optionally taxed.
 */
function chargeOn(Shipment $shipment, float $priceUnit = 100, float $quantity = 1, array $taxes = []): ShipmentCharge
{
    $charge = $shipment->charges()->create([
        'description' => 'Freight',
        'quantity'    => $quantity,
        'price_unit'  => $priceUnit,
        'is_billable' => true,
        'currency_id' => $shipment->currency_id,
    ]);

    if ($taxes !== []) {
        $charge->taxes()->sync(collect($taxes)->pluck('id'));
    }

    return $charge->refresh();
}

function logisticsTax(float $amount, TaxIncludeOverride $include): Tax
{
    return Tax::factory()->create([
        'amount'                 => $amount,
        'amount_type'            => AmountType::PERCENT,
        'price_include_override' => $include,
        'type_tax_use'           => TypeTaxUse::SALE,
        'company_id'             => LogisticsHelper::company()->id,
    ]);
}

it('invoices billable charges as an accounting invoice, not a second system', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());
    $shipment = LogisticsHelper::shipment($company);

    chargeOn($shipment, priceUnit: 250);

    CompanyHelper::actingAsCompanyUser($company, ['create_invoice_logistics_shipment']);

    $invoice = app(ShipmentInvoicer::class)->createInvoice($shipment);

    expect($invoice->move_type)->toBe(MoveType::OUT_INVOICE)
        ->and($invoice->invoice_origin)->toBe($shipment->name)
        ->and((int) $invoice->company_id)->toBe((int) $shipment->company_id)
        ->and((int) $invoice->partner_id)->toBe((int) $shipment->customer_id)
        ->and($invoice->lines()->count())->toBe(1)
        // Linked through the pivot WP-1 created, so the shipment can show it.
        ->and($shipment->invoices()->count())->toBe(1);
});

it('matches accounting’s own totals for an exclusive tax', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());
    $shipment = LogisticsHelper::shipment($company);

    chargeOn($shipment, priceUnit: 100, taxes: [logisticsTax(10, TaxIncludeOverride::TAX_EXCLUDED)]);

    CompanyHelper::actingAsCompanyUser($company, ['create_invoice_logistics_shipment']);

    $invoice = app(ShipmentInvoicer::class)->createInvoice($shipment)->refresh();

    // 100 net, 10% added on top.
    expect((float) $invoice->amount_untaxed)->toBe(100.0)
        ->and((float) $invoice->amount_total)->toBe(110.0);
});

it('matches accounting’s own totals for an inclusive tax', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());
    $shipment = LogisticsHelper::shipment($company);

    chargeOn($shipment, priceUnit: 110, taxes: [logisticsTax(10, TaxIncludeOverride::TAX_INCLUDED)]);

    CompanyHelper::actingAsCompanyUser($company, ['create_invoice_logistics_shipment']);

    $invoice = app(ShipmentInvoicer::class)->createInvoice($shipment)->refresh();

    // 110 gross with the tax carved out, not added.
    expect((float) $invoice->amount_untaxed)->toBe(100.0)
        ->and((float) $invoice->amount_total)->toBe(110.0);
});

it('invoices only the charges added since the last invoice', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());
    $shipment = LogisticsHelper::shipment($company);

    $first = chargeOn($shipment, priceUnit: 100);

    CompanyHelper::actingAsCompanyUser($company, ['create_invoice_logistics_shipment']);

    $service = app(ShipmentInvoicer::class);

    $invoiceOne = $service->createInvoice($shipment);

    expect($first->refresh()->move_line_id)->not->toBeNull();

    // Nothing new yet.
    expect(fn () => $service->createInvoice($shipment))->toThrow(NothingToInvoice::class);

    $second = chargeOn($shipment, priceUnit: 40);

    $invoiceTwo = $service->createInvoice($shipment)->refresh();

    expect($invoiceTwo->getKey())->not->toBe($invoiceOne->getKey())
        ->and($invoiceTwo->lines()->count())->toBe(1)
        ->and((float) $invoiceTwo->amount_untaxed)->toBe(40.0)
        ->and($second->refresh()->move_line_id)->not->toBeNull();
});

it('refuses to invoice a shipment with nothing billable', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());
    $shipment = LogisticsHelper::shipment($company);

    // Present, but explicitly not billable.
    $shipment->charges()->create([
        'description' => 'Goodwill waiver',
        'quantity'    => 1,
        'price_unit'  => 50,
        'is_billable' => false,
        'currency_id' => $shipment->currency_id,
    ]);

    CompanyHelper::actingAsCompanyUser($company, ['create_invoice_logistics_shipment']);

    expect(fn () => app(ShipmentInvoicer::class)->createInvoice($shipment))
        ->toThrow(NothingToInvoice::class);

    expect($shipment->invoices()->count())->toBe(0);
});

it('refuses to invoice without the create_invoice permission', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());
    $shipment = LogisticsHelper::shipment($company);

    chargeOn($shipment);

    CompanyHelper::actingAsCompanyUser($company, ['view_any_logistics_shipment']);

    expect(fn () => app(ShipmentInvoicer::class)->createInvoice($shipment))
        ->toThrow(Illuminate\Auth\Access\AuthorizationException::class);

    expect($shipment->invoices()->count())->toBe(0);
});

it('refuses to invoice a shipment of a company the user is not in', function () {
    $a = LogisticsHelper::enable(LogisticsHelper::company());
    $b = LogisticsHelper::enable(LogisticsHelper::company());

    $theirs = LogisticsHelper::shipment($b);
    chargeOn($theirs);

    CompanyHelper::actingAsCompanyUser($a, ['create_invoice_logistics_shipment']);

    // The company scope hides it outright.
    expect(Shipment::query()->whereKey($theirs->id)->exists())->toBeFalse();

    expect(fn () => app(ShipmentInvoicer::class)->createInvoice($theirs))
        ->toThrow(Illuminate\Auth\Access\AuthorizationException::class);
});

it('suggests waiting time only past the free allowance, and creates nothing on its own', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());

    Webkul\Logistics\Models\CompanySetting::forCompany($company->id)
        ->forceFill(['free_waiting_minutes' => 30])->save();

    $shipment = LogisticsHelper::shipment($company);

    // Within the allowance: 20 minutes.
    $shipment->stops()->create([
        'type'                 => Webkul\Logistics\Enums\StopType::PICKUP,
        'state'                => Webkul\Logistics\Enums\StopState::DEPARTED,
        'sequence'             => 1,
        'actual_arrival_at'    => now()->subMinutes(20),
        'actual_departure_at'  => now(),
    ]);

    $service = app(ShipmentInvoicer::class);

    expect($service->waitingTimeSuggestions($shipment->refresh()))->toBe([]);

    // Over it: 90 minutes, so 60 billable.
    $shipment->stops()->create([
        'type'                => Webkul\Logistics\Enums\StopType::DELIVERY,
        'state'               => Webkul\Logistics\Enums\StopState::DEPARTED,
        'sequence'            => 2,
        'actual_arrival_at'   => now()->subMinutes(90),
        'actual_departure_at' => now(),
    ]);

    $suggestions = $service->waitingTimeSuggestions($shipment->refresh());

    expect($suggestions)->toHaveCount(1)
        ->and($suggestions[0]['billable_minutes'])->toBe(60)
        // Suggesting is not charging (D14).
        ->and($shipment->charges()->count())->toBe(0);
});
