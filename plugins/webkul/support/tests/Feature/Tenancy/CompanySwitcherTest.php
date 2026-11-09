<?php

use Filament\Facades\Filament;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Auth;
use Webkul\Account\Models\Account;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\User;
use Webkul\Support\Models\UtmCampaign;
use Webkul\Support\Services\CompanyContext;

require_once __DIR__.'/../../Helpers/CompanyHelper.php';
require_once __DIR__.'/../../Helpers/FilamentHelper.php';
require_once __DIR__.'/../../Helpers/TestBootstrapHelper.php';

/**
 * Only installation staff may switch company (user decision, 2026-10-29).
 *
 * Clients are unrelated businesses in different sectors, so a client's own
 * users work in their default company. Super Admins and Multi-Company Admins
 * keep the switcher, within the companies they hold; the Multi-Company Admin
 * cases live in security's MultiCompanyAdminTest ("switches a Multi-Company
 * Admin to an assigned company", "returns forbidden when switching to an
 * unassigned company").
 *
 * The rule is CompanyContext::canSwitchCompanies(). It is enforced by
 * SetCompanyContextRequest, because the switch is a plain POST that anyone
 * could send by hand, and the render hook uses it only to hide the control.
 */
beforeEach(function (): void {
    TestBootstrapHelper::ensureERPInstalled();

    Auth::forgetGuards();
    session()->forget(CompanyContext::SESSION_KEY);
    session()->forget(CompanyContext::SCOPE_MODE_SESSION_KEY);
    app()->forgetInstance(CompanyContext::class);
});

afterEach(function (): void {
    Auth::forgetGuards();
    session()->forget(CompanyContext::SESSION_KEY);
    session()->forget(CompanyContext::SCOPE_MODE_SESSION_KEY);
    app()->forgetInstance(CompanyContext::class);
});

function companySwitcherMakeSuperAdmin(User $user): User
{
    $role = null;

    foreach (Role::getSuperAdminRoleNames() as $name) {
        $role = Role::query()->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])->first();

        if ($role) {
            break;
        }
    }

    expect($role)->not->toBeNull('no super-admin role exists, so this test would prove nothing');

    // The installed Admin role name does not itself grant the company-scope bypass.
    $user->assignRole($role);
    $user->unsetRelation('roles');
    app()->forgetInstance(CompanyContext::class);

    return $user;
}

function companySwitcherHeader(): string
{
    Filament::setCurrentPanel(Filament::getPanel('admin'));

    return (string) FilamentView::renderHook(PanelsRenderHook::GLOBAL_SEARCH_BEFORE);
}

it('refuses a company switch from a client user who belongs to two companies', function () {
    $companyA = CompanyHelper::company(['name' => 'Switcher Client A', 'is_active' => true]);
    $companyB = CompanyHelper::company(['name' => 'Switcher Client B', 'is_active' => true]);

    $user = CompanyHelper::actingAsCompanyUser([$companyA, $companyB], [], [$companyA->getKey()]);

    // Precondition: B is a company they hold, so the refusal is about who may
    // switch and not about membership.
    expect(app(CompanyContext::class)->allowedIds())->toContain((int) $companyB->getKey())
        ->and($user->isSuperAdmin())->toBeFalse()
        ->and($user->isMultiCompanyAdmin())->toBeFalse();

    $this->actingAs($user, 'web')
        ->post(route('company-context.set'), [
            'companies' => [$companyB->getKey()],
            'current'   => $companyB->getKey(),
        ])
        ->assertForbidden();

    expect(session(CompanyContext::SESSION_KEY))->toBe([$companyA->getKey()]);
});

it('refuses the reset action from a client user too', function () {
    $companyA = CompanyHelper::company(['name' => 'Switcher Reset A', 'is_active' => true]);

    $user = CompanyHelper::actingAsCompanyUser($companyA, [], [$companyA->getKey()]);

    $this->actingAs($user, 'web')
        ->post(route('company-context.set'), ['action' => 'reset'])
        ->assertForbidden();
});

it('refuses all-company mode for a client user', function () {
    $company = CompanyHelper::company(['name' => 'Switcher Client All', 'is_active' => true]);
    $user = CompanyHelper::actingAsCompanyUser($company, [], [$company->getKey()]);

    $this->actingAs($user, 'web')
        ->post(route('company-context.set'), ['action' => 'all'])
        ->assertForbidden();

    expect(session(CompanyContext::SCOPE_MODE_SESSION_KEY))->toBeNull();
});

it('lets a Super Admin switch between the companies they hold', function () {
    $companyA = CompanyHelper::company(['name' => 'Switcher Admin A', 'is_active' => true]);
    $companyB = CompanyHelper::company(['name' => 'Switcher Admin B', 'is_active' => true]);

    $user = companySwitcherMakeSuperAdmin(
        CompanyHelper::actingAsCompanyUser([$companyA, $companyB], [], [$companyA->getKey()]),
    );

    $this->actingAs($user, 'web')
        ->post(route('company-context.set'), [
            'companies' => [$companyB->getKey()],
            'current'   => $companyB->getKey(),
        ])
        ->assertRedirect();

    expect(session(CompanyContext::SESSION_KEY))->toBe([$companyB->getKey()]);
});

it('limits a Super Admin to one company unless they explicitly select all companies', function () {
    TestBootstrapHelper::ensurePluginInstalled('accounts');

    $companyA = CompanyHelper::company(['name' => 'Scoped Admin A', 'is_active' => true]);
    $companyB = CompanyHelper::company(['name' => 'Scoped Admin B', 'is_active' => true]);
    $companyC = CompanyHelper::company(['name' => 'Scoped Admin C', 'is_active' => true]);

    $campaignA = UtmCampaign::factory()->company($companyA)->create();
    $campaignB = UtmCampaign::factory()->company($companyB)->create();
    $campaignC = UtmCampaign::factory()->company($companyC)->create();

    $accountA = Account::factory()->create();
    $accountA->companies()->attach($companyA);
    $accountB = Account::factory()->create();
    $accountB->companies()->attach($companyB);

    $user = SecurityHelper::authenticateWithPermissions([], bypassCompanyScope: true);
    $user->forceFill(['default_company_id' => $companyA->getKey()])->saveQuietly();
    $user->allowedCompanies()->sync([$companyA->getKey(), $companyB->getKey(), $companyC->getKey()]);
    session([CompanyContext::SESSION_KEY => [$companyA->getKey(), $companyB->getKey()]]);
    app()->forgetInstance(CompanyContext::class);
    $user = companySwitcherMakeSuperAdmin($user);

    $this->actingAs($user, 'web');

    expect($user->can('bypass_company_scope'))->toBeTrue()
        ->and($user->isMultiCompanyAdmin())->toBeFalse();

    // Old sessions may contain multiple checked companies; normal mode narrows
    // them to the current company so switching never leaves a wider data view.
    expect(UtmCampaign::query()->pluck('id'))
        ->toContain($campaignA->id)
        ->not->toContain($campaignB->id)
        ->not->toContain($campaignC->id);

    $this->post(route('company-context.set'), ['action' => 'all'])
        ->assertRedirect();

    expect(UtmCampaign::query()->pluck('id'))
        ->toContain($campaignA->id)
        ->toContain($campaignB->id)
        ->toContain($campaignC->id)
        ->and(Account::query()->pluck('id'))
        ->toContain($accountA->id)
        ->toContain($accountB->id)
        ->and(companySwitcherHeader())
        ->toContain(__('support::company-switcher.all_companies'));

    $this->post(route('company-context.set'), [
        'companies' => [$companyA->getKey(), $companyB->getKey()],
        'current'   => $companyB->getKey(),
    ])->assertRedirect();

    expect(session(CompanyContext::SESSION_KEY))->toBe([$companyB->getKey()])
        ->and(app(CompanyContext::class)->allCompaniesActive())->toBeFalse()
        ->and(UtmCampaign::query()->pluck('id'))
        ->toContain($campaignB->id)
        ->not->toContain($campaignA->id)
        ->not->toContain($campaignC->id)
        ->and(Account::query()->pluck('id'))
        ->toContain($accountB->id)
        ->not->toContain($accountA->id);
});

it('shows a client user their company name but no switch form', function () {
    $companyA = CompanyHelper::company(['name' => 'Switcher Header A', 'is_active' => true]);
    $companyB = CompanyHelper::company(['name' => 'Switcher Header B', 'is_active' => true]);

    $user = CompanyHelper::actingAsCompanyUser([$companyA, $companyB], [], [$companyA->getKey()]);
    $this->actingAs($user, 'web');

    $html = companySwitcherHeader();

    expect($html)->toContain('Switcher Header A')
        ->and($html)->not->toContain(route('company-context.set'))
        ->and($html)->not->toContain('Switcher Header B');
});

it('shows a Super Admin the switch form', function () {
    $companyA = CompanyHelper::company(['name' => 'Switcher Form A', 'is_active' => true]);
    $companyB = CompanyHelper::company(['name' => 'Switcher Form B', 'is_active' => true]);

    $user = companySwitcherMakeSuperAdmin(
        CompanyHelper::actingAsCompanyUser([$companyA, $companyB], [], [$companyA->getKey()]),
    );
    $this->actingAs($user, 'web');

    $html = companySwitcherHeader();

    expect($html)->toContain(route('company-context.set'))
        ->and($html)->toContain('Switcher Form B');
});
