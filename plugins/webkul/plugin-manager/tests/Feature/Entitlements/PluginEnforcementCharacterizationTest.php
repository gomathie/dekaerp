<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Support\PluginAccess;

require_once __DIR__.'/../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

/**
 * SAAS-00 characterization, **inverted by SAAS-04**.
 *
 * Plugin grants can be revoked per company. Until SAAS-04 the routes did not
 * enforce that decision - this test used to assert `assertOk()` on a revoked
 * grant, which is the "looks gated and is not" failure. The API route group now
 * carries `plugin.entitlement:<plugin>`, so the same request is denied.
 *
 * Kept rather than deleted: it is the record of the hole and the proof that it
 * closed. The full contract (exempt plugins, super admin, no-company, record
 * assertions) lives in PluginEntitlementGuardTest.
 */
beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('sales');
    PluginAccess::flush();
});

it('denies a sales API request after the current company loses its sales grant', function () {
    $user = SecurityHelper::authenticateWithPermissions(['view_any_sale_order']);
    $companyId = (int) $user->default_company_id;
    $sales = Plugin::query()->where('name', 'sales')->firstOrFail();

    DB::table('company_plugin')->insertOrIgnore([
        'company_id' => $companyId,
        'plugin_id'  => $sales->getKey(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    PluginAccess::flush();

    expect($companyId)->toBeGreaterThan(0)
        ->and($sales->is_installed)->toBeTrue()
        ->and(Route::has('admin.api.v1.sales.orders.index'))->toBeTrue()
        ->and($user->can('view_any_sale_order'))->toBeTrue()
        ->and(PluginAccess::enabledFor('sales', $companyId))->toBeTrue();

    DB::table('company_plugin')
        ->where('company_id', $companyId)
        ->where('plugin_id', $sales->getKey())
        ->delete();
    PluginAccess::flush();

    expect(PluginAccess::enabledFor('sales', $companyId))->toBeFalse();

    $this->getJson(route('admin.api.v1.sales.orders.index'))
        ->assertForbidden();
});
