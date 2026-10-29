<?php

use Filament\Facades\Filament;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Auth;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\User;
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
    app()->forgetInstance(CompanyContext::class);
});

afterEach(function (): void {
    Auth::forgetGuards();
    session()->forget(CompanyContext::SESSION_KEY);
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
