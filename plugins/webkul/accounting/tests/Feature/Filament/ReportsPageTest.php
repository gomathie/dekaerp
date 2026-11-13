<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\Support\Filament\Pages\Reports;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';

/**
 * Reports page (reports plan R1): it lists exactly the report pages the
 * user may open, found from each module's Reporting cluster, and closes when
 * there are none. Lives in the accounting suite because Accounting is the
 * module with the most report pages.
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

    // The Reports page reads the panel's registered pages. If another test
    // built the panel before Accounting was installed, rebuild it so the
    // accounting report pages are part of it (order-independent test).
    FilamentHelper::bootAdminPanel();
});

it('lists only the reports the user may open', function () {
    FilamentHelper::actingAs(['page_accounting_trial_balance', 'page_accounting_tax_summary']);

    $labels = collect(Reports::entries())->flatten(1)->pluck('label')->all();

    expect($labels)->toContain(__('accounting::filament/clusters/reporting.pages.trial-balance.navigation.title'))
        ->and($labels)->toContain(__('accounting::filament/clusters/reporting.pages.tax-summary.navigation.title'))
        ->and($labels)->not->toContain(__('accounting::filament/clusters/reporting.pages.general-ledger.navigation.title'));

    Livewire::test(Reports::class)->assertOk();
});

it('closes when the user may open no report', function () {
    FilamentHelper::actingAs([]);

    expect(Reports::canAccess())->toBeFalse();
});
