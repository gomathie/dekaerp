<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Webkul\Inventory\Models\Receipt;
use Webkul\Inventory\Models\Move;
use Webkul\Inventory\Services\MoveConfirmer;
use Webkul\PluginManager\Exceptions\PluginNotEntitledException;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Support\PluginAccess;
use Webkul\PluginManager\Support\PluginEntitlement;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('inventories');
    PluginAccess::flush();
});

it('enforces the inventories entitlement through a registered resource policy', function (): void {
    $companyA = Company::factory()->create();
    $companyB = Company::factory()->create();
    $user = CompanyHelper::actingAsCompanyUser($companyA, ['view_any_inventory_receipt']);
    $plugin = Plugin::query()->where('name', 'inventories')->firstOrFail();

    expect($plugin->is_installed)->toBeTrue()
        ->and(PluginEntitlement::enabledForCompany('inventories', (int) $companyA->getKey()))->toBeTrue()
        ->and(Gate::forUser($user)->allows('viewAny', Receipt::class))->toBeTrue();

    DB::table('company_plugin')
        ->where('company_id', $companyA->getKey())
        ->where('plugin_id', $plugin->getKey())
        ->delete();
    PluginAccess::flush();

    expect(Gate::forUser($user)->allows('viewAny', Receipt::class))->toBeFalse()
        ->and(PluginEntitlement::enabledForCompany('inventories', (int) $companyB->getKey()))->toBeTrue();
});

it('blocks inventory move confirmation after the company entitlement is revoked', function (): void {
    $company = Company::factory()->create();
    $plugin = Plugin::query()->where('name', 'inventories')->firstOrFail();

    expect(PluginEntitlement::enabledForCompany('inventories', (int) $company->getKey()))->toBeTrue();

    DB::table('company_plugin')
        ->where('company_id', $company->getKey())
        ->where('plugin_id', $plugin->getKey())
        ->delete();
    PluginAccess::flush();

    $move = new Move;
    $move->company_id = $company->getKey();

    expect(fn () => app(MoveConfirmer::class)->confirm(collect([$move])))
        ->toThrow(PluginNotEntitledException::class);
});
