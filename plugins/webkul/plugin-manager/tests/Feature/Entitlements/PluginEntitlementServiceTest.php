<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Services\CompanyPluginProvisioner;
use Webkul\PluginManager\Services\PluginEntitlementService;
use Webkul\PluginManager\Support\PluginAccess;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\User;
use Webkul\Security\Services\SecurityAuditLogger;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';

/**
 * SAAS-03: one writer for `company_plugin`.
 *
 * Both screens - the Companies action on the Plugins page and the modules
 * section on the company form - now go through PluginEntitlementService, which
 * is where authorization, dependency closure, the audit record and the cache
 * flush live. These tests pin the rules that used to be spread over two
 * controllers of differing strictness.
 *
 * The dependency tests mock `dependencyNamesFor()` rather than installing two
 * real plugins: dependency names come from the package registration, and a real
 * install costs minutes per plugin in this suite.
 */
beforeEach(function (): void {
    TestBootstrapHelper::ensureERPInstalled();
    PluginAccess::flush();
});

function pluginEntitlementsSuperAdmin(): User
{
    $user = FilamentHelper::actingAs();

    $role = null;

    foreach (Role::getSuperAdminRoleNames() as $name) {
        $role = Role::query()->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])->first();

        if ($role) {
            break;
        }
    }

    expect($role)->not->toBeNull('no super-admin role exists, so this test would prove nothing');

    $user->assignRole($role);
    $user->unsetRelation('roles');

    Auth::guard('web')->setUser($user);
    Auth::guard('sanctum')->setUser($user);

    return $user;
}

function pluginEntitlementsCompanyAdmin(): User
{
    $user = FilamentHelper::actingAs();

    $role = Role::query()->whereRaw('LOWER(name) = ?', [mb_strtolower(Role::COMPANY_ADMIN)])->first()
        ?? Role::create(['name' => Role::COMPANY_ADMIN, 'guard_name' => 'web']);

    $user->assignRole($role);
    $user->unsetRelation('roles');

    Auth::guard('web')->setUser($user);
    Auth::guard('sanctum')->setUser($user);

    expect(auth()->user()?->isSuperAdmin())->toBeFalse();

    return $user;
}

function pluginEntitlementsPlugin(string $name): Plugin
{
    return Plugin::updateOrCreate(
        ['name' => $name],
        ['is_installed' => true, 'is_active' => true],
    );
}

/**
 * @return array<int, int>
 */
function pluginEntitlementsGranted(Company $company): array
{
    return app(PluginEntitlementService::class)->grantedPluginIds((int) $company->getKey());
}

it('grants only the configured default set to a new company', function () {
    $default = pluginEntitlementsPlugin('saas-default-plugin');
    $notDefault = pluginEntitlementsPlugin('saas-non-default-plugin');

    config(['plugin-manager.default_company_plugins' => ['saas-default-plugin']]);

    $company = Company::factory()->create(['is_active' => true]);

    $granted = pluginEntitlementsGranted($company);

    expect($granted)->toContain((int) $default->getKey())
        ->and($granted)->not->toContain((int) $notDefault->getKey());
});

it('keeps an existing grant when the default set is narrowed', function () {
    pluginEntitlementsPlugin('saas-kept-plugin');
    pluginEntitlementsPlugin('saas-dropped-plugin');

    $company = Company::factory()->create(['is_active' => true]);

    $before = pluginEntitlementsGranted($company);

    expect($before)->not->toBeEmpty();

    // Narrowing the default set must not reach back into a company that already
    // has the module: the audit called it out, because provisioning a company is
    // additive by design.
    config(['plugin-manager.default_company_plugins' => ['saas-kept-plugin']]);

    app(CompanyPluginProvisioner::class)->grantInstalledPlugins($company);

    expect(pluginEntitlementsGranted($company))->toBe($before);
});

it('adds the dependencies of a granted plugin', function () {
    pluginEntitlementsSuperAdmin();

    $child = pluginEntitlementsPlugin('saas-child-plugin');
    $dependency = pluginEntitlementsPlugin('saas-parent-plugin');

    $company = Company::factory()->create(['is_active' => true]);

    $service = Mockery::mock(PluginEntitlementService::class)->makePartial();
    $service->shouldReceive('dependencyNamesFor')->andReturnUsing(
        fn (string $name): array => $name === 'saas-child-plugin' ? ['saas-parent-plugin'] : [],
    );

    $service->syncForCompany($company, [(int) $child->getKey()]);

    $granted = pluginEntitlementsGranted($company);

    expect($granted)->toContain((int) $child->getKey())
        ->and($granted)->toContain((int) $dependency->getKey());
});

it('refuses to revoke a plugin a still-granted plugin depends on', function () {
    pluginEntitlementsSuperAdmin();

    $child = pluginEntitlementsPlugin('saas-dependent-plugin');
    $dependency = pluginEntitlementsPlugin('saas-required-plugin');

    $company = Company::factory()->create(['is_active' => true]);

    $service = Mockery::mock(PluginEntitlementService::class)->makePartial();
    $service->shouldReceive('dependencyNamesFor')->andReturnUsing(
        fn (string $name): array => $name === 'saas-dependent-plugin' ? ['saas-required-plugin'] : [],
    );

    $service->syncForCompany($company, [(int) $child->getKey(), (int) $dependency->getKey()]);

    expect(pluginEntitlementsGranted($company))->toContain((int) $dependency->getKey());

    // Dropping the dependency while the dependent stays granted would leave a
    // screen that cannot load. The set is refused, not silently adjusted.
    expect(fn () => $service->syncForCompany($company, [(int) $child->getKey()]))
        ->toThrow(ValidationException::class);

    expect(pluginEntitlementsGranted($company))->toContain((int) $dependency->getKey());
});

it('records the actor and the before/after grants', function () {
    $superAdmin = pluginEntitlementsSuperAdmin();

    $plugin = pluginEntitlementsPlugin('saas-audited-plugin');
    $company = Company::factory()->create(['is_active' => true]);

    $captured = null;

    $logger = Mockery::mock(SecurityAuditLogger::class);
    $logger->shouldReceive('record')->once()->andReturnUsing(function () use (&$captured): void {
        $captured = func_get_args();
    });

    $this->app->instance(SecurityAuditLogger::class, $logger);

    $before = pluginEntitlementsGranted($company);

    app(PluginEntitlementService::class)->syncForCompany($company, [(int) $plugin->getKey()], $superAdmin);

    expect($captured)->not->toBeNull()
        ->and($captured[0])->toBe('plugin.entitlement.updated')
        ->and($captured[1])->toBeInstanceOf(Company::class)
        ->and($captured[2]?->getKey())->toBe($superAdmin->getKey())
        ->and($captured[3])->toBe((int) $company->getKey())
        ->and($captured[4])->toBe(['plugin_ids' => $before])
        ->and($captured[5]['plugin_ids'])->toContain((int) $plugin->getKey());
});

it('refuses a tenant administrator', function () {
    pluginEntitlementsCompanyAdmin();

    $plugin = pluginEntitlementsPlugin('saas-forbidden-plugin');
    $company = Company::factory()->create(['is_active' => true]);

    expect(fn () => app(PluginEntitlementService::class)->syncForCompany($company, [(int) $plugin->getKey()]))
        ->toThrow(AuthorizationException::class);
});

it('ignores a plugin that is not installed', function () {
    pluginEntitlementsSuperAdmin();

    $notInstalled = Plugin::updateOrCreate(
        ['name' => 'saas-not-installed-plugin'],
        ['is_installed' => false, 'is_active' => true],
    );

    $company = Company::factory()->create(['is_active' => true]);

    app(PluginEntitlementService::class)->syncForCompany($company, [(int) $notInstalled->getKey()]);

    expect(DB::table('company_plugin')
        ->where('company_id', $company->getKey())
        ->where('plugin_id', $notInstalled->getKey())
        ->count())->toBe(0);
});

it('grants a newly installed plugin to every company', function () {
    $companyA = Company::factory()->create(['is_active' => true]);
    $companyB = Company::factory()->create(['is_active' => true]);

    $plugin = pluginEntitlementsPlugin('saas-installed-later');

    // Simulate "this plugin was just installed here": no company holds it yet.
    DB::table('company_plugin')->where('plugin_id', $plugin->getKey())->delete();
    PluginAccess::flush();

    $added = app(PluginEntitlementService::class)->grantToEveryCompany($plugin);

    expect($added)->toBe(2)
        ->and(pluginEntitlementsGranted($companyA))->toContain((int) $plugin->getKey())
        ->and(pluginEntitlementsGranted($companyB))->toContain((int) $plugin->getKey());
});
it('leaves a grant for a plugin that is not installed here untouched', function () {
    pluginEntitlementsSuperAdmin();

    $installed = pluginEntitlementsPlugin('saas-locally-installed-plugin');

    $elsewhere = Plugin::updateOrCreate(
        ['name' => 'saas-installed-elsewhere-plugin'],
        ['is_installed' => false, 'is_active' => true],
    );

    $company = Company::factory()->create(['is_active' => true]);

    DB::table('company_plugin')->insertOrIgnore([
        'company_id' => $company->getKey(),
        'plugin_id'  => $elsewhere->getKey(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // The set is replaced, but a grant this installation cannot even see is not
    // part of the set - dropping it would lose the entitlement for a company
    // whose plugin is installed on another node.
    app(PluginEntitlementService::class)->syncForCompany($company, [(int) $installed->getKey()]);

    expect(DB::table('company_plugin')
        ->where('company_id', $company->getKey())
        ->where('plugin_id', $elsewhere->getKey())
        ->count())->toBe(1)
        ->and(pluginEntitlementsGranted($company))->toContain((int) $installed->getKey());
});
