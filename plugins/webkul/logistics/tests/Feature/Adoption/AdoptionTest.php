<?php

use Illuminate\Support\Facades\Gate;
use Webkul\Logistics\Exceptions\LogisticsNotEnabledException;
use Webkul\Logistics\Models\CompanySetting;
use Webkul\Logistics\Models\Shipment;
use Webkul\Logistics\Services\CompanyProvisioner;
use Webkul\Logistics\Support\LogisticsAccess;
use Webkul\Logistics\Support\LogisticsSequences;
use Webkul\Product\Models\Product;
use Webkul\Support\Models\Sequence;

require_once __DIR__.'/../../Helpers/LogisticsHelper.php';

beforeEach(function () {
    LogisticsHelper::install();
});

it('keeps Logistics off for a company until an admin enables it', function () {
    $company = LogisticsHelper::company();

    expect(LogisticsAccess::enabledFor($company->id))->toBeFalse()
        ->and(CompanySetting::withoutGlobalScopes()->where('company_id', $company->id)->exists())->toBeFalse()
        ->and(Product::withoutGlobalScopes()->where('company_id', $company->id)->where('reference', 'like', 'LOG-%')->exists())->toBeFalse();
});

it('enables one company without enabling any other', function () {
    $a = LogisticsHelper::company();
    $b = LogisticsHelper::company();

    LogisticsHelper::enable($a);

    expect(LogisticsAccess::enabledFor($a->id))->toBeTrue()
        ->and(LogisticsAccess::enabledFor($b->id))->toBeFalse()
        ->and(fn () => LogisticsAccess::ensureEnabled($b->id))->toThrow(LogisticsNotEnabledException::class);
});

it('hides Logistics from users of a company that has not enabled it', function () {
    $a = LogisticsHelper::enable(LogisticsHelper::company());
    $b = LogisticsHelper::company();
    $permissions = ['view_any_logistics_shipment', 'create_logistics_shipment'];

    CompanyHelper::actingAsCompanyUser($b, $permissions);

    expect(Gate::allows('viewAny', Shipment::class))->toBeFalse()
        ->and(Gate::allows('create', Shipment::class))->toBeFalse();

    CompanyHelper::actingAsCompanyUser($a, $permissions);

    expect(Gate::allows('viewAny', Shipment::class))->toBeTrue();
});

it('provisions per-company numbering, default products and settings once', function () {
    $company = LogisticsHelper::company();
    $provisioner = app(CompanyProvisioner::class);

    $provisioner->enable($company);
    $provisioner->enable($company);
    $provisioner->provision($company);

    $products = Product::withoutGlobalScopes()
        ->where('company_id', $company->id)
        ->where('reference', 'like', 'LOG-%')
        ->pluck('reference')
        ->sort()
        ->values()
        ->all();

    expect($products)->toBe(collect(array_keys(CompanyProvisioner::SERVICE_PRODUCTS))->sort()->values()->all())
        ->and(Sequence::withoutGlobalScopes()->where('company_id', $company->id)->whereIn('code', LogisticsSequences::CODES)->count())->toBe(3)
        ->and(Sequence::withoutGlobalScopes()->whereNull('company_id')->whereIn('code', LogisticsSequences::CODES)->count())->toBe(0)
        ->and(CompanySetting::withoutGlobalScopes()->where('company_id', $company->id)->count())->toBe(1);
});

it('reports missing accounting setup instead of creating it', function () {
    $company = LogisticsHelper::company();

    $problems = app(CompanyProvisioner::class)->readiness($company);

    expect($problems)->toContain('missing-sale-journal')
        ->and($problems)->toContain('missing-purchase-journal');

    LogisticsHelper::enable($company);

    expect(CompanySetting::forCompany($company->id)->invoice_journal_id)->toBeNull();
});

it('keeps data but refuses changes after a company disables Logistics', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());
    $shipment = LogisticsHelper::shipment($company);

    LogisticsHelper::disable($company);

    CompanyHelper::actingAsCompanyUser($company, ['view_logistics_shipment', 'update_logistics_shipment']);

    expect(Shipment::withoutGlobalScopes()->whereKey($shipment->id)->exists())->toBeTrue()
        ->and(LogisticsAccess::enabledFor($company->id))->toBeFalse()
        ->and(Gate::allows('view', $shipment))->toBeTrue()
        ->and(Gate::allows('update', $shipment))->toBeFalse();

    LogisticsHelper::enable($company);

    expect(Gate::allows('update', $shipment))->toBeTrue();
});
