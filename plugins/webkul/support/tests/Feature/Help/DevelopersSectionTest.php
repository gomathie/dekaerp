<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Symfony\Component\Yaml\Yaml;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Support\PluginAccess;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\User;
use Webkul\Support\Filament\Pages\Help;
use Webkul\Support\Models\Company;
use Webkul\Support\Services\ApiReference;

require_once __DIR__.'/../../Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../Helpers/FilamentHelper.php';
require_once __DIR__.'/../../Helpers/CompanyHelper.php';

/**
 * The Developers tab on the Help page (user, 2026-11-25, approved mockup): the
 * API reference for Company Admin and up, listing only the modules the
 * current company has.
 *
 * The tab is a Livewire state on a page every user can open, so these tests
 * check the refusal from the server side as well as the hidden tab: a link
 * with ?tab=developers, and a tampered update, must show an ordinary user
 * nothing.
 */
beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('sales');
    PluginAccess::flush();
    Cache::flush();
});

function developersCompany(bool $withSales = true): Company
{
    $company = Company::factory()->create();

    developersGrant($company, 'sales', $withSales);

    return $company;
}

function developersGrant(Company $company, string $plugin, bool $enabled): void
{
    $pluginId = Plugin::query()->where('name', $plugin)->value('id');

    expect($pluginId)->not->toBeNull("the {$plugin} plugin is not installed, so this test would prove nothing");

    DB::table('company_plugin')
        ->where('company_id', $company->getKey())
        ->where('plugin_id', $pluginId)
        ->delete();

    if ($enabled) {
        DB::table('company_plugin')->insert([
            'company_id' => $company->getKey(),
            'plugin_id'  => $pluginId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    PluginAccess::flush();
}

function developersActingAs(Company $company, bool $companyAdmin): User
{
    $user = FilamentHelper::actingAsCompanyUser([$company], []);
    CompanyHelper::setActive([$company->getKey()]);

    if ($companyAdmin) {
        $role = Role::query()->whereRaw('LOWER(name) = ?', [mb_strtolower(Role::COMPANY_ADMIN)])->first()
            ?? Role::create(['name' => Role::COMPANY_ADMIN, 'guard_name' => 'web']);

        $user->assignRole($role);
        $user->unsetRelation('roles');

        Auth::guard('web')->setUser($user);
        Auth::guard('sanctum')->setUser($user);

        expect($user->isCompanyAdmin())->toBeTrue();
    }

    return $user;
}

/**
 * A Scribe endpoint directory holding one Sales endpoint whose texts carry
 * markup, for the escaping test.
 */
function developersFixtureDirectory(string $description): string
{
    $directory = sys_get_temp_dir().'/api-reference-'.bin2hex(random_bytes(6));
    mkdir($directory);

    file_put_contents($directory.'/00.yaml', Yaml::dump([
        'name'        => 'Sales API Management',
        'description' => '',
        'endpoints'   => [[
            'httpMethods' => ['GET'],
            'uri'         => 'admin/api/v1/sales/orders',
            'metadata'    => [
                'groupName'           => 'Sales API Management',
                'subgroup'            => 'Orders',
                'subgroupDescription' => '',
                'title'               => 'List orders',
                'description'         => $description,
                'authenticated'       => true,
            ],
            'urlParameters'       => [],
            'cleanUrlParameters'  => [],
            'queryParameters'     => [
                'status' => [
                    'name'        => 'status',
                    'description' => 'Filter.</br><b>Available options:</b> <code>draft</code>',
                    'required'    => false,
                    'type'        => 'string',
                ],
            ],
            'cleanQueryParameters' => [],
            'bodyParameters'       => [],
            'cleanBodyParameters'  => [],
            'responses'            => [[
                'status'  => 200,
                'content' => '{"data": [{"id": 1}, {"id": 2}, {"id": 3}, {"id": 4}]}',
            ]],
        ]],
    ], 10, 2));

    return $directory;
}

it('shows a company admin the Developers tab with the getting started card', function (): void {
    developersActingAs(developersCompany(), companyAdmin: true);

    Livewire::test(Help::class)
        ->assertSee(__('support::filament/pages/help-developers.tabs.developers'))
        ->call('openTab', 'developers')
        ->assertSet('tab', 'developers')
        ->assertSet('apiModule', 'sales')
        ->assertSee(__('support::filament/pages/help-developers.getting_started.base_url'))
        // Sign-in and sign-out belong to Getting started whatever the modules.
        ->assertSee('/admin/api/v1/login');
});

it('hides the Developers tab from an ordinary user and refuses it from the URL or a tampered update', function (): void {
    developersActingAs(developersCompany(), companyAdmin: false);

    $reference = app(ApiReference::class);

    expect($reference->availableModules())->toBe([])
        ->and($reference->module('sales'))->toBeNull()
        ->and($reference->authEndpoints())->toBe([]);

    Livewire::withQueryParams(['tab' => 'developers', 'module' => 'sales'])
        ->test(Help::class)
        ->assertSet('tab', null)
        ->assertSet('apiModule', null)
        ->assertDontSee(__('support::filament/pages/help-developers.tabs.developers'))
        ->assertDontSee('/admin/api/v1/sales/orders')
        ->set('tab', 'developers')
        ->set('apiModule', 'sales')
        ->assertDontSee(__('support::filament/pages/help-developers.getting_started.base_url'))
        ->assertDontSee('/admin/api/v1/sales/orders')
        ->assertDontSee('/admin/api/v1/login');
});

it('lists only the modules the current company has', function (): void {
    $company = developersCompany(withSales: true);
    developersActingAs($company, companyAdmin: true);

    $modules = array_keys(app(ApiReference::class)->availableModules());

    // Contacts and Settings are core plugins, always available; Sales is granted.
    expect($modules)->toContain('sales', 'partners', 'support');

    developersGrant($company, 'sales', false);
    app()->forgetInstance(ApiReference::class);

    expect(array_keys(app(ApiReference::class)->availableModules()))->not->toContain('sales')
        ->and(app(ApiReference::class)->module('sales'))->toBeNull();

    Livewire::withQueryParams(['tab' => 'developers', 'module' => 'sales'])
        ->test(Help::class)
        ->assertSet('tab', 'developers')
        ->assertNotSet('apiModule', 'sales')
        ->assertDontSeeHtml("selectApiModule('sales')")
        ->assertDontSee('/admin/api/v1/sales/orders');
});

it('states the real base URL, rate limit, token lifetime and current company in getting started', function (): void {
    config([
        'app.url'            => 'https://erp.example.test',
        'api.rate_limit'     => 77,
        'sanctum.expiration' => 1440,
    ]);

    $company = developersCompany();
    developersActingAs($company, companyAdmin: true);

    Livewire::withQueryParams(['tab' => 'developers'])
        ->test(Help::class)
        ->assertSee('https://erp.example.test/admin/api/v1')
        ->assertSee(trans_choice('support::filament/pages/help-developers.getting_started.rate_limit', 77, ['count' => 77]))
        ->assertSee(__('support::filament/pages/help-developers.getting_started.expiry', ['time' => '1 day']))
        ->assertSee('X-Company-Id: '.$company->getKey());
});

it('prints endpoint descriptions as text, never as markup', function (): void {
    $directory = developersFixtureDirectory('Lists orders.<script>alert(1)</script><br><b>Bold</b> &amp; more');
    app()->instance(ApiReference::class, new ApiReference($directory));

    developersActingAs(developersCompany(), companyAdmin: true);

    $component = Livewire::withQueryParams(['tab' => 'developers', 'module' => 'sales', 'resource' => 'orders'])
        ->test(Help::class)
        ->assertSee('List orders')
        ->assertSee('Available options:');

    $html = $component->html();

    expect($html)->not->toContain('<script>alert(1)</script>')
        ->and($html)->not->toContain('<b>Bold</b>')
        ->and($html)->not->toContain('<b>Available options:</b>')
        ->and($html)->toContain('Bold &amp; more')
        // A long example list is shortened, not printed whole.
        ->and($html)->toContain('… 2 more');
});

it('renders only the selected resource of the selected module', function (): void {
    developersActingAs(developersCompany(), companyAdmin: true);

    $sales = app(ApiReference::class)->module('sales');
    $resource = $sales['resources'][0];

    $html = Livewire::withQueryParams(['tab' => 'developers', 'module' => 'sales', 'resource' => $resource['key']])
        ->test(Help::class)
        ->assertSet('apiResource', $resource['key'])
        ->html();

    expect(substr_count($html, 'id="api-endpoint-'))->toBe(count($resource['endpoints']))
        ->and($html)->toContain('id="api-endpoint-'.$resource['endpoints'][0]['id'].'"')
        ->and($html)->not->toContain('id="api-endpoint-get-admin-api-v1-partners')
        ->and($html)->not->toContain('/admin/api/v1/accounts/');
});

it('opens the resource of an endpoint picked from the sidebar', function (): void {
    developersActingAs(developersCompany(), companyAdmin: true);

    $resources = app(ApiReference::class)->module('sales')['resources'];
    $last = end($resources);
    $endpoint = $last['endpoints'][0];

    Livewire::withQueryParams(['tab' => 'developers', 'module' => 'sales'])
        ->test(Help::class)
        ->call('selectApiEndpoint', $endpoint['id'])
        ->assertSet('apiResource', $last['key'])
        ->assertSeeHtml('id="api-endpoint-'.$endpoint['id'].'"');
});

it('parses the endpoint files once and serves the parsed data from the cache', function (): void {
    $directory = developersFixtureDirectory('Lists orders.');

    developersActingAs(developersCompany(), companyAdmin: true);

    $first = new ApiReference($directory);
    $first->availableModules();

    $summary = Cache::get($first->cacheKey('summary'));
    $module = Cache::get($first->cacheKey('module.sales'));

    expect($summary['fingerprint'] ?? null)->toBe($first->fingerprint())
        ->and($module['data']['resources'][0]['endpoints'][0]['title'] ?? null)->toBe('List orders');

    // A second reader with the same files reads the cache, not the YAML.
    $module['data']['resources'][0]['endpoints'][0]['title'] = 'Read from the cache';
    Cache::forever($first->cacheKey('module.sales'), $module);

    expect((new ApiReference($directory))->module('sales')['resources'][0]['endpoints'][0]['title'])
        ->toBe('Read from the cache');

    // Changed files change the fingerprint, and the stale entry is ignored.
    file_put_contents($directory.'/00.yaml', str_replace('List orders', 'All orders', file_get_contents($directory.'/00.yaml')));

    $third = new ApiReference($directory);

    expect($third->fingerprint())->not->toBe($first->fingerprint())
        ->and($third->module('sales')['resources'][0]['endpoints'][0]['title'])->toBe('All orders');
});

it('maps every Scribe group in the committed reference to a module', function (): void {
    // A group with no plugin mapping is never shown, so a new plugin API would
    // silently miss the Developers tab. This catches it when .scribe changes.
    $groups = [];

    foreach (glob(base_path('.scribe/endpoints').'/[0-9]*.yaml') as $file) {
        $groups[] = Yaml::parseFile($file)['name'];
    }

    expect($groups)->not->toBeEmpty();

    foreach ($groups as $group) {
        expect(array_key_exists($group, ApiReference::MODULES) || $group === ApiReference::AUTH_GROUP)
            ->toBeTrue("Scribe group [{$group}] has no module in ApiReference::MODULES");
    }
});
