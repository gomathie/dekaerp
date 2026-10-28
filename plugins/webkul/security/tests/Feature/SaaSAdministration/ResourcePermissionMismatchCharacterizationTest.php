<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Spatie\Permission\PermissionRegistrar;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Filament\Resources\UserResource\Pages\CreateUser;
use Webkul\Security\Filament\Resources\UserResource\Pages\EditUser;
use Webkul\Security\Models\User;
use Webkul\Security\Services\CompanyAdminRoleProvisioner;
use Webkul\Security\Services\MultiCompanyAdminService;
use Webkul\Support\Models\Company;
use Webkul\Support\Services\CompanyContext;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

/**
 * SAAS-00 characterization, **inverted by SAAS-01**.
 *
 * It used to pin that creating a Company Admin forced global resource
 * visibility while promoting an existing restricted user through the edit page
 * kept the restriction. SAAS-01 made `EditUser` apply the same rule as
 * `CreateUser`, so both real Filament paths now end on `global`.
 */
beforeEach(function (): void {
    Auth::forgetGuards();
    session()->forget(CompanyContext::SESSION_KEY);
    app()->forgetInstance(CompanyContext::class);
    app(PermissionRegistrar::class)->forgetCachedPermissions();
    app(CompanyAdminRoleProvisioner::class)->provision();
    URL::resolveMissingNamedRoutesUsing(fn (): string => '#');
});

afterEach(function (): void {
    Auth::forgetGuards();
    session()->forget(CompanyContext::SESSION_KEY);
    app()->forgetInstance(CompanyContext::class);
    app(PermissionRegistrar::class)->forgetCachedPermissions();
});

it('applies the same resource permission when creating and promoting Company Admins', function () {
    $company = Company::factory()->create([
        'name'      => 'SaaS Resource Permission Characterization',
        'is_active' => true,
    ]);
    $companyAdminRole = app(CompanyAdminRoleProvisioner::class)->provision();
    $superAdmin = User::query()->where('email', 'admin@example.com')->firstOrFail();

    $superAdmin->allowedCompanies()->syncWithoutDetaching([$company->getKey()]);

    Auth::shouldUse('web');
    Auth::guard('web')->login($superAdmin);
    session([CompanyContext::SESSION_KEY => [$company->getKey()]]);
    app()->forgetInstance(CompanyContext::class);

    // The same predicate both pages call, so "it is a Company Admin role" is
    // proven the way production decides it rather than by a name comparison.
    expect($superAdmin->isSuperAdmin())->toBeTrue()
        ->and(app(MultiCompanyAdminService::class)->includesCompanyAdminRole([$companyAdminRole->getKey()]))->toBeTrue();

    $createdEmail = fake()->unique()->safeEmail();

    Livewire::test(CreateUser::class)
        ->fillForm([
            'name'                  => 'Created Company Administrator',
            'email'                 => $createdEmail,
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'roles'                 => [$companyAdminRole->getKey()],
            'resource_permission'   => PermissionType::INDIVIDUAL->value,
            'allowed_companies'     => [$company->getKey()],
            'default_company_id'    => $company->getKey(),
            'is_active'             => true,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $createdAdministrator = User::query()->where('email', $createdEmail)->firstOrFail();

    $promotedAdministrator = User::factory()->create([
        'default_company_id'  => $company->getKey(),
        'resource_permission' => PermissionType::INDIVIDUAL,
        'is_active'           => true,
        'is_default'          => false,
    ]);
    $promotedAdministrator->allowedCompanies()->sync([$company->getKey()]);

    Livewire::test(EditUser::class, ['record' => $promotedAdministrator->getKey()])
        ->fillForm([
            'roles'               => [$companyAdminRole->getKey()],
            'resource_permission' => PermissionType::INDIVIDUAL->value,
            'allowed_companies'   => [$company->getKey()],
            'default_company_id'  => $company->getKey(),
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $createdAdministrator->refresh()->unsetRelation('roles');
    $promotedAdministrator->refresh()->unsetRelation('roles');

    // Asked for this company: since SAAS-01 the role is held in a membership.
    expect($createdAdministrator->isCompanyAdmin((int) $company->getKey()))->toBeTrue()
        ->and($promotedAdministrator->isCompanyAdmin((int) $company->getKey()))->toBeTrue()
        ->and($createdAdministrator->resource_permission)->toBe(PermissionType::GLOBAL)
        ->and($promotedAdministrator->resource_permission)->toBe(PermissionType::GLOBAL);
});
