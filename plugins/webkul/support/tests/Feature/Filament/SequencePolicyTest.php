<?php

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Filament\Actions\Testing\TestAction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;
use Spatie\Permission\PermissionRegistrar;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\Permission;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\User;
use Webkul\Security\PermissionRegistrar as ForkPermissionRegistrar;
use Webkul\Security\Services\CompanyAdminRoleProvisioner;
use Webkul\Security\Services\MultiCompanyAdminRoleProvisioner;
use Webkul\Support\Enums\SequenceResetFrequency;
use Webkul\Support\Filament\Resources\SequenceResource;
use Webkul\Support\Filament\Resources\SequenceResource\Pages\ManageSequences;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Sequence;
use Webkul\Support\Services\CompanyContext;

require_once __DIR__.'/../../Helpers/FilamentHelper.php';
require_once __DIR__.'/../../Helpers/TestBootstrapHelper.php';

/**
 * UF-029 (upstream PR #1693; user decision 2026-11-25, "Company Admin and
 * up"): the Sequences screen had no policy, so every signed-in user could
 * edit, reset or delete document numbering, including the shared counters
 * every company without its own uses. Now: installation staff, or a member
 * of the sequence's company whose role grants the permission there (Company
 * Admin has it by default); shared sequences only installation staff;
 * nobody else.
 */
const SEQUENCE_TEST_PERMISSIONS = [
    'view_any_support_sequence',
    'view_support_sequence',
    'create_support_sequence',
    'update_support_sequence',
    'delete_support_sequence',
    'delete_any_support_sequence',
];

const SEQUENCE_TEST_EDIT = [
    'name'            => 'A numbering',
    'prefix'          => 'INV-A/',
    'padding'         => 5,
    'next_number'     => 7,
    'step'            => 1,
    'reset_frequency' => 'never',
];

beforeEach(function () {
    TestBootstrapHelper::ensureERPInstalled();

    Package::$plugins = Plugin::all()->keyBy('name');

    $this->companyA = Company::factory()->create(['name' => 'Numbering A', 'is_active' => true]);
    $this->companyB = Company::factory()->create(['name' => 'Numbering B', 'is_active' => true]);

    [$this->sequenceA, $this->sequenceB, $this->shared] = CompanyHelper::asNobody(fn (): array => [
        Sequence::query()->create(['code' => 'uf029.a', 'name' => 'A numbering', 'company_id' => $this->companyA->id, 'next_number' => 1]),
        Sequence::query()->create(['code' => 'uf029.b', 'name' => 'B numbering', 'company_id' => $this->companyB->id, 'next_number' => 1]),
        Sequence::query()->create(['code' => 'uf029.shared', 'name' => 'Shared numbering', 'company_id' => null, 'next_number' => 1]),
    ]);
});

function sequenceTestUser(Company $company, array $roles): User
{
    $user = User::withoutEvents(fn (): User => User::factory()->create([
        'default_company_id'  => $company->id,
        'resource_permission' => PermissionType::GLOBAL,
        'is_active'           => true,
        'is_default'          => false,
    ]));

    $user->allowedCompanies()->sync([$company->id]);
    $user->syncRoles(collect($roles)->map->getKey()->all());
    $user->unsetRelation('roles');

    return $user;
}

function sequenceTestRole(string $name, array $permissions): Role
{
    $role = Role::query()->firstOrCreate(['name' => $name, 'guard_name' => 'web']);

    $role->givePermissionTo(collect($permissions)->map(fn (string $permission) => Permission::query()
        ->firstOrCreate(['name' => $permission, 'guard_name' => 'web']))->all());

    app(PermissionRegistrar::class)->forgetCachedPermissions();
    app(ForkPermissionRegistrar::class)->forgetCachedPermissions();

    return $role;
}

function sequenceTestCompanyAdminRole(): Role
{
    $role = app(CompanyAdminRoleProvisioner::class)->provision();

    app(PermissionRegistrar::class)->forgetCachedPermissions();
    app(ForkPermissionRegistrar::class)->forgetCachedPermissions();

    return $role;
}

function sequenceTestSignIn(User $user, Company $company): void
{
    Auth::forgetGuards();
    Auth::shouldUse('web');
    Auth::guard('web')->login($user);
    session([CompanyContext::SESSION_KEY => [$company->id]]);
    app()->forgetInstance(CompanyContext::class);
    FilamentHelper::bootAdminPanel();
}

it('generates exactly the sequence permissions the policy checks', function () {
    FilamentHelper::bootAdminPanel();

    // The policy names <affix>_support_sequence; if Shield generated other
    // names, no role could ever be given numbering and the screen would be
    // closed even to a Company Admin, while every other test here still
    // passed because they create the names by hand.
    $generated = collect(FilamentShield::getResources())
        ->flatMap(fn (array $resource): array => collect($resource['permissions'] ?? [])
            ->map(fn (mixed $permission): ?string => is_array($permission) ? ($permission['key'] ?? null) : (is_string($permission) ? $permission : null))
            ->filter()
            ->values()
            ->all())
        ->filter(fn (string $name): bool => str_ends_with($name, '_support_sequence'))
        ->values()
        ->all();

    expect($generated)->toEqualCanonicalizing(SEQUENCE_TEST_PERMISSIONS);
});

it('refuses an ordinary user the numbering list, edit, create and delete, also by direct address', function () {
    $user = sequenceTestUser($this->companyA, [sequenceTestRole('Numbering outsider', ['view_any_support_currency'])]);

    sequenceTestSignIn($user, $this->companyA);

    expect(SequenceResource::canViewAny())->toBeFalse()
        ->and(Gate::allows('view', $this->sequenceA))->toBeFalse()
        ->and(Gate::allows('update', $this->sequenceA))->toBeFalse()
        ->and(Gate::allows('delete', $this->sequenceA))->toBeFalse()
        ->and(Gate::allows('create', Sequence::class))->toBeFalse()
        ->and(Gate::allows('update', $this->shared))->toBeFalse();

    // A signed-in GET that is refused lands on the dashboard (2026-11-20),
    // so the address is refused, not served. Before Livewire::test(), which
    // swaps the redirector for the rest of the test.
    $this->get(SequenceResource::getUrl('index'))->assertRedirect(url('admin'));

    Livewire::test(ManageSequences::class)->assertForbidden();

    expect($this->sequenceA->refresh()->next_number)->toBe(1);
});

it('lets a Company Admin manage their own company\'s numbering but not another company\'s or a shared one', function () {
    $user = sequenceTestUser($this->companyA, [sequenceTestCompanyAdminRole()]);

    sequenceTestSignIn($user, $this->companyA);

    expect(SequenceResource::canViewAny())->toBeTrue()
        ->and(Gate::allows('update', $this->sequenceA))->toBeTrue()
        ->and(Gate::allows('delete', $this->sequenceA))->toBeTrue()
        ->and(Gate::allows('update', $this->sequenceB))->toBeFalse()
        ->and(Gate::allows('delete', $this->sequenceB))->toBeFalse()
        // Readable, since it numbers their documents until they have their own.
        ->and(Gate::allows('view', $this->shared))->toBeTrue()
        ->and(Gate::allows('update', $this->shared))->toBeFalse()
        ->and(Gate::allows('delete', $this->shared))->toBeFalse()
        ->and(Gate::allows('createIn', [Sequence::class, null]))->toBeFalse()
        ->and(Gate::allows('createIn', [Sequence::class, $this->companyB->id]))->toBeFalse()
        // Bulk delete stays with installation staff.
        ->and(Gate::allows('deleteAny', Sequence::class))->toBeFalse();

    $this->get(SequenceResource::getUrl('index'))->assertOk();

    Livewire::test(ManageSequences::class)
        ->assertActionHidden(TestAction::make('edit')->table($this->shared))
        ->assertActionHidden(TestAction::make('delete')->table($this->shared))
        ->callAction(TestAction::make('edit')->table($this->sequenceA), SEQUENCE_TEST_EDIT)
        ->assertHasNoActionErrors();

    expect($this->sequenceA->refresh()->prefix)->toBe('INV-A/')
        ->and($this->sequenceA->next_number)->toBe(7)
        ->and($this->shared->refresh()->next_number)->toBe(1);
});

it('does not treat a global Company Admin role as administering a company the user is not a member of', function () {
    $user = sequenceTestUser($this->companyA, [sequenceTestCompanyAdminRole()]);

    sequenceTestSignIn($user, $this->companyA);

    // The role is global, so isCompanyAdmin() is true for company B too;
    // membership is what keeps B's numbering out of reach.
    expect($user->isCompanyAdmin($this->companyB->id))->toBeTrue()
        ->and(Gate::allows('view', $this->sequenceB))->toBeFalse()
        ->and(Gate::allows('update', $this->sequenceB))->toBeFalse()
        ->and(Gate::allows('delete', $this->sequenceB))->toBeFalse();
});

it('lets a member whose role grants numbering manage their own company\'s sequences only', function () {
    $user = sequenceTestUser($this->companyA, [sequenceTestRole('Numbering clerk', SEQUENCE_TEST_PERMISSIONS)]);

    sequenceTestSignIn($user, $this->companyA);

    expect($user->isCompanyAdmin($this->companyA->id))->toBeFalse()
        ->and(SequenceResource::canViewAny())->toBeTrue()
        ->and(Gate::allows('update', $this->sequenceA))->toBeTrue()
        ->and(Gate::allows('update', $this->sequenceB))->toBeFalse()
        ->and(Gate::allows('update', $this->shared))->toBeFalse()
        ->and(Gate::allows('delete', $this->shared))->toBeFalse()
        ->and(Gate::allows('createIn', [Sequence::class, null]))->toBeFalse()
        // Holding delete_any does not open bulk delete outside staff.
        ->and(Gate::allows('deleteAny', Sequence::class))->toBeFalse();
});

it('creates a Company Admin\'s new sequence in their own company, never a shared one', function () {
    $user = sequenceTestUser($this->companyA, [sequenceTestCompanyAdminRole()]);

    sequenceTestSignIn($user, $this->companyA);

    Livewire::test(ManageSequences::class)
        ->callAction('create', [
            'name'            => 'Delivery notes',
            'code'            => 'uf029.created',
            'company_id'      => null,
            'padding'         => 5,
            'next_number'     => 1,
            'step'            => 1,
            'reset_frequency' => SequenceResetFrequency::NEVER->value,
        ])
        ->assertHasNoActionErrors();

    expect(Sequence::withoutGlobalScopes()->where('code', 'uf029.created')->value('company_id'))
        ->toBe($this->companyA->id);
});

it('lets installation staff edit a shared sequence, a Multi-Company Admin only within assigned companies', function () {
    $superAdmin = User::query()->where('email', 'admin@example.com')->firstOrFail();
    $superAdmin->allowedCompanies()->syncWithoutDetaching([$this->companyA->id]);

    sequenceTestSignIn($superAdmin, $this->companyA);

    expect($superAdmin->isSuperAdmin())->toBeTrue()
        ->and(Gate::allows('update', $this->shared))->toBeTrue()
        ->and(Gate::allows('delete', $this->shared))->toBeTrue()
        ->and(Gate::allows('createIn', [Sequence::class, null]))->toBeTrue();

    Livewire::test(ManageSequences::class)
        ->callAction(TestAction::make('edit')->table($this->shared), [...SEQUENCE_TEST_EDIT, 'name' => 'Shared numbering', 'prefix' => 'ALL/'])
        ->assertHasNoActionErrors();

    expect($this->shared->refresh()->prefix)->toBe('ALL/');

    app(MultiCompanyAdminRoleProvisioner::class)->provision();

    $multiCompanyAdmin = sequenceTestUser($this->companyA, [
        Role::query()->whereRaw('LOWER(name) = ?', [mb_strtolower(Role::MULTI_COMPANY_ADMIN)])->firstOrFail(),
    ]);

    sequenceTestSignIn($multiCompanyAdmin, $this->companyA);

    expect(Gate::allows('update', $this->shared))->toBeTrue()
        ->and(Gate::allows('update', $this->sequenceA))->toBeTrue()
        // Only within the companies assigned to them.
        ->and(Gate::allows('update', $this->sequenceB))->toBeFalse()
        ->and(Gate::allows('delete', $this->sequenceB))->toBeFalse();
});

it('gives the Company Admin role its numbering permissions on deploy, adding only', function () {
    $role = sequenceTestCompanyAdminRole();
    $role->revokePermissionTo('update_support_sequence');

    $extra = Permission::query()->firstOrCreate(['name' => 'view_any_support_currency', 'guard_name' => 'web']);
    $role->givePermissionTo($extra);

    expect(app(CompanyAdminRoleProvisioner::class)->grantMissingBasePermissions())->toBe(1);

    $names = $role->refresh()->permissions->pluck('name');

    expect($names)->toContain('update_support_sequence')
        // Something added on the Roles screen is kept.
        ->and($names)->toContain('view_any_support_currency')
        // Bulk delete stays with staff.
        ->and($names)->not->toContain('delete_any_support_sequence');

    expect(app(CompanyAdminRoleProvisioner::class)->grantMissingBasePermissions())->toBe(0);
});
