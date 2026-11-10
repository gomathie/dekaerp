<?php

use Illuminate\Support\Facades\DB;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\User;
use Webkul\Security\Services\CompanyAdministratorInviter;
use Webkul\Security\Services\CompanyAdminRoleProvisioner;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensureERPInstalled();
    app(CompanyAdminRoleProvisioner::class)->provision();
});

function backfillTestMember(Company $company, int $roleId): User
{
    $user = User::factory()->create(['default_company_id' => $company->getKey(), 'is_active' => true]);
    $user->allowedCompanies()->sync([$company->getKey()]);

    DB::table('company_membership_roles')->insertOrIgnore([
        'user_id'    => $user->getKey(), 'company_id' => $company->getKey(), 'role_id' => $roleId,
        'created_at' => now(), 'updated_at' => now(),
    ]);

    return $user;
}

function runBusinessAdminBackfill(): void
{
    (require __DIR__.'/../../../database/migrations/2026_11_11_000001_make_existing_business_admins_company_admins.php')->up();
}

function holdsCompanyAdmin(User $user, Company $company): bool
{
    return DB::table('company_membership_roles')
        ->where('user_id', $user->getKey())
        ->where('company_id', $company->getKey())
        ->where('role_id', CompanyAdministratorInviter::companyAdminRoleId())
        ->exists();
}

/**
 * Existing companies' administrators held "Admin2" but never "Company Admin",
 * so the company page listed nobody (user, 2026-11-11). The backfill acts only
 * when it is certain: one candidate, and no administrator already.
 */
it('makes the single Admin2 member of a company without an administrator its Company Admin', function () {
    $admin2 = Role::query()->firstOrCreate(['name' => 'Admin2', 'guard_name' => 'web']);

    $single = Company::factory()->create();
    $owner = backfillTestMember($single, $admin2->getKey());

    $twoCandidates = Company::factory()->create();
    $first = backfillTestMember($twoCandidates, $admin2->getKey());
    $second = backfillTestMember($twoCandidates, $admin2->getKey());

    $alreadyAdministered = Company::factory()->create();
    backfillTestMember($alreadyAdministered, CompanyAdministratorInviter::companyAdminRoleId());
    $other = backfillTestMember($alreadyAdministered, $admin2->getKey());

    runBusinessAdminBackfill();

    expect(holdsCompanyAdmin($owner, $single))->toBeTrue()
        ->and(holdsCompanyAdmin($first, $twoCandidates))->toBeFalse()
        ->and(holdsCompanyAdmin($second, $twoCandidates))->toBeFalse()
        ->and(holdsCompanyAdmin($other, $alreadyAdministered))->toBeFalse()
        // Their business role stays.
        ->and(DB::table('company_membership_roles')->where('user_id', $owner->getKey())->where('role_id', $admin2->getKey())->exists())->toBeTrue();

    // Safe to run twice.
    runBusinessAdminBackfill();

    expect(DB::table('company_membership_roles')
        ->where('user_id', $owner->getKey())
        ->where('role_id', CompanyAdministratorInviter::companyAdminRoleId())
        ->count())->toBe(1);
});
