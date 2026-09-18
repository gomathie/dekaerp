<?php

use AnourValar\EloquentSerialize\Facades\EloquentSerializeFacade;
use Filament\Facades\Filament;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Spatie\Permission\PermissionRegistrar;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Filament\Resources\CompanyResource as SecurityCompanyResource;
use Webkul\Security\Filament\Resources\UserResource;
use Webkul\Security\Filament\Resources\UserResource\Pages\CreateUser;
use Webkul\Security\Filament\Resources\UserResource\Pages\EditUser;
use Webkul\Security\Models\Invitation;
use Webkul\Security\Models\Permission;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\User;
use Webkul\Security\Policies\RolePolicy;
use Webkul\Security\Policies\UserPolicy;
use Webkul\Security\Services\MultiCompanyAdminRoleProvisioner;
use Webkul\Security\Services\MultiCompanyAdminService;
use Webkul\Security\Services\UserInvitationService;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\UtmCampaign;
use Webkul\Support\Services\CompanyContext;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function (): void {
    Auth::forgetGuards();
    session()->forget(CompanyContext::SESSION_KEY);
    app()->forgetInstance(CompanyContext::class);
    app(PermissionRegistrar::class)->forgetCachedPermissions();
    app(MultiCompanyAdminRoleProvisioner::class)->provision();
    URL::resolveMissingNamedRoutesUsing(fn (): string => '#');
});

afterEach(function (): void {
    Auth::forgetGuards();
    session()->forget(CompanyContext::SESSION_KEY);
    app()->forgetInstance(CompanyContext::class);
    app(PermissionRegistrar::class)->forgetCachedPermissions();
});

function multiCompanyAdminTestCompany(?string $name = null): Company
{
    return Company::factory()->create([
        'name'      => $name ?? 'Company '.fake()->unique()->numerify('####'),
        'is_active' => true,
    ]);
}

function multiCompanyAdminTestRole(array $permissionNames = [], ?string $name = null): Role
{
    $role = Role::query()->create([
        'name'       => $name ?? 'Company Role '.fake()->unique()->numerify('####'),
        'guard_name' => 'web',
        'is_default' => false,
    ]);

    $permissions = collect($permissionNames)->map(
        fn (string $permissionName): Permission => Permission::query()->firstOrCreate([
            'name'       => $permissionName,
            'guard_name' => 'web',
        ]),
    );

    $role->syncPermissions($permissions);
    app(PermissionRegistrar::class)->forgetCachedPermissions();

    return $role;
}

function multiCompanyAdminTestUser(
    Company|array $companies,
    Role|array|null $roles = null,
    array $attributes = [],
): User {
    $companies = collect(is_array($companies) ? $companies : [$companies]);
    $roles = collect(match (true) {
        $roles instanceof Role => [$roles],
        is_array($roles)       => $roles,
        default               => [multiCompanyAdminTestRole()],
    });

    $user = User::withoutEvents(fn (): User => User::factory()->create(array_merge([
        'default_company_id' => $companies->first()->getKey(),
        'resource_permission' => PermissionType::INDIVIDUAL,
        'is_active'          => true,
        'is_default'         => false,
    ], $attributes)));

    $user->allowedCompanies()->sync($companies->pluck('id')->all());
    $user->syncRoles($roles->pluck('id')->all());
    $user->unsetRelation('roles');

    return $user;
}

function multiCompanyAdminTestAdministrator(
    Company|array $companies,
    array $additionalRoles = [],
    array $attributes = [],
): User {
    $role = Role::query()
        ->whereRaw('LOWER(name) = ?', [mb_strtolower(Role::MULTI_COMPANY_ADMIN)])
        ->firstOrFail();

    return multiCompanyAdminTestUser(
        $companies,
        [$role, ...$additionalRoles],
        array_merge(['resource_permission' => PermissionType::GLOBAL], $attributes),
    );
}

function multiCompanyAdminTestSuperAdmin(): User
{
    return User::query()->where('email', 'admin@example.com')->firstOrFail();
}

function multiCompanyAdminTestAuthenticate(User $user, array $activeCompanyIds): void
{
    Auth::forgetGuards();
    Auth::shouldUse('web');
    Auth::guard('web')->login($user);
    session([CompanyContext::SESSION_KEY => $activeCompanyIds]);
    app()->forgetInstance(CompanyContext::class);
}

function multiCompanyAdminTestGrantPermissions(User $user, array $permissionNames): void
{
    $permissions = collect($permissionNames)->map(
        fn (string $permissionName): Permission => Permission::query()->firstOrCreate([
            'name'       => $permissionName,
            'guard_name' => 'web',
        ]),
    );

    $user->givePermissionTo($permissions);
    app(PermissionRegistrar::class)->forgetCachedPermissions();
    $user->unsetRelation('permissions');
}

it('provisions the Multi-Company Admin role exactly once', function () {
    $role = app(MultiCompanyAdminRoleProvisioner::class)->provision();

    expect($role->isMultiCompanyAdminRole())->toBeTrue()
        ->and(Role::query()
            ->where('guard_name', 'web')
            ->whereRaw('LOWER(name) = ?', [mb_strtolower(Role::MULTI_COMPANY_ADMIN)])
            ->count())->toBe(1);
});

it('can rerun provisioning without duplicate roles or permissions', function () {
    $provisioner = app(MultiCompanyAdminRoleProvisioner::class);
    $first = $provisioner->provision();
    $second = $provisioner->provision();

    expect($second->is($first))->toBeTrue()
        ->and($second->permissions()->whereIn('name', MultiCompanyAdminRoleProvisioner::BASE_PERMISSIONS)->count())
        ->toBe(count(MultiCompanyAdminRoleProvisioner::BASE_PERMISSIONS))
        ->and(DB::table('role_has_permissions')->where('role_id', $second->getKey())->count())
        ->toBe(count(MultiCompanyAdminRoleProvisioner::BASE_PERMISSIONS));
});

it('blocks non-super users from creating a protected role variant', function () {
    $company = multiCompanyAdminTestCompany();
    $ordinary = multiCompanyAdminTestUser($company);
    multiCompanyAdminTestAuthenticate($ordinary, [$company->getKey()]);

    expect(fn () => Role::query()->create([
        'name'       => mb_strtolower(Role::MULTI_COMPANY_ADMIN),
        'guard_name' => 'web',
    ]))->toThrow(AuthorizationException::class);
});

it('allows only a Super Admin to assign the Multi-Company Admin role', function () {
    $company = multiCompanyAdminTestCompany();
    $ordinary = multiCompanyAdminTestUser($company);
    $multiCompanyRole = app(MultiCompanyAdminRoleProvisioner::class)->provision();
    $service = app(MultiCompanyAdminService::class);

    expect(fn () => $service->assertUserAssignment(
        $ordinary,
        null,
        [$multiCompanyRole->getKey()],
        [$company->getKey()],
        $company->getKey(),
    ))->toThrow(ValidationException::class);

    $service->assertUserAssignment(
        multiCompanyAdminTestSuperAdmin(),
        null,
        [$multiCompanyRole->getKey()],
        [$company->getKey()],
        $company->getKey(),
    );

    expect(true)->toBeTrue();
});

it('allows only a Super Admin to change a Multi-Company Admin company assignment', function () {
    $companyA = multiCompanyAdminTestCompany();
    $companyB = multiCompanyAdminTestCompany();
    $ordinaryRole = multiCompanyAdminTestRole(['view_security_user', 'update_security_user']);
    $ordinary = multiCompanyAdminTestUser($companyA, $ordinaryRole);
    $administrator = multiCompanyAdminTestAdministrator($companyA, attributes: [
        'creator_id' => $ordinary->getKey(),
    ]);
    $multiCompanyRole = app(MultiCompanyAdminRoleProvisioner::class)->provision();
    $service = app(MultiCompanyAdminService::class);
    multiCompanyAdminTestAuthenticate($ordinary, [$companyA->getKey()]);

    expect(app(UserPolicy::class)->view($ordinary, $administrator))->toBeFalse()
        ->and(app(UserPolicy::class)->update($ordinary, $administrator))->toBeFalse()
        ->and(UserResource::getEloquentQuery()->whereKey($administrator->getKey())->exists())->toBeFalse();

    expect(fn () => $service->assertUserAssignment(
        $ordinary,
        $administrator,
        [$multiCompanyRole->getKey()],
        [$companyA->getKey(), $companyB->getKey()],
        $companyA->getKey(),
    ))->toThrow(ValidationException::class);

    $service->assertUserAssignment(
        multiCompanyAdminTestSuperAdmin(),
        $administrator,
        [$multiCompanyRole->getKey()],
        [$companyA->getKey(), $companyB->getKey()],
        $companyA->getKey(),
    );

    expect(true)->toBeTrue();
});

it('hides the protected role from an ordinary Company Admin', function () {
    $company = multiCompanyAdminTestCompany();
    $roleManager = multiCompanyAdminTestRole(['view_any_role', 'view_role']);
    $ordinary = multiCompanyAdminTestUser($company, $roleManager);
    $multiCompanyRole = app(MultiCompanyAdminRoleProvisioner::class)->provision();
    $service = app(MultiCompanyAdminService::class);

    expect(app(RolePolicy::class)->view($ordinary, $multiCompanyRole))->toBeFalse()
        ->and($service->scopeAssignableRoles(Role::query(), $ordinary)
            ->whereKey($multiCompanyRole->getKey())
            ->exists())->toBeFalse();
});

it('shows a Multi-Company Admin only explicitly assigned companies', function () {
    $companyA = multiCompanyAdminTestCompany();
    $companyB = multiCompanyAdminTestCompany();
    $companyC = multiCompanyAdminTestCompany();
    $administrator = multiCompanyAdminTestAdministrator([$companyA, $companyB]);
    multiCompanyAdminTestAuthenticate($administrator, [$companyA->getKey()]);

    expect(app(CompanyContext::class)->allowedIds())->toEqualCanonicalizing([
        $companyA->getKey(),
        $companyB->getKey(),
    ])->not->toContain($companyC->getKey());
});

it('switches a Multi-Company Admin to an assigned company', function () {
    $companyA = multiCompanyAdminTestCompany();
    $companyB = multiCompanyAdminTestCompany();
    $administrator = multiCompanyAdminTestAdministrator([$companyA, $companyB]);
    multiCompanyAdminTestAuthenticate($administrator, [$companyA->getKey()]);

    $this->post(route('company-context.set'), [
        'companies' => [$companyB->getKey()],
        'current'   => $companyB->getKey(),
    ])->assertRedirect();

    expect(session(CompanyContext::SESSION_KEY))->toBe([$companyB->getKey()])
        ->and(app(CompanyContext::class)->currentId())->toBe($companyB->getKey());
});

it('returns forbidden when switching to an unassigned company', function () {
    $companyA = multiCompanyAdminTestCompany();
    $companyB = multiCompanyAdminTestCompany();
    $administrator = multiCompanyAdminTestAdministrator($companyA);
    multiCompanyAdminTestAuthenticate($administrator, [$companyA->getKey()]);

    $this->post(route('company-context.set'), [
        'companies' => [$companyB->getKey()],
        'current'   => $companyB->getKey(),
    ])->assertForbidden();

    expect(session(CompanyContext::SESSION_KEY))->toBe([$companyA->getKey()]);
});

it('does not resolve a manually supplied unassigned company ID', function () {
    $companyA = multiCompanyAdminTestCompany();
    $companyB = multiCompanyAdminTestCompany();
    $administrator = multiCompanyAdminTestAdministrator($companyA);
    multiCompanyAdminTestAuthenticate($administrator, [$companyA->getKey()]);

    expect(Company::query()->find($companyB->getKey()))->toBeNull()
        ->and(SecurityCompanyResource::getEloquentQuery()->pluck('companies.id')->all())
        ->toContain($companyA->getKey())
        ->not->toContain($companyB->getKey());
});

it('can view users belonging to the active assigned company', function () {
    $companyA = multiCompanyAdminTestCompany();
    $companyB = multiCompanyAdminTestCompany();
    $administrator = multiCompanyAdminTestAdministrator([$companyA, $companyB]);
    $userA = multiCompanyAdminTestUser($companyA);
    $userB = multiCompanyAdminTestUser($companyB);
    multiCompanyAdminTestAuthenticate($administrator, [$companyA->getKey()]);

    $visibleIds = UserResource::getEloquentQuery()->pluck('users.id')->all();

    expect($visibleIds)->toContain($userA->getKey())
        ->not->toContain($userB->getKey())
        ->and(app(UserPolicy::class)->view($administrator, $userA))->toBeTrue();
});

it('can invite a user into an assigned company with a permitted company role', function () {
    $company = multiCompanyAdminTestCompany();
    $companyRole = multiCompanyAdminTestRole();
    $administrator = multiCompanyAdminTestAdministrator($company);
    multiCompanyAdminTestAuthenticate($administrator, [$company->getKey()]);

    $invitation = app(UserInvitationService::class)->create(
        $administrator,
        fake()->unique()->safeEmail(),
        $company->getKey(),
        [$companyRole->getKey()],
    );
    $user = app(UserInvitationService::class)->accept($invitation, 'Invited User', 'password123');

    expect($user->allowedCompanies()->pluck('companies.id')->all())->toBe([$company->getKey()])
        ->and($user->roles()->pluck('roles.id')->all())->toBe([$companyRole->getKey()])
        ->and($user->default_company_id)->toBe($company->getKey())
        ->and(Invitation::query()->find($invitation->getKey()))->toBeNull();
});

it('creates a company user through the Filament relationship form', function () {
    $company = multiCompanyAdminTestCompany();
    $companyRole = multiCompanyAdminTestRole();
    $administrator = multiCompanyAdminTestAdministrator($company);
    $email = fake()->unique()->safeEmail();
    multiCompanyAdminTestAuthenticate($administrator, [$company->getKey()]);

    Livewire::test(CreateUser::class)
        ->fillForm([
            'name'                => 'Created Company User',
            'email'               => $email,
            'password'            => 'password123',
            'password_confirmation' => 'password123',
            'roles'               => [$companyRole->getKey()],
            'resource_permission' => PermissionType::INDIVIDUAL->value,
            'allowed_companies'   => [$company->getKey()],
            'default_company_id'  => $company->getKey(),
            'is_active'           => true,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $createdUser = User::query()->where('email', $email)->firstOrFail();

    expect($createdUser->roles()->pluck('roles.id')->all())->toBe([$companyRole->getKey()])
        ->and($createdUser->allowedCompanies()->pluck('companies.id')->all())->toBe([$company->getKey()]);
});

it('lets a Super Admin promote a user and assign multiple companies through Filament', function () {
    $companyA = multiCompanyAdminTestCompany();
    $companyB = multiCompanyAdminTestCompany();
    $target = multiCompanyAdminTestUser($companyA);
    $multiCompanyRole = app(MultiCompanyAdminRoleProvisioner::class)->provision();
    $superAdmin = multiCompanyAdminTestSuperAdmin();
    multiCompanyAdminTestAuthenticate($superAdmin, [$companyA->getKey()]);

    Livewire::test(EditUser::class, ['record' => $target->getKey()])
        ->fillForm([
            'roles'              => [$multiCompanyRole->getKey()],
            'allowed_companies'  => [$companyA->getKey(), $companyB->getKey()],
            'default_company_id' => $companyA->getKey(),
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $target->refresh()->unsetRelation('roles');

    expect($target->isMultiCompanyAdmin())->toBeTrue()
        ->and($target->resource_permission)->toBe(PermissionType::GLOBAL)
        ->and($target->allowedCompanies()->pluck('companies.id')->all())
        ->toEqualCanonicalizing([$companyA->getKey(), $companyB->getKey()]);
});

it('can deactivate and safely soft-delete an allowed user', function () {
    $company = multiCompanyAdminTestCompany();
    $administrator = multiCompanyAdminTestAdministrator($company);
    $target = multiCompanyAdminTestUser($company);
    multiCompanyAdminTestAuthenticate($administrator, [$company->getKey()]);
    $policy = app(UserPolicy::class);

    expect($policy->update($administrator, $target))->toBeTrue()
        ->and($policy->delete($administrator, $target))->toBeTrue();

    $target->update(['is_active' => false]);
    $target->refresh();

    expect($target->is_active)->toBeFalse();

    $target->delete();

    expect(User::withTrashed()->findOrFail($target->getKey())->trashed())->toBeTrue();
});

it('cannot access a user belonging only to an unassigned company', function () {
    $assigned = multiCompanyAdminTestCompany();
    $unassigned = multiCompanyAdminTestCompany();
    $administrator = multiCompanyAdminTestAdministrator($assigned);
    $target = multiCompanyAdminTestUser($unassigned);
    multiCompanyAdminTestAuthenticate($administrator, [$assigned->getKey()]);

    expect(app(UserPolicy::class)->view($administrator, $target))->toBeFalse()
        ->and(UserResource::getEloquentQuery()->whereKey($target->getKey())->exists())->toBeFalse();
});

it('cannot create a user in an unassigned company', function () {
    $assigned = multiCompanyAdminTestCompany();
    $unassigned = multiCompanyAdminTestCompany();
    $companyRole = multiCompanyAdminTestRole();
    $administrator = multiCompanyAdminTestAdministrator($assigned);

    expect(fn () => app(MultiCompanyAdminService::class)->assertUserAssignment(
        $administrator,
        null,
        [$companyRole->getKey()],
        [$unassigned->getKey()],
        $unassigned->getKey(),
    ))->toThrow(ValidationException::class);
});

it('cannot assign the Super Admin role', function () {
    $company = multiCompanyAdminTestCompany();
    $administrator = multiCompanyAdminTestAdministrator($company);
    $superRole = multiCompanyAdminTestSuperAdmin()->roles()->firstOrFail();

    expect(fn () => app(MultiCompanyAdminService::class)->assertUserAssignment(
        $administrator,
        null,
        [$superRole->getKey()],
        [$company->getKey()],
        $company->getKey(),
    ))->toThrow(ValidationException::class);
});

it('cannot assign the Multi-Company Admin role', function () {
    $company = multiCompanyAdminTestCompany();
    $administrator = multiCompanyAdminTestAdministrator($company);
    $multiCompanyRole = app(MultiCompanyAdminRoleProvisioner::class)->provision();

    expect(fn () => app(MultiCompanyAdminService::class)->assertUserAssignment(
        $administrator,
        null,
        [$multiCompanyRole->getKey()],
        [$company->getKey()],
        $company->getKey(),
    ))->toThrow(ValidationException::class);
});

it('cannot assign a company role containing permissions it does not possess', function () {
    $company = multiCompanyAdminTestCompany();
    $elevatedRole = multiCompanyAdminTestRole(['update_support_currency']);
    $administrator = multiCompanyAdminTestAdministrator($company);

    expect(fn () => app(MultiCompanyAdminService::class)->assertUserAssignment(
        $administrator,
        null,
        [$elevatedRole->getKey()],
        [$company->getKey()],
        $company->getKey(),
    ))->toThrow(ValidationException::class);
});

it('cannot modify a Super Admin', function () {
    $company = multiCompanyAdminTestCompany();
    $companyRole = multiCompanyAdminTestRole();
    $administrator = multiCompanyAdminTestAdministrator($company);
    $superAdmin = multiCompanyAdminTestSuperAdmin();
    multiCompanyAdminTestAuthenticate($administrator, [$company->getKey()]);

    expect(app(UserPolicy::class)->update($administrator, $superAdmin))->toBeFalse()
        ->and(fn () => app(MultiCompanyAdminService::class)->assertUserAssignment(
            $administrator,
            $superAdmin,
            [$companyRole->getKey()],
            [$company->getKey()],
            $company->getKey(),
        ))->toThrow(ValidationException::class);
});

it('cannot modify another Multi-Company Admin', function () {
    $company = multiCompanyAdminTestCompany();
    $companyRole = multiCompanyAdminTestRole();
    $administrator = multiCompanyAdminTestAdministrator($company);
    $otherAdministrator = multiCompanyAdminTestAdministrator($company);
    multiCompanyAdminTestAuthenticate($administrator, [$company->getKey()]);

    expect(app(UserPolicy::class)->update($administrator, $otherAdministrator))->toBeFalse()
        ->and(fn () => app(MultiCompanyAdminService::class)->assertUserAssignment(
            $administrator,
            $otherAdministrator,
            [$companyRole->getKey()],
            [$company->getKey()],
            $company->getKey(),
        ))->toThrow(ValidationException::class);
});

it('cannot modify its own role or company assignments', function () {
    $companyA = multiCompanyAdminTestCompany();
    $companyB = multiCompanyAdminTestCompany();
    $administrator = multiCompanyAdminTestAdministrator([$companyA, $companyB]);
    $companyRole = multiCompanyAdminTestRole();

    expect(fn () => app(MultiCompanyAdminService::class)->assertUserAssignment(
        $administrator,
        $administrator,
        [$companyRole->getKey()],
        [$companyA->getKey()],
        $companyA->getKey(),
    ))->toThrow(ValidationException::class);
});

it('cannot force-delete users or business records', function () {
    $company = multiCompanyAdminTestCompany();
    $administrator = multiCompanyAdminTestAdministrator($company);
    $target = multiCompanyAdminTestUser($company);
    multiCompanyAdminTestGrantPermissions($administrator, [
        'force_delete_security_user',
        'force_delete_any_security_user',
        'force_delete_support_company',
    ]);

    expect($administrator->can('force_delete_security_user'))->toBeFalse()
        ->and($administrator->can('force_delete_support_company'))->toBeFalse()
        ->and(app(UserPolicy::class)->forceDelete($administrator, $target))->toBeFalse()
        ->and(app(UserPolicy::class)->forceDeleteAny($administrator))->toBeFalse();
});

it('revokes company access immediately when an assignment is removed', function () {
    $companyA = multiCompanyAdminTestCompany();
    $companyB = multiCompanyAdminTestCompany();
    $administrator = multiCompanyAdminTestAdministrator([$companyA, $companyB]);
    $campaignA = UtmCampaign::factory()->company($companyA)->create();
    $campaignB = UtmCampaign::factory()->company($companyB)->create();
    multiCompanyAdminTestAuthenticate($administrator, [$companyB->getKey()]);
    $context = app(CompanyContext::class);

    expect(UtmCampaign::query()->pluck('id')->all())->toContain($campaignB->getKey())
        ->and($context->currentCompany()?->getKey())->toBe($companyB->getKey());

    $administrator->allowedCompanies()->sync([$companyA->getKey()]);
    app()->forgetInstance(CompanyContext::class);
    $context = app(CompanyContext::class);

    expect($context->allowedIds())->toBe([$companyA->getKey()])
        ->and($context->currentId())->toBe($companyA->getKey())
        ->and($context->currentCompany()?->getKey())->toBe($companyA->getKey())
        ->and(UtmCampaign::query()->pluck('id')->all())->toContain($campaignA->getKey())
        ->not->toContain($campaignB->getKey());
});

it('removes panel API token and permission access when suspended', function () {
    $company = multiCompanyAdminTestCompany();
    $administrator = multiCompanyAdminTestAdministrator($company);
    $administrator->createToken('suspension-test');
    multiCompanyAdminTestAuthenticate($administrator, [$company->getKey()]);

    $administrator->update(['is_active' => false]);

    expect($administrator->canAccessPanel(Filament::getPanel('admin')))->toBeFalse()
        ->and($administrator->can('view_any_security_user'))->toBeFalse()
        ->and($administrator->tokens()->count())->toBe(0);

    $this->post(route('company-context.set'), [
        'companies' => [$company->getKey()],
    ])->assertForbidden();

    Auth::forgetGuards();

    $this->postJson('/admin/api/v1/login', [
        'email'    => $administrator->email,
        'password' => 'password',
    ])->assertUnprocessable();
});

it('scopes report data to the active assigned company', function () {
    $companyA = multiCompanyAdminTestCompany();
    $companyB = multiCompanyAdminTestCompany();
    $companyC = multiCompanyAdminTestCompany();
    $campaignA = UtmCampaign::factory()->company($companyA)->create();
    $campaignB = UtmCampaign::factory()->company($companyB)->create();
    $campaignC = UtmCampaign::factory()->company($companyC)->create();
    $administrator = multiCompanyAdminTestAdministrator([$companyA, $companyB]);
    multiCompanyAdminTestAuthenticate($administrator, [$companyB->getKey()]);

    $visibleIds = UtmCampaign::query()->pluck('id')->all();

    expect($visibleIds)->toContain($campaignB->getKey())
        ->not->toContain($campaignA->getKey(), $campaignC->getKey());
});

it('serializes export queries with the active company constraint', function () {
    $companyA = multiCompanyAdminTestCompany();
    $companyB = multiCompanyAdminTestCompany();
    $campaignA = UtmCampaign::factory()->company($companyA)->create();
    $campaignB = UtmCampaign::factory()->company($companyB)->create();
    $administrator = multiCompanyAdminTestAdministrator([$companyA, $companyB]);
    multiCompanyAdminTestAuthenticate($administrator, [$companyB->getKey()]);

    $serialized = EloquentSerializeFacade::serialize(UtmCampaign::query());
    app(CompanyContext::class)->setActive([$companyA->getKey()], $companyA->getKey());
    $exportedIds = EloquentSerializeFacade::unserialize($serialized)->pluck('id')->all();

    expect($exportedIds)->toContain($campaignB->getKey())
        ->not->toContain($campaignA->getKey());
});

it('retains the selected company constraint when a queued query runs without session state', function () {
    $companyA = multiCompanyAdminTestCompany();
    $companyB = multiCompanyAdminTestCompany();
    $campaignA = UtmCampaign::factory()->company($companyA)->create();
    $campaignB = UtmCampaign::factory()->company($companyB)->create();
    $administrator = multiCompanyAdminTestAdministrator([$companyA, $companyB]);
    multiCompanyAdminTestAuthenticate($administrator, [$companyB->getKey()]);

    $serialized = EloquentSerializeFacade::serialize(UtmCampaign::query());

    Auth::forgetGuards();
    session()->forget(CompanyContext::SESSION_KEY);
    app()->forgetInstance(CompanyContext::class);

    $jobResultIds = EloquentSerializeFacade::unserialize($serialized)->pluck('id')->all();

    expect($jobResultIds)->toContain($campaignB->getKey())
        ->not->toContain($campaignA->getKey());
});

it('does not leak Bouncer ownership cache entries between authenticated users', function () {
    $company = multiCompanyAdminTestCompany();
    $first = multiCompanyAdminTestUser($company);
    $second = multiCompanyAdminTestUser($company);

    multiCompanyAdminTestAuthenticate($first, [$company->getKey()]);
    expect(bouncer()->getAuthorizedUserIds())->toBe([$first->getKey()]);

    multiCompanyAdminTestAuthenticate($second, [$company->getKey()]);
    expect(bouncer()->getAuthorizedUserIds())->toBe([$second->getKey()]);
});

it('clears the resolved company when switching active company context', function () {
    $companyA = multiCompanyAdminTestCompany();
    $companyB = multiCompanyAdminTestCompany();
    $campaignA = UtmCampaign::factory()->company($companyA)->create();
    $campaignB = UtmCampaign::factory()->company($companyB)->create();
    $administrator = multiCompanyAdminTestAdministrator([$companyA, $companyB]);
    multiCompanyAdminTestAuthenticate($administrator, [$companyA->getKey()]);
    $context = app(CompanyContext::class);

    expect($context->currentCompany()?->getKey())->toBe($companyA->getKey())
        ->and(UtmCampaign::query()->pluck('id')->all())->toContain($campaignA->getKey())
        ->not->toContain($campaignB->getKey());

    $context->setActive([$companyB->getKey()], $companyB->getKey());

    expect($context->currentCompany()?->getKey())->toBe($companyB->getKey())
        ->and(UtmCampaign::query()->pluck('id')->all())->toContain($campaignB->getKey())
        ->not->toContain($campaignA->getKey());
});

it('denies platform bypass role and branding permissions even if directly attached', function () {
    $company = multiCompanyAdminTestCompany();
    $administrator = multiCompanyAdminTestAdministrator($company);
    multiCompanyAdminTestGrantPermissions($administrator, [
        'bypass_company_scope',
        'bypass_ownership_scope',
        'view_any_role',
        'page_security_manage_api_tokens',
        'page_support_manage_branding',
        'view_any_plugin_manager_plugin',
    ]);
    multiCompanyAdminTestAuthenticate($administrator, [$company->getKey()]);

    expect($administrator->can('bypass_company_scope'))->toBeFalse()
        ->and($administrator->can('bypass_ownership_scope'))->toBeFalse()
        ->and($administrator->can('view_any_role'))->toBeFalse()
        ->and($administrator->can('page_security_manage_api_tokens'))->toBeFalse()
        ->and($administrator->can('page_support_manage_branding'))->toBeFalse()
        ->and($administrator->can('view_any_plugin_manager_plugin'))->toBeFalse()
        ->and(app(CompanyContext::class)->bypassed())->toBeFalse();
});

it('preserves existing Super Admin access and protected role behavior', function () {
    $companyA = multiCompanyAdminTestCompany();
    $companyB = multiCompanyAdminTestCompany();
    $administrator = multiCompanyAdminTestAdministrator($companyA);
    $superAdmin = multiCompanyAdminTestSuperAdmin();
    $multiCompanyRole = app(MultiCompanyAdminRoleProvisioner::class)->provision();
    multiCompanyAdminTestAuthenticate($superAdmin, [$companyA->getKey()]);

    app(MultiCompanyAdminService::class)->assertUserAssignment(
        $superAdmin,
        $administrator,
        [$multiCompanyRole->getKey()],
        [$companyA->getKey(), $companyB->getKey()],
        $companyA->getKey(),
    );

    expect($superAdmin->isSuperAdmin())->toBeTrue()
        ->and($superAdmin->roles()->firstOrFail()->isSystemRole())->toBeTrue()
        ->and(UserResource::getEloquentQuery()->whereKey($administrator->getKey())->exists())->toBeTrue()
        ->and(SecurityCompanyResource::getEloquentQuery()->whereKey($companyB->getKey())->exists())->toBeTrue()
        ->and(app(RolePolicy::class)->view($superAdmin, $multiCompanyRole))->toBeTrue();
});

it('preserves tenant isolation for ordinary company users', function () {
    $companyA = multiCompanyAdminTestCompany();
    $companyB = multiCompanyAdminTestCompany();
    $campaignA = UtmCampaign::factory()->company($companyA)->create();
    $campaignB = UtmCampaign::factory()->company($companyB)->create();
    $ordinary = multiCompanyAdminTestUser($companyA, attributes: [
        'resource_permission' => PermissionType::GLOBAL,
    ]);
    multiCompanyAdminTestAuthenticate($ordinary, [$companyA->getKey()]);

    expect($ordinary->isMultiCompanyAdmin())->toBeFalse()
        ->and(UtmCampaign::query()->pluck('id')->all())->toContain($campaignA->getKey())
        ->not->toContain($campaignB->getKey());
});
