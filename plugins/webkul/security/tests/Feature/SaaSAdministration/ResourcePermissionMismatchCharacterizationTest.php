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
use Webkul\Support\Models\Company;
use Webkul\Support\Services\CompanyContext;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

/**
 * SAAS-00 characterization: pins a known defect, it does not endorse it.
 *
 * Creating a Company Admin forces global resource visibility, but promoting an
 * existing restricted user through the edit page preserves the restriction.
 * SAAS-01 must make both real Filament paths agree, then invert this test.
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

it('applies different resource permissions when creating and promoting Company Admins', function () {
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

    expect($superAdmin->isSuperAdmin())->toBeTrue()
        ->and($companyAdminRole->isCompanyAdminRole())->toBeTrue();

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

    expect($createdAdministrator->isCompanyAdmin())->toBeTrue()
        ->and($promotedAdministrator->isCompanyAdmin())->toBeTrue()
        ->and($createdAdministrator->resource_permission)->toBe(PermissionType::GLOBAL)
        ->and($promotedAdministrator->resource_permission)->toBe(PermissionType::INDIVIDUAL);
});
