<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Support\PluginAccess;

require_once __DIR__.'/../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

/**
 * SAAS-00 characterization: this pins today's behavior, not the desired rule.
 *
 * Plugin grants can be revoked per company, but plugin routes and policies do
 * not yet enforce that decision. SAAS-04 must invert the response expectation
 * once a shared enforcement boundary protects HTTP, Filament, and service
 * entry points.
 */
beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('sales');
    PluginAccess::flush();
});

it('allows a sales API request after the current company loses its sales grant', function () {
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
        ->assertOk();
});
