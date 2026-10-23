<?php

use Illuminate\Support\Facades\DB;
use Webkul\PluginManager\Services\CompanyPluginProvisioner;
use Webkul\PluginManager\Support\PluginAccess;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/CompanyHelper.php';

/**
 * Per-company plugin availability (user, 2026-10-21: "we should be able to
 * activate plugins for some companies and some other companies not").
 *
 * What is asserted is the semantics, because that is where the risk is. A row
 * means the company may use the plugin and no row means it may not, so the
 * dangerous mistake is not a wrong boolean - it is the feature silently
 * removing every plugin from every company the moment it ships.
 *
 * Every fixture is built here rather than taken from the bootstrap. This suite
 * starts with no plugin marked installed at all, so a test that assumed one
 * existed either failed confusingly or - worse - passed while asserting
 * nothing, which is what the first version of this file did.
 */
beforeEach(function (): void {
    PluginAccess::flush();
});

function installedPlugin(?string $name = null): array
{
    $name = $name ?? 'test-plugin-'.fake()->unique()->numerify('######');

    $id = DB::table('plugins')->insertGetId([
        'name'         => $name,
        'is_installed' => true,
        'is_active'    => true,
        'sort'         => 1,
        'created_at'   => now(),
        'updated_at'   => now(),
    ]);

    PluginAccess::flush();

    return ['id' => $id, 'name' => $name];
}

function revokePlugin(int $companyId, int $pluginId): void
{
    DB::table('company_plugin')
        ->where('company_id', $companyId)
        ->where('plugin_id', $pluginId)
        ->delete();

    PluginAccess::flush();
}

function grantPlugin(int $companyId, int $pluginId): void
{
    DB::table('company_plugin')->insertOrIgnore([
        'company_id' => $companyId,
        'plugin_id'  => $pluginId,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    PluginAccess::flush();
}

function freshCompany(string $label): Company
{
    return Company::factory()->create([
        'name' => $label.' '.fake()->unique()->numerify('######'),
    ]);
}

it('grants every installed plugin to a newly created company', function () {
    // The cliff this avoids: the migration backfills existing companies, so
    // without this a company created afterwards would start with nothing and
    // look like a broken install.
    $a = installedPlugin();
    $b = installedPlugin();

    $company = freshCompany('Fresh Tenant');

    $granted = DB::table('company_plugin')
        ->where('company_id', $company->getKey())
        ->pluck('plugin_id')
        ->all();

    expect($granted)->toContain($a['id'])
        ->and($granted)->toContain($b['id']);
});

it('lets one company keep a plugin that another has lost', function () {
    // The whole point of the feature, as one assertion.
    $plugin = installedPlugin();

    $keeps = freshCompany('Keeps');
    $loses = freshCompany('Loses');

    revokePlugin((int) $loses->getKey(), $plugin['id']);

    expect(PluginAccess::enabledFor($plugin['name'], (int) $keeps->getKey()))->toBeTrue()
        ->and(PluginAccess::enabledFor($plugin['name'], (int) $loses->getKey()))->toBeFalse();
});

it('treats a missing row as no access, and a restored row as access again', function () {
    $plugin = installedPlugin();
    $company = freshCompany('Toggles');
    $companyId = (int) $company->getKey();

    revokePlugin($companyId, $plugin['id']);
    expect(PluginAccess::enabledFor($plugin['name'], $companyId))->toBeFalse();

    grantPlugin($companyId, $plugin['id']);
    expect(PluginAccess::enabledFor($plugin['name'], $companyId))->toBeTrue();
});

it('denies access when there is no company at all', function () {
    // Console commands and queued jobs can run with no company resolved, and
    // the safe answer there is no.
    $plugin = installedPlugin();

    expect(PluginAccess::enabledFor($plugin['name'], null))->toBeFalse()
        ->and(PluginAccess::enabledFor($plugin['name'], 0))->toBeFalse();
});

it('ignores a grant for a plugin that is no longer installed', function () {
    // Both questions must be true: the code must exist here, and the company
    // must be allowed it. A stale grant row must not resurrect an uninstalled
    // plugin.
    $plugin = installedPlugin();
    $company = freshCompany('Stale');
    $companyId = (int) $company->getKey();

    expect(PluginAccess::enabledFor($plugin['name'], $companyId))->toBeTrue();

    DB::table('plugins')->where('id', $plugin['id'])->update(['is_installed' => false]);
    PluginAccess::flush();

    $grantStillThere = DB::table('company_plugin')
        ->where('company_id', $companyId)
        ->where('plugin_id', $plugin['id'])
        ->exists();

    expect($grantStillThere)->toBeTrue()
        ->and(PluginAccess::enabledFor($plugin['name'], $companyId))->toBeFalse();
});

it('reports access across the active companies for navigation', function () {
    $plugin = installedPlugin();

    $allowed = freshCompany('Allowed');
    $denied = freshCompany('Denied');

    revokePlugin((int) $denied->getKey(), $plugin['id']);

    CompanyHelper::actingAsCompanyUser([$allowed, $denied]);
    PluginAccess::flush();

    // True because one active company may use it; per-record checks still use
    // enabledFor() with the record's own company.
    expect(PluginAccess::enabledForCurrent($plugin['name']))->toBeTrue();

    CompanyHelper::actingAsCompanyUser([$denied]);
    PluginAccess::flush();

    expect(PluginAccess::enabledForCurrent($plugin['name']))->toBeFalse();
});

it('does not grant twice when provisioning runs again', function () {
    // Asserted against a plugin this test created, so it cannot pass by there
    // being nothing to grant - which is how the first version of this test
    // passed while proving nothing.
    $plugin = installedPlugin();
    $company = freshCompany('Idempotent');

    $before = DB::table('company_plugin')->where('company_id', $company->getKey())->count();

    expect($before)->toBeGreaterThan(0);

    $granted = app(CompanyPluginProvisioner::class)->grantInstalledPlugins($company);

    expect($granted)->toBe(0)
        ->and(DB::table('company_plugin')->where('company_id', $company->getKey())->count())->toBe($before);
});

it('lists the companies allowed a plugin', function () {
    $plugin = installedPlugin();

    $allowed = freshCompany('Listed');
    $denied = freshCompany('Unlisted');

    revokePlugin((int) $denied->getKey(), $plugin['id']);

    $ids = PluginAccess::companyIdsFor($plugin['name']);

    expect($ids)->toContain((int) $allowed->getKey())
        ->and($ids)->not->toContain((int) $denied->getKey());
});
