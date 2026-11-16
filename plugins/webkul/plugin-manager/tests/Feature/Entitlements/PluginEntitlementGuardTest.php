<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Webkul\PluginManager\Exceptions\PluginNotEntitledException;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Support\PluginAccess;
use Webkul\PluginManager\Support\PluginEntitlement;
use Webkul\Security\Models\Role;
use Webkul\Support\Models\Company;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Webkul\Support\Services\CompanyContext;
use Webkul\Website\Http\Middleware\EnsureCustomerPortalIsEntitled;
use Webkul\Website\Models\Partner as Customer;

require_once __DIR__.'/../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';

/**
 * SAAS-04: one enforcement boundary for module entitlement.
 *
 * Before this, revoking a company's grant changed a menu and nothing else - the
 * Sales API still answered 200 (see
 * PluginEnforcementCharacterizationTest, which SAAS-04 was written to invert).
 *
 * The guard is applied to every plugin's `routes/api.php` centrally in
 * PackageServiceProvider, so a plugin cannot forget it. Core plugins are exempt
 * (they are the installation, not a module), a super admin keeps recovery
 * access, and "no active company" is denied rather than allowed.
 */
beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('sales');
    PluginAccess::flush();
});

function pluginGuardGrant(Company|int|null $companyId, string $plugin = 'sales'): void
{
    $id = $companyId instanceof Company ? (int) $companyId->getKey() : (int) $companyId;

    $pluginId = Plugin::query()->where('name', $plugin)->value('id');

    if (! $pluginId) {
        return;
    }

    DB::table('company_plugin')->insertOrIgnore([
        'company_id' => $id,
        'plugin_id'  => $pluginId,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    PluginAccess::flush();
}

function pluginGuardRevoke(int $companyId, string $plugin = 'sales'): void
{
    $pluginId = Plugin::query()->where('name', $plugin)->value('id');

    if (! $pluginId) {
        return;
    }

    DB::table('company_plugin')
        ->where('company_id', $companyId)
        ->where('plugin_id', $pluginId)
        ->delete();

    PluginAccess::flush();
}

it('allows a sales API request while the company holds the grant', function () {
    $user = SecurityHelper::authenticateWithPermissions(['view_any_sale_order']);
    $companyId = (int) $user->default_company_id;

    pluginGuardGrant($companyId);

    expect($companyId)->toBeGreaterThan(0)
        ->and(PluginAccess::enabledFor('sales', $companyId))->toBeTrue();

    $this->getJson(route('admin.api.v1.sales.orders.index'))->assertOk();
});

it('denies a sales API request once the company loses the grant', function () {
    $user = SecurityHelper::authenticateWithPermissions(['view_any_sale_order']);
    $companyId = (int) $user->default_company_id;

    pluginGuardGrant($companyId);
    pluginGuardRevoke($companyId);

    expect(PluginAccess::enabledFor('sales', $companyId))->toBeFalse();

    // The inversion of SAAS-00's characterization: a revoked module stops
    // answering instead of merely disappearing from navigation.
    $this->getJson(route('admin.api.v1.sales.orders.index'))->assertForbidden();
});

it('exempts a core plugin from the guard', function () {
    Route::middleware('plugin.entitlement:support')->get('/__plugin_guard_core', fn () => response('ok'));

    // Unauthenticated, no company: still allowed, because `support` is part of
    // the installation and gating it would take away the pages that manage it.
    expect(PluginEntitlement::isExempt('support'))->toBeTrue();

    $this->get('/__plugin_guard_core')->assertOk();
});

/**
 * Contacts installs like a module, but every business plugin reads it, so a
 * company with it revoked would be broken rather than restricted. User
 * decision, 2026-11-05 (SAAS-05 review finding 4).
 */
it('treats contacts as always available, whatever a company was granted', function () {
    $company = Company::factory()->create();

    expect(PluginEntitlement::isExempt('contacts'))->toBeTrue()
        ->and(PluginEntitlement::enabledForCompany('contacts', $company->getKey()))->toBeTrue();
});

it('fails closed when an authenticated user has no active company', function () {
    $user = SecurityHelper::authenticateWithPermissions([]);

    $user->forceFill(['default_company_id' => null])->saveQuietly();
    $user->allowedCompanies()->sync([]);
    app()->forgetInstance(CompanyContext::class);

    expect(app(CompanyContext::class)->currentId())->toBeNull();

    Route::middleware('plugin.entitlement:sales')->get('/__plugin_guard_no_company', fn () => response('ok'));

    $this->getJson('/__plugin_guard_no_company')->assertForbidden();
});

it('leaves an unauthenticated request to the route authentication', function () {
    Route::middleware(['plugin.entitlement:sales', 'auth:sanctum'])->get('/__plugin_guard_guest', fn () => response('ok'));

    // 401 from auth:sanctum, not 403 from the guard: the client needs to know to
    // authenticate, and nothing behind the route is reachable either way.
    $this->getJson('/__plugin_guard_guest')->assertUnauthorized();
});

it('lets the installation administrator through', function () {
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

    Route::middleware('plugin.entitlement:sales')->get('/__plugin_guard_super_admin', fn () => response('ok'));

    $this->actingAs($user)->get('/__plugin_guard_super_admin')->assertOk();
});

it('asserts a record company entitlement for services and jobs', function () {
    $entitled = Company::factory()->create(['is_active' => true]);
    $notEntitled = Company::factory()->create(['is_active' => true]);

    pluginGuardGrant($entitled);

    // Company::created provisions every installed plugin, so "not entitled" has
    // to be stated explicitly or this test would assert the opposite of its
    // name - the same trap a factory default set for another package.
    pluginGuardRevoke((int) $notEntitled->getKey());

    expect(PluginAccess::enabledFor('sales', (int) $entitled->getKey()))->toBeTrue()
        ->and(PluginAccess::enabledFor('sales', (int) $notEntitled->getKey()))->toBeFalse();

    PluginEntitlement::assertForCompany('sales', (int) $entitled->getKey());

    expect(fn () => PluginEntitlement::assertForCompany('sales', (int) $notEntitled->getKey()))
        ->toThrow(PluginNotEntitledException::class);

    expect(fn () => PluginEntitlement::assertForCompany('sales', null))
        ->toThrow(PluginNotEntitledException::class);
});

/**
 * SAAS-05 review finding 1. A shared customer (no company, §4k) belongs to no
 * tenant, so no company's Website switch may lock them out of the portal; a
 * company's own customer still follows that company's grant. User decision,
 * 2026-11-05. The middleware is not yet registered on the customer panel,
 * which has no Authenticate middleware to run after (see the SaaS plan).
 */
it('lets a shared customer through the portal guard and holds a company customer to its grant', function () {
    // Website is deliberately not installed in this suite, so the company
    // below holds no Website grant and its customer must be refused.
    $company = Company::factory()->create();

    $guard = new EnsureCustomerPortalIsEntitled;
    $next = fn () => response('ok');

    $shared = new Customer;
    $shared->company_id = null;
    Auth::guard('customer')->setUser($shared);

    expect($guard->handle(request(), $next)->getContent())->toBe('ok');

    $owned = new Customer;
    $owned->company_id = $company->getKey();
    Auth::guard('customer')->setUser($owned);

    expect(fn () => $guard->handle(request(), $next))
        ->toThrow(HttpException::class);
});
