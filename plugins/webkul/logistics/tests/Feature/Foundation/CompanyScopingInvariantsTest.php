<?php

use Webkul\Logistics\Models\ExpenseCategory;
use Webkul\Logistics\Models\PackageType;
use Webkul\Logistics\Models\ServiceType;
use Webkul\Logistics\Models\VehicleType;

require_once __DIR__.'/../../Helpers/LogisticsHelper.php';

$plugin = 'logistics';

$shared = [
    ServiceType::class,
    VehicleType::class,
    PackageType::class,
    ExpenseCategory::class,
];

beforeEach(function () {
    LogisticsHelper::install();
});

it('finds the models of this plugin that opt into company scoping', function () use ($plugin) {
    expect(CompanyScopeHelper::companyModels($plugin))->toHaveCount(16);
});

it('stamps the active company on every model that is not declared shared', function () use ($plugin, $shared) {
    expect(CompanyScopeHelper::unexpectedlyShared($plugin, $shared))->toBe([]);
});

it('keeps every model declared shared free of an automatic company', function () use ($shared) {
    expect(CompanyScopeHelper::unexpectedlyScoped($shared))->toBe([]);
});

it('lets every model declared shared hold no company', function () use ($shared) {
    expect(CompanyScopeHelper::withNonNullableCompanyColumn($shared))->toBe([]);
});
