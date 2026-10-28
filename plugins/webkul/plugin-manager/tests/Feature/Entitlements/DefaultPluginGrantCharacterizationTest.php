<?php

use Illuminate\Support\Facades\DB;
use Webkul\PluginManager\Services\CompanyPluginProvisioner;
use Webkul\PluginManager\Services\PluginEntitlementService;
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
 *
 * SAAS-03 update: the first test still holds, because `null` in
 * `config('plugin-manager.default_company_plugins')` deliberately means "every
 * installed plugin" - that is the upgrade path. The third test was rewritten to
 * assert the configuration seam that now exists.
 */
beforeEach(function (): void {
    // At least one plugin must be installed, or "grants every installed plugin"
    // would compare two empty lists and pass on a bare database.
    TestBootstrapHelper::ensurePluginInstalled('sales');
    PluginAccess::flush();
});

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

it('reads the default set from configuration instead of granting everything unconditionally', function () {
    // SAAS-03 replaced "grant every installed plugin" with a configured set.
    // `null` still means every installed plugin, so nothing changes for an
    // installation that does not set it - but there is now a key that controls
    // it, and the provisioner delegates to the one writer rather than querying
    // `plugins` itself.
    expect(config('plugin-manager.default_company_plugins'))->toBeNull();

    $provisioner = new ReflectionClass(CompanyPluginProvisioner::class);
    $provisionerSource = file_get_contents($provisioner->getFileName());

    expect($provisionerSource)->toContain('PluginEntitlementService')
        ->and($provisionerSource)->not->toContain("->where('is_installed', true)");

    $service = new ReflectionClass(PluginEntitlementService::class);
    $serviceSource = file_get_contents($service->getFileName());

    expect($serviceSource)->toContain('plugin-manager.default_company_plugins');
});
