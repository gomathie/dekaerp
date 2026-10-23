<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Webkul\PluginManager\CompanyContributions;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Support\PluginAccess;
use Webkul\Security\Models\Role;
use Webkul\Support\Filament\Resources\CompanyResource\Support\CompanySchemaRegistry;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/FilamentHelper.php';

/**
 * Per-company plugin grants from the company side (user, 2026-10-23: editing a
 * company should show its plugins, the way a PILOT account shows its modules).
 *
 * The section is contributed into support's company form through
 * CompanySchemaRegistry - support is the base plugin and must not reference
 * Plugin Manager, which depends on it - and it writes the same `company_plugin`
 * rows as the Companies action on the Plugins page.
 *
 * What matters most here is who may write them: entitlement is an
 * installation-level decision, so a Company Admin must not be able to grant
 * their own company a plugin.
 *
 * CompanyContributions::register() is deliberately NOT called here. The service
 * provider already called it at boot, and the registry is static with no reset,
 * so registering again would contribute the section twice.
 */
beforeEach(function () {
    TestBootstrapHelper::ensureERPInstalled();
});

function companyModulesPlugin(string $name): Plugin
{
    return Plugin::updateOrCreate(
        ['name' => $name],
        ['is_installed' => true, 'is_active' => true],
    );
}

/**
 * @return array<int, int>
 */
function companyModulesGrants(Company $company): array
{
    return DB::table('company_plugin')
        ->where('company_id', $company->getKey())
        ->pluck('plugin_id')
        ->map(fn ($id): int => (int) $id)
        ->all();
}

function companyModulesActAsSuperAdmin(): void
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

    expect(auth()->user()?->isSuperAdmin())->toBeTrue();
}

function companyModulesActAsCompanyAdmin(): void
{
    $user = FilamentHelper::actingAs();

    $role = Role::query()->whereRaw('LOWER(name) = ?', [mb_strtolower(Role::COMPANY_ADMIN)])->first()
        ?? Role::create(['name' => Role::COMPANY_ADMIN, 'guard_name' => 'web']);

    $user->assignRole($role);
    $user->unsetRelation('roles');

    Auth::guard('web')->setUser($user);
    Auth::guard('sanctum')->setUser($user);

    expect(auth()->user()?->isCompanyAdmin())->toBeTrue()
        ->and(auth()->user()?->isSuperAdmin())->toBeFalse();
}

it('contributes a plugins section into the company form', function () {
    companyModulesActAsSuperAdmin();

    // If the contribution were not registered by the service provider, every
    // assertion below would be testing an empty slot.
    $sections = CompanySchemaRegistry::renderForm('main.after');

    expect($sections)->toHaveCount(1)
        ->and($sections[0]->isVisible())->toBeTrue();
});

it('hides the section from a company admin', function () {
    companyModulesActAsCompanyAdmin();

    $sections = CompanySchemaRegistry::renderForm('main.after');

    expect($sections)->toHaveCount(1)
        ->and($sections[0]->isVisible())->toBeFalse();
});

it('grants the chosen plugins to the company', function () {
    companyModulesActAsSuperAdmin();

    $company = Company::factory()->create();
    $a = companyModulesPlugin('test-plugin-a');
    $b = companyModulesPlugin('test-plugin-b');

    CompanyContributions::syncGrants($company, [$a->id, $b->id]);

    expect(companyModulesGrants($company))->toContain($a->id)
        ->and(companyModulesGrants($company))->toContain($b->id);
});

it('revokes a plugin that was unticked', function () {
    companyModulesActAsSuperAdmin();

    $company = Company::factory()->create();
    $a = companyModulesPlugin('test-plugin-a');
    $b = companyModulesPlugin('test-plugin-b');

    CompanyContributions::syncGrants($company, [$a->id, $b->id]);

    expect(companyModulesGrants($company))->toContain($b->id);

    CompanyContributions::syncGrants($company, [$a->id]);

    expect(companyModulesGrants($company))->toContain($a->id)
        ->and(companyModulesGrants($company))->not->toContain($b->id);
});

it('refuses to change grants for a company admin', function () {
    companyModulesActAsCompanyAdmin();

    $company = Company::factory()->create();
    $plugin = companyModulesPlugin('test-plugin-a');

    DB::table('company_plugin')
        ->where('company_id', $company->getKey())
        ->where('plugin_id', $plugin->id)
        ->delete();

    CompanyContributions::syncGrants($company, [$plugin->id]);

    expect(companyModulesGrants($company))->not->toContain($plugin->id);
});

it('flushes the access cache so a revoked grant is not served from memory', function () {
    companyModulesActAsSuperAdmin();

    $company = Company::factory()->create();
    companyModulesPlugin('test-plugin-a');

    CompanyContributions::syncGrants($company, [Plugin::where('name', 'test-plugin-a')->value('id')]);

    expect(PluginAccess::enabledFor('test-plugin-a', $company->getKey()))->toBeTrue();

    CompanyContributions::syncGrants($company, []);

    // Without the flush in syncGrants this still reads true: PluginAccess is a
    // scoped singleton holding the grants for the whole request.
    expect(PluginAccess::enabledFor('test-plugin-a', $company->getKey()))->toBeFalse();
});
