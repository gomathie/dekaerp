<?php

use Illuminate\Support\Facades\DB;
use Webkul\PluginManager\Services\CompanyPluginProvisioner;
use Webkul\PluginManager\Support\PluginAccess;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

/**
 * SAAS-00 characterization: pins a known defect, it does not endorse it.
 *
 * Every newly created company receives every globally installed plugin. There
 * is no configured default set — the provisioner grants the full installed
 * list. SAAS-03 must add a configurable default set without revoking existing
 * grants.
 *
 * See: docs/saas-administration-billing-audit-plan.md § ENT-02.
 */
it('grants every installed plugin to a new company', function () {
    $installedPluginIds = DB::table('plugins')
        ->where('is_installed', true)
        ->pluck('id')
        ->map(fn ($id): int => (int) $id)
        ->sort()
        ->values()
        ->all();

    // Precondition: at least some plugins are installed.
    expect($installedPluginIds)->not->toBeEmpty();

    $company = Company::factory()->create(['is_active' => true]);
    PluginAccess::flush();

    $grantedPluginIds = DB::table('company_plugin')
        ->where('company_id', $company->getKey())
        ->pluck('plugin_id')
        ->map(fn ($id): int => (int) $id)
        ->sort()
        ->values()
        ->all();

    // The behavior: the provisioner (called by Company::created event) grants
    // every installed plugin to the new company — not a subset.
    expect($grantedPluginIds)->toBe($installedPluginIds);
});

it('does not revoke grants when called again on an existing company', function () {
    $company = Company::factory()->create(['is_active' => true]);
    PluginAccess::flush();

    $grantedBefore = DB::table('company_plugin')
        ->where('company_id', $company->getKey())
        ->count();

    // Calling the provisioner again is idempotent.
    $granted = app(CompanyPluginProvisioner::class)->grantInstalledPlugins($company);

    $grantedAfter = DB::table('company_plugin')
        ->where('company_id', $company->getKey())
        ->count();

    expect($granted)->toBe(0)
        ->and($grantedAfter)->toBe($grantedBefore);
});

it('confirms there is no default_set or configurable plugin list', function () {
    // The provisioner has no concept of a "default set" — it queries all
    // installed plugins directly. There is no setting, config key or
    // database row that controls which plugins a new company should receive.
    $provisioner = new ReflectionClass(CompanyPluginProvisioner::class);
    $source = file_get_contents($provisioner->getFileName());

    // The provisioner queries all installed plugins unconditionally.
    expect($source)->toContain("->where('is_installed', true)")
        ->and($source)->not->toContain('default_set')
        ->and($source)->not->toContain('default_plugins');
});
