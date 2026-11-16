<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\TaxGroupResource;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\TaxResource;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\TaxResource\Pages\ListTaxes;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';

/**
 * Tax groups live under Taxes, as in Zoho Books (user, 2026-11-14): not in
 * the side menu, reached from a button on the Taxes list, with Taxes kept
 * highlighted while on them.
 */
beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('accounting');

    DB::table('plugins')->whereIn('name', ['accounts', 'accounting'])->update([
        'is_installed' => true,
        'is_active'    => true,
        'updated_at'   => now(),
    ]);

    Package::$plugins = Plugin::all()->keyBy('name');

    URL::resolveMissingNamedRoutesUsing(fn () => '#');

    FilamentHelper::bootAdminPanel();
});

it('keeps tax groups out of the menu and reaches them from Taxes', function () {
    FilamentHelper::actingAs(['view_any_accounting_tax', 'view_any_accounting_tax::group']);

    expect(TaxGroupResource::shouldRegisterNavigation())->toBeFalse()
        ->and((array) TaxResource::getNavigationItemActiveRoutePattern())
        ->toContain(TaxGroupResource::getRouteBaseName().'.*');

    Livewire::test(ListTaxes::class)
        ->assertActionVisible('taxGroups');
});
