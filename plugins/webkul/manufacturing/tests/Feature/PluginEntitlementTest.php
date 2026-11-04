<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Webkul\Manufacturing\Models\Order;
use Webkul\Manufacturing\Services\OrderWorkflow;
use Webkul\PluginManager\Exceptions\PluginNotEntitledException;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Support\PluginAccess;
use Webkul\PluginManager\Support\PluginEntitlement;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('manufacturing');
    PluginAccess::flush();
});

it('enforces the manufacturing entitlement through a registered resource policy', function (): void {
    $companyA = Company::factory()->create();
    $companyB = Company::factory()->create();
    $user = CompanyHelper::actingAsCompanyUser($companyA, ['view_any_manufacturing_order']);
    $plugin = Plugin::query()->where('name', 'manufacturing')->firstOrFail();

    expect($plugin->is_installed)->toBeTrue()
        ->and(PluginEntitlement::enabledForCompany('manufacturing', (int) $companyA->getKey()))->toBeTrue()
        ->and(Gate::forUser($user)->allows('viewAny', Order::class))->toBeTrue();

    DB::table('company_plugin')
        ->where('company_id', $companyA->getKey())
        ->where('plugin_id', $plugin->getKey())
        ->delete();
    PluginAccess::flush();

    expect(Gate::forUser($user)->allows('viewAny', Order::class))->toBeFalse()
        ->and(PluginEntitlement::enabledForCompany('manufacturing', (int) $companyB->getKey()))->toBeTrue();
});

it('blocks manufacturing order workflows after the company entitlement is revoked', function (): void {
    $company = Company::factory()->create();
    $plugin = Plugin::query()->where('name', 'manufacturing')->firstOrFail();

    expect(PluginEntitlement::enabledForCompany('manufacturing', (int) $company->getKey()))->toBeTrue();

    DB::table('company_plugin')
        ->where('company_id', $company->getKey())
        ->where('plugin_id', $plugin->getKey())
        ->delete();
    PluginAccess::flush();

    $order = new Order;
    $order->company_id = $company->getKey();

    expect(fn () => app(OrderWorkflow::class)->confirm($order))
        ->toThrow(PluginNotEntitledException::class);
});
