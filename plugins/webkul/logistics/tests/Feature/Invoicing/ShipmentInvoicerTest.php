<?php

use Webkul\Account\Enums\AccountType;
use Webkul\Account\Enums\AmountType;
use Webkul\Account\Enums\DocumentType;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\RepartitionType;
use Webkul\Account\Enums\TaxIncludeOverride;
use Webkul\Account\Enums\TypeTaxUse;
use Webkul\Account\Models\Account;
use Webkul\Account\Models\Journal;
use Webkul\Account\Models\Tax;
use Webkul\Account\Models\TaxPartition;
use Webkul\Logistics\Exceptions\NothingToInvoice;
use Webkul\Logistics\Models\Shipment;
use Webkul\Logistics\Models\ShipmentCharge;
use Webkul\Logistics\Services\ShipmentInvoicer;
use Webkul\Product\Models\Product;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;

require_once __DIR__.'/../../Helpers/LogisticsHelper.php';

beforeEach(function () {
    LogisticsHelper::install();
});

/**
 * A Logistics-enabled company that can actually be invoiced.
 *
 * Accounting refuses to build a move without a sale journal, which is exactly
 * what CompanyProvisioner::readiness() reports as missing. Logistics never
 * creates journals or accounts - they belong to Accounting - so the test sets
 * up the same chart of accounts an onboarded company would already have.
 */
function billableCompany(): Company
{
    $company = LogisticsHelper::company();

    LogisticsHelper::enable($company);

    $currency = Currency::query()->find($company->currency_id) ?? Currency::query()->first();

    Journal::factory()->sale()->create([
        'company_id'         => $company->id,
        'currency_id'        => $currency?->id,
        'default_account_id' => Account::factory()->income()->create([
            'currency_id' => $currency?->id,
        ])->id,
    ]);

    return $company;
}

/**
 * A billable charge on a shipment, optionally taxed.
 */
function chargeOn(Shipment $shipment, float $priceUnit = 100, float $quantity = 1, array $taxes = []): ShipmentCharge
{
    // A real charge is filled from a service product (WP-7 step 1), and the
    // LOG-* products exist because enabling the company provisions them.
    // Without a product the move line has no account and accounting treats it
    // as a non-product line, so no tax is computed at all.
    $product = Product::withoutGlobalScopes()
        ->where('company_id', $shipment->company_id)
        ->where('reference', 'LOG-FREIGHT')
        ->first();

    $charge = $shipment->charges()->create([
        'description' => 'Freight',
        'quantity'    => $quantity,
        'price_unit'  => $priceUnit,
        'is_billable' => true,
        'product_id'  => $product?->id,
        'uom_id'      => $product?->uom_id,
        'currency_id' => $shipment->currency_id,
    ]);

    if ($taxes !== []) {
        $charge->taxes()->sync(collect($taxes)->pluck('id'));
    }

    return $charge->refresh();
}

/**
 * The tax must belong to the shipment's company, or accounting scopes it out
 * and the invoice silently computes untaxed. LogisticsHelper::company() creates
 * a NEW company on every call, so it must not be used here.
 */
function logisticsTax(Company $company, float $amount, TaxIncludeOverride $include): Tax
{
    $tax = Tax::factory()->create([
        'amount'                 => $amount,
        'amount_type'            => AmountType::PERCENT,
        'price_include_override' => $include,
        'type_tax_use'           => TypeTaxUse::SALE,
        'company_id'             => $company->id,
    ]);

    // Repartition lines are what make a tax compute. Without a BASE and a TAX
    // partition the tax contributes nothing and the invoice comes out untaxed
    // with no error - see AccountHelper::taxWithAccounts(), which this follows.
    $taxAccount = Account::factory()->create([
        'account_type' => AccountType::LIABILITY_CURRENT,
        'currency_id'  => $company->currency_id,
    ]);

    foreach ([DocumentType::INVOICE, DocumentType::REFUND] as $document) {
        TaxPartition::factory()->create([
            'tax_id'           => $tax->id,
            'document_type'    => $document,
            'repartition_type' => RepartitionType::BASE,
            'factor_percent'   => 100,
            'account_id'       => null,
            'company_id'       => $company->id,
        ]);

        TaxPartition::factory()->create([
            'tax_id'           => $tax->id,
            'document_type'    => $document,
            'repartition_type' => RepartitionType::TAX,
            'factor_percent'   => 100,
            'account_id'       => $taxAccount->id,
            'company_id'       => $company->id,
        ]);
    }

    return $tax->refresh();
}

it('invoices billable charges as an accounting invoice, not a second system', function () {
    $company = billableCompany();
    $shipment = LogisticsHelper::shipment($company);

    chargeOn($shipment, priceUnit: 250);

    CompanyHelper::actingAsCompanyUser($company, ['create_invoice_logistics_shipment']);

    $invoice = app(ShipmentInvoicer::class)->createInvoice($shipment);

    expect($invoice->move_type)->toBe(MoveType::OUT_INVOICE)
        ->and($invoice->invoice_origin)->toBe($shipment->name)
        ->and((int) $invoice->company_id)->toBe((int) $shipment->company_id)
        ->and((int) $invoice->partner_id)->toBe((int) $shipment->customer_id)
        // Product lines only: computeAccountMove() adds its own balancing and
        // tax lines, which are accounting's business, not ours to count.
        ->and($invoice->lines()->whereNotNull('product_id')->count())->toBe(1)
        // Linked through the pivot WP-1 created, so the shipment can show it.
        ->and($shipment->invoices()->count())->toBe(1);
});

it('matches accounting’s own totals for an exclusive tax', function () {
    $company = billableCompany();
    $shipment = LogisticsHelper::shipment($company);

    chargeOn($shipment, priceUnit: 100, taxes: [logisticsTax($company, 10, TaxIncludeOverride::TAX_EXCLUDED)]);

    CompanyHelper::actingAsCompanyUser($company, ['create_invoice_logistics_shipment']);

    $invoice = app(ShipmentInvoicer::class)->createInvoice($shipment)->refresh();

    // 100 net, 10% added on top.
    expect((float) $invoice->amount_untaxed)->toBe(100.0)
        ->and((float) $invoice->amount_total)->toBe(110.0);
});

it('matches accounting’s own totals for an inclusive tax', function () {
    $company = billableCompany();
    $shipment = LogisticsHelper::shipment($company);

    chargeOn($shipment, priceUnit: 110, taxes: [logisticsTax($company, 10, TaxIncludeOverride::TAX_INCLUDED)]);

    CompanyHelper::actingAsCompanyUser($company, ['create_invoice_logistics_shipment']);

    $invoice = app(ShipmentInvoicer::class)->createInvoice($shipment)->refresh();

    // 110 gross with the tax carved out, not added.
    expect((float) $invoice->amount_untaxed)->toBe(100.0)
        ->and((float) $invoice->amount_total)->toBe(110.0);
});

it('invoices only the charges added since the last invoice', function () {
    $company = billableCompany();
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
        ->and($invoiceTwo->lines()->whereNotNull('product_id')->count())->toBe(1)
        ->and((float) $invoiceTwo->amount_untaxed)->toBe(40.0)
        ->and($second->refresh()->move_line_id)->not->toBeNull();
});

it('refuses to invoice a shipment with nothing billable', function () {
    $company = billableCompany();
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
    $company = billableCompany();
    $shipment = LogisticsHelper::shipment($company);

    chargeOn($shipment);

    CompanyHelper::actingAsCompanyUser($company, ['view_any_logistics_shipment']);

    expect(fn () => app(ShipmentInvoicer::class)->createInvoice($shipment))
        ->toThrow(Illuminate\Auth\Access\AuthorizationException::class);

    expect($shipment->invoices()->count())->toBe(0);
});

it('refuses to invoice a shipment of a company the user is not in', function () {
    $a = billableCompany();
    $b = billableCompany();

    $theirs = LogisticsHelper::shipment($b);
    chargeOn($theirs);

    CompanyHelper::actingAsCompanyUser($a, ['create_invoice_logistics_shipment']);

    // The company scope hides it outright.
    expect(Shipment::query()->whereKey($theirs->id)->exists())->toBeFalse();

    // Holding the model does not help. The policy grants on permission plus the
    // per-company switch, both of which this user satisfies for company B, so
    // authorisation alone would let this through - the service re-reads under
    // the scope and finds nothing.
    expect(fn () => app(ShipmentInvoicer::class)->createInvoice($theirs))
        ->toThrow(Illuminate\Database\Eloquent\ModelNotFoundException::class);

    expect($theirs->invoices()->count())->toBe(0);
});

it('suggests waiting time only past the free allowance, and creates nothing on its own', function () {
    $company = billableCompany();

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
