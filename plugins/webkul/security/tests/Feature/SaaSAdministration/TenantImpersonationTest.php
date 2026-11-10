<?php

use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Spatie\Permission\PermissionRegistrar;
use Symfony\Component\HttpFoundation\Response;
use Webkul\Chatter\Models\Message;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Filament\Resources\CompanyResource\Pages\EditCompany;
use Webkul\Security\Filament\Resources\CompanyResource\RelationManagers\UsersRelationManager;
use Webkul\Security\Http\Middleware\EnforceImpersonationSession;
use Webkul\Security\Http\Middleware\RequirePasswordChange;
use Webkul\Security\Models\Company as SecurityCompany;
use Webkul\Security\Models\Permission;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\User;
use Webkul\Security\Services\MultiCompanyAdminRoleProvisioner;
use Webkul\Security\Services\TenantImpersonation;
use Webkul\Support\Models\Company;
use Webkul\Support\Services\CompanyContext;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';

/**
 * Tenant impersonation (SAAS-15, user decisions 2026-11-10): Super Admins by
 * default, Multi-Company Admins only with an explicit grant in an assigned
 * company; tenant users only as targets; own password and a reason to start;
 * 60 minutes; nothing extra blocked, every write logged with the
 * administrator's id.
 */
beforeEach(function (): void {
    TestBootstrapHelper::ensureERPInstalled();
    app(MultiCompanyAdminRoleProvisioner::class)->provision();
    Auth::forgetGuards();
    session()->forget([TenantImpersonation::SESSION_KEY, CompanyContext::SESSION_KEY, CompanyContext::SCOPE_MODE_SESSION_KEY]);
    app()->forgetInstance(CompanyContext::class);
    app(PermissionRegistrar::class)->forgetCachedPermissions();
    Filament::setCurrentPanel(Filament::getPanel('admin'));
});

afterEach(function (): void {
    session()->forget(TenantImpersonation::SESSION_KEY);
    Auth::forgetGuards();
});

const IMPERSONATION_PASSWORD = 'Staff-password-42';

function impersonationCompany(): Company
{
    return Company::factory()->create([
        'name'      => 'Impersonation '.fake()->unique()->numerify('####'),
        'is_active' => true,
        'phone'     => '+260971234567',
        'mobile'    => '+260961234567',
    ]);
}

function impersonationUser(Company|array $companies, array $roles = [], array $attributes = []): User
{
    $companies = collect(is_array($companies) ? $companies : [$companies]);

    $user = User::withoutEvents(fn (): User => User::factory()->create(array_merge([
        'default_company_id'  => $companies->first()->getKey(),
        'resource_permission' => PermissionType::GLOBAL,
        'is_active'           => true,
        'is_default'          => false,
        'password'            => Hash::make(IMPERSONATION_PASSWORD),
    ], $attributes)));

    $user->allowedCompanies()->sync($companies->pluck('id')->all());
    $user->syncRoles(collect($roles)->pluck('id')->all());
    $user->unsetRelation('roles');

    return $user;
}

function impersonationTenantRole(array $permissions = []): Role
{
    $role = Role::query()->create(['name' => 'Tenant '.fake()->unique()->numerify('####'), 'guard_name' => 'web']);
    $role->syncPermissions(collect($permissions)->map(fn (string $name): Permission => Permission::query()->firstOrCreate(['name' => $name, 'guard_name' => 'web'])));
    app(PermissionRegistrar::class)->forgetCachedPermissions();

    return $role;
}

function impersonationMultiCompanyAdmin(Company|array $companies): User
{
    $role = Role::query()->whereRaw('LOWER(name) = ?', [mb_strtolower(Role::MULTI_COMPANY_ADMIN)])->firstOrFail();

    return impersonationUser($companies, [$role]);
}

function impersonationSuperAdmin(): User
{
    $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();
    $admin->forceFill(['password' => Hash::make(IMPERSONATION_PASSWORD)])->saveQuietly();

    return $admin->fresh();
}

function impersonationSignIn(User $user, array $companyIds = []): void
{
    Auth::forgetGuards();
    Auth::shouldUse('web');
    Auth::guard('web')->login($user);
    session([CompanyContext::SESSION_KEY => $companyIds]);
    app()->forgetInstance(CompanyContext::class);
}

function impersonationService(): TenantImpersonation
{
    return app(TenantImpersonation::class);
}

function impersonationStart(User $actor, User $target, Company $company, string $password = IMPERSONATION_PASSWORD, string $reason = 'Client reports a missing invoice'): void
{
    impersonationService()->start($actor, $target, $company, $password, $reason);
}

function impersonationRefusal(Closure $start): string
{
    try {
        $start();
    } catch (AuthorizationException $exception) {
        return $exception->getMessage();
    } catch (ValidationException $exception) {
        return (string) collect($exception->errors())->flatten()->first();
    }

    throw new RuntimeException('The impersonation was not refused.');
}

function impersonationEndMiddleware(): Response
{
    return app(EnforceImpersonationSession::class)->handle(Request::create('/admin'), fn () => response('passed'));
}

/**
 * InstallERP syncs every permission onto the Admin role, which is a Super
 * Admin role, so it holds this one too. That grants nothing new: a Super
 * Admin impersonates without it. What must hold is that no other role and
 * no user starts with it.
 */
it('creates the impersonation permission and gives it to no one but super admin roles', function () {
    $permission = Permission::query()->where('name', TenantImpersonation::PERMISSION)->where('guard_name', 'web')->first();
    $superAdminNames = Role::getSuperAdminRoleNames();

    expect($permission)->not->toBeNull()
        ->and($permission->users()->count())->toBe(0)
        ->and($permission->roles()->pluck('name')
            ->reject(fn (string $name): bool => in_array(mb_strtolower(trim($name)), $superAdminNames, true))
            ->all())->toBe([]);
});

it('lets a super admin impersonate a tenant user, in that company, with the target’s own permissions', function () {
    $company = impersonationCompany();
    $other = impersonationCompany();
    $target = impersonationUser([$company, $other], [impersonationTenantRole()]);
    $admin = impersonationSuperAdmin();
    impersonationSignIn($admin, [(int) $other->getKey()]);

    impersonationStart($admin, $target, $company);

    $state = impersonationService()->state();

    expect(Auth::guard('web')->id())->toBe($target->getKey())
        ->and($state['impersonator_id'])->toBe((int) $admin->getKey())
        ->and($state['company_id'])->toBe((int) $company->getKey())
        ->and(app(CompanyContext::class)->currentId())->toBe((int) $company->getKey())
        // The super admin's own Gate bypass does not ride along.
        ->and(Auth::user()->isSuperAdmin())->toBeFalse()
        ->and(Gate::allows('view_any_support_calendar'))->toBeFalse()
        ->and(Message::withoutGlobalScopes()
            ->where('messageable_id', $target->getKey())
            ->where('event', 'security.impersonation_started')
            ->where('causer_id', $admin->getKey())
            ->exists())->toBeTrue();
});

it('requires the administrator’s own password and a reason, with pinned messages', function () {
    $company = impersonationCompany();
    $target = impersonationUser($company, [impersonationTenantRole()]);
    $admin = impersonationSuperAdmin();
    impersonationSignIn($admin);

    expect(impersonationRefusal(fn () => impersonationStart($admin, $target, $company, 'wrong-password')))->toBe(TenantImpersonation::WRONG_PASSWORD)
        ->and(impersonationRefusal(fn () => impersonationStart($admin, $target, $company, reason: '   ')))->toBe(TenantImpersonation::REASON_REQUIRED)
        ->and(Auth::guard('web')->id())->toBe($admin->getKey())
        ->and(impersonationService()->isActive())->toBeFalse();
});

it('lets a multi-company admin impersonate only with the grant and only in an assigned company', function () {
    $assigned = impersonationCompany();
    $unassigned = impersonationCompany();
    $mca = impersonationMultiCompanyAdmin($assigned);
    $target = impersonationUser($assigned, [impersonationTenantRole()]);
    $outsider = impersonationUser($unassigned, [impersonationTenantRole()]);
    impersonationSignIn($mca, [(int) $assigned->getKey()]);

    expect(impersonationRefusal(fn () => impersonationStart($mca, $target, $assigned)))->toBe(TenantImpersonation::NOT_ALLOWED);

    impersonationSignIn(impersonationSuperAdmin());
    impersonationService()->grant(impersonationSuperAdmin(), $mca);
    impersonationSignIn($mca->fresh(), [(int) $assigned->getKey()]);

    expect(impersonationRefusal(fn () => impersonationStart($mca->fresh(), $outsider, $unassigned)))->toBe(TenantImpersonation::NOT_ALLOWED);

    impersonationStart($mca->fresh(), $target, $assigned);

    expect(Auth::guard('web')->id())->toBe($target->getKey());
});

it('refuses anyone but installation staff, whatever permission they hold', function () {
    $company = impersonationCompany();
    $holder = impersonationUser($company, [impersonationTenantRole([TenantImpersonation::PERMISSION])]);
    $target = impersonationUser($company, [impersonationTenantRole()]);
    impersonationSignIn($holder, [(int) $company->getKey()]);

    expect(impersonationRefusal(fn () => impersonationStart($holder, $target, $company)))->toBe(TenantImpersonation::NOT_ALLOWED);
});

it('refuses a target who is staff, the administrator, inactive or not a member', function () {
    $company = impersonationCompany();
    $admin = impersonationSuperAdmin();
    impersonationSignIn($admin);

    $targets = [
        'super admin'         => $admin,
        'multi-company admin' => impersonationMultiCompanyAdmin($company),
        'inactive'            => impersonationUser($company, [impersonationTenantRole()], ['is_active' => false]),
        'outside the company' => impersonationUser(impersonationCompany(), [impersonationTenantRole()]),
    ];

    foreach ($targets as $label => $target) {
        expect(impersonationRefusal(fn () => impersonationStart($admin, $target, $company)))
            ->toBe(TenantImpersonation::NOT_ELIGIBLE, $label);
    }
});

it('refuses a second impersonation while one is running', function () {
    $company = impersonationCompany();
    $first = impersonationUser($company, [impersonationTenantRole()]);
    $second = impersonationUser($company, [impersonationTenantRole()]);
    $admin = impersonationSuperAdmin();
    impersonationSignIn($admin);
    impersonationStart($admin, $first, $company);

    expect(impersonationRefusal(fn () => impersonationStart($admin, $second, $company)))->toBe(TenantImpersonation::ALREADY_ACTIVE);
});

it('ends by signing the administrator back in with their own company selection, and records it', function () {
    $company = impersonationCompany();
    $own = impersonationCompany();
    $target = impersonationUser($company, [impersonationTenantRole()]);
    $admin = impersonationSuperAdmin();
    impersonationSignIn($admin, [(int) $own->getKey()]);
    impersonationStart($admin, $target, $company);

    $returned = impersonationService()->end();

    expect($returned?->getKey())->toBe($admin->getKey())
        ->and(Auth::guard('web')->id())->toBe($admin->getKey())
        ->and(session(CompanyContext::SESSION_KEY))->toBe([(int) $own->getKey()])
        ->and(impersonationService()->isActive())->toBeFalse()
        ->and(Message::withoutGlobalScopes()
            ->where('messageable_id', $target->getKey())
            ->where('event', 'security.impersonation_ended')
            ->exists())->toBeTrue();
});

it('ends an impersonation after sixty minutes on the next panel request', function () {
    $company = impersonationCompany();
    $target = impersonationUser($company, [impersonationTenantRole()]);
    $admin = impersonationSuperAdmin();
    impersonationSignIn($admin);
    impersonationStart($admin, $target, $company);

    $this->travel(59)->minutes();
    expect(impersonationEndMiddleware()->getContent())->toBe('passed')
        ->and(Auth::guard('web')->id())->toBe($target->getKey());

    $this->travel(2)->minutes();
    $response = impersonationEndMiddleware();

    expect($response->isRedirect())->toBeTrue()
        ->and(Auth::guard('web')->id())->toBe($admin->getKey())
        ->and(impersonationService()->isActive())->toBeFalse();
});

it('ends an impersonation at once when the multi-company admin’s grant is revoked', function () {
    $company = impersonationCompany();
    $mca = impersonationMultiCompanyAdmin($company);
    $target = impersonationUser($company, [impersonationTenantRole()]);
    $admin = impersonationSuperAdmin();
    impersonationSignIn($admin);
    impersonationService()->grant($admin, $mca);
    impersonationSignIn($mca->fresh(), [(int) $company->getKey()]);
    impersonationStart($mca->fresh(), $target, $company);

    $mca->fresh()->revokePermissionTo(TenantImpersonation::PERMISSION);
    app(PermissionRegistrar::class)->forgetCachedPermissions();

    expect(impersonationEndMiddleware()->isRedirect())->toBeTrue()
        ->and(Auth::guard('web')->id())->toBe($mca->getKey())
        ->and(impersonationService()->isActive())->toBeFalse();
});

it('lets only a super admin grant impersonation, and only to a multi-company admin', function () {
    $company = impersonationCompany();
    $mca = impersonationMultiCompanyAdmin($company);
    $otherMca = impersonationMultiCompanyAdmin($company);
    $tenant = impersonationUser($company, [impersonationTenantRole()]);
    $admin = impersonationSuperAdmin();

    expect(impersonationRefusal(fn () => impersonationService()->grant($mca, $otherMca)))->toBe(TenantImpersonation::ONLY_SUPER_ADMIN_GRANTS)
        ->and(impersonationRefusal(fn () => impersonationService()->grant($admin, $tenant)))->toBe(TenantImpersonation::ONLY_MULTI_COMPANY_ADMIN);

    impersonationService()->grant($admin, $mca);
    expect(impersonationService()->holdsGrant($mca->fresh()))->toBeTrue();

    impersonationService()->revoke($admin, $mca->fresh());
    expect(impersonationService()->holdsGrant($mca->fresh()))->toBeFalse();
});

it('does not hold the administrator on the target’s change-password page', function () {
    $company = impersonationCompany();
    $target = impersonationUser($company, [impersonationTenantRole()], ['must_change_password' => true]);
    $admin = impersonationSuperAdmin();
    impersonationSignIn($admin);
    impersonationStart($admin, $target, $company);

    $response = app(RequirePasswordChange::class)->handle(Request::create('/admin'), fn () => response('passed'));

    expect($response->getContent())->toBe('passed');
});

it('logs every write made while impersonating with the administrator’s id', function () {
    $company = impersonationCompany();
    $target = impersonationUser($company, [impersonationTenantRole()]);
    $admin = impersonationSuperAdmin();
    impersonationSignIn($admin);
    impersonationStart($admin, $target, $company);

    Log::spy();
    $target->forceFill(['name' => 'Renamed while impersonated'])->save();

    Log::shouldHaveReceived('notice')->withArgs(fn (string $message, array $context = []): bool => $message === 'Write during impersonation.'
        && $context['model'] === User::class
        && $context['impersonator_id'] === (int) $admin->getKey())->atLeast()->once();
});

it('records the end when the impersonated session logs out', function () {
    $company = impersonationCompany();
    $target = impersonationUser($company, [impersonationTenantRole()]);
    $admin = impersonationSuperAdmin();
    impersonationSignIn($admin);
    impersonationStart($admin, $target, $company);

    Auth::guard('web')->logout();

    expect(impersonationService()->isActive())->toBeFalse()
        ->and(Message::withoutGlobalScopes()
            ->where('messageable_id', $target->getKey())
            ->where('event', 'security.impersonation_ended')
            ->where('properties->after->end_reason', 'logout')
            ->exists())->toBeTrue();
});

it('shows a banner naming both people while impersonating, and nothing otherwise', function () {
    $company = impersonationCompany();
    $target = impersonationUser($company, [impersonationTenantRole()]);
    $admin = impersonationSuperAdmin();
    impersonationSignIn($admin);

    expect((string) FilamentView::renderHook(PanelsRenderHook::BODY_START))->not->toContain('data-impersonation-banner');

    impersonationStart($admin, $target, $company);
    $banner = (string) FilamentView::renderHook(PanelsRenderHook::BODY_START);

    expect($banner)->toContain('data-impersonation-banner')
        ->and($banner)->toContain(e($target->name))
        ->and($banner)->toContain(e($admin->name))
        ->and($banner)->toContain(route('security.impersonation.end'))
        ->and(substr_count($banner, 'data-impersonation-banner'))->toBe(1);
});

it('ends only on a POST to the end route', function () {
    $company = impersonationCompany();
    $target = impersonationUser($company, [impersonationTenantRole()]);
    $admin = impersonationSuperAdmin();
    impersonationSignIn($admin);
    impersonationStart($admin, $target, $company);

    $this->get(route('security.impersonation.end'))->assertMethodNotAllowed();
    expect(impersonationService()->isActive())->toBeTrue();

    $this->post(route('security.impersonation.end'))->assertRedirect();
    expect(impersonationService()->isActive())->toBeFalse()
        ->and(Auth::guard('web')->id())->toBe($admin->getKey());
});

it('starts from the company’s Users list for a super admin and is hidden from a multi-company admin without the grant', function () {
    $company = SecurityCompany::query()->findOrFail(impersonationCompany()->getKey());
    $target = impersonationUser($company, [impersonationTenantRole()]);
    $admin = impersonationSuperAdmin();
    $mca = impersonationMultiCompanyAdmin($company);

    impersonationSignIn($mca, [(int) $company->getKey()]);
    Livewire::test(UsersRelationManager::class, ['ownerRecord' => $company, 'pageClass' => EditCompany::class])
        ->assertActionHidden(TestAction::make('impersonate')->table($target));

    impersonationSignIn($admin, [(int) $company->getKey()]);
    Livewire::test(UsersRelationManager::class, ['ownerRecord' => $company, 'pageClass' => EditCompany::class])
        ->assertActionHidden(TestAction::make('impersonate')->table($mca))
        ->callAction(TestAction::make('impersonate')->table($target), data: [
            'reason'   => 'Reproducing a report',
            'password' => 'wrong-password',
        ])
        ->assertHasFormErrors(['password']);

    expect(impersonationService()->isActive())->toBeFalse();

    Livewire::test(UsersRelationManager::class, ['ownerRecord' => $company, 'pageClass' => EditCompany::class])
        ->callAction(TestAction::make('impersonate')->table($target), data: [
            'reason'   => 'Reproducing a report',
            'password' => IMPERSONATION_PASSWORD,
        ])
        ->assertHasNoFormErrors()
        ->assertRedirect();

    expect(Auth::guard('web')->id())->toBe($target->getKey());
});
