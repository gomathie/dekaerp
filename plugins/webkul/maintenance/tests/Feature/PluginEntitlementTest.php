<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Webkul\Maintenance\Models\Equipment;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Support\PluginAccess;
use Webkul\PluginManager\Support\PluginEntitlement;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('maintenance');
    PluginAccess::flush();
});

it('enforces the maintenance entitlement through a registered resource policy', function (): void {
    $companyA = Company::factory()->create();
    $companyB = Company::factory()->create();
    $user = CompanyHelper::actingAsCompanyUser($companyA, ['view_any_maintenance_equipment']);
    $plugin = Plugin::query()->where('name', 'maintenance')->firstOrFail();

    expect($plugin->is_installed)->toBeTrue()
        ->and(PluginEntitlement::enabledForCompany('maintenance', (int) $companyA->getKey()))->toBeTrue()
        ->and(Gate::forUser($user)->allows('viewAny', Equipment::class))->toBeTrue();

    DB::table('company_plugin')
        ->where('company_id', $companyA->getKey())
        ->where('plugin_id', $plugin->getKey())
        ->delete();
    PluginAccess::flush();

    expect(Gate::forUser($user)->allows('viewAny', Equipment::class))->toBeFalse()
        ->and(PluginEntitlement::enabledForCompany('maintenance', (int) $companyB->getKey()))->toBeTrue();
});