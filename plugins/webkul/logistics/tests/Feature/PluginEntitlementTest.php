<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Webkul\Logistics\Models\CompanySetting;
use Webkul\Logistics\Models\Shipment;
use Webkul\Logistics\Support\LogisticsAccess;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Support\PluginAccess;
use Webkul\PluginManager\Support\PluginEntitlement;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('logistics');
    PluginAccess::flush();
    LogisticsAccess::flush();
});

it('requires both the plugin entitlement and the Logistics company activation for policies', function (): void {
    $company = Company::factory()->create();
    $user = CompanyHelper::actingAsCompanyUser($company, ['view_any_logistics_shipment']);
    $plugin = Plugin::query()->where('name', 'logistics')->firstOrFail();
    $setting = CompanySetting::forCompany((int) $company->getKey());
    $setting->forceFill(['is_enabled' => true])->save();
    LogisticsAccess::flush();

    expect(PluginEntitlement::enabledForCompany('logistics', (int) $company->getKey()))->toBeTrue()
        ->and(Gate::forUser($user)->allows('viewAny', Shipment::class))->toBeTrue();

    DB::table('company_plugin')
        ->where('company_id', $company->getKey())
        ->where('plugin_id', $plugin->getKey())
        ->delete();
    PluginAccess::flush();
    LogisticsAccess::flush();

    expect(Gate::forUser($user)->allows('viewAny', Shipment::class))->toBeFalse();
});
