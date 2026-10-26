<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

/**
 * SAAS-00 characterization: pins a known defect, it does not endorse it.
 *
 * The `user_allowed_companies` table has no unique constraint on
 * `(user_id, company_id)`. Eloquent `sync()` happens to avoid duplicates, but
 * the database does not protect raw writes, imports, or races.
 *
 * SAAS-01 must add the unique constraint after cleaning up any duplicates.
 * See: docs/saas-administration-billing-audit-plan.md § Known related defects.
 */
it('allows duplicate membership rows in user_allowed_companies', function () {
    expect(Schema::hasTable('user_allowed_companies'))->toBeTrue();

    $company = Company::factory()->create();
    $user = User::withoutEvents(fn (): User => User::factory()->create([
        'default_company_id' => $company->getKey(),
        'is_active'          => true,
    ]));

    // Precondition: no membership exists yet.
    $countBefore = DB::table('user_allowed_companies')
        ->where('user_id', $user->getKey())
        ->where('company_id', $company->getKey())
        ->count();

    expect($countBefore)->toBe(0);

    // Insert one membership via Eloquent sync (the normal path).
    $user->allowedCompanies()->sync([$company->getKey()]);

    $countAfterSync = DB::table('user_allowed_companies')
        ->where('user_id', $user->getKey())
        ->where('company_id', $company->getKey())
        ->count();

    expect($countAfterSync)->toBe(1);

    // Insert a duplicate via raw DB — this succeeds because no unique constraint
    // exists. If this test starts throwing a unique-violation exception, the
    // defect has been fixed and this test should be updated accordingly.
    DB::table('user_allowed_companies')->insert([
        'user_id'    => $user->getKey(),
        'company_id' => $company->getKey(),
    ]);

    $countAfterDuplicate = DB::table('user_allowed_companies')
        ->where('user_id', $user->getKey())
        ->where('company_id', $company->getKey())
        ->count();

    // The defect: two rows exist for the same (user, company) pair.
    expect($countAfterDuplicate)->toBe(2);

    // Clean up the duplicate so subsequent tests are not affected.
    DB::table('user_allowed_companies')
        ->where('user_id', $user->getKey())
        ->where('company_id', $company->getKey())
        ->limit(1)
        ->delete();
});

it('confirms the membership table has no unique index on user_id and company_id', function () {
    $indexes = DB::select(
        "SELECT indexname, indexdef FROM pg_indexes WHERE tablename = 'user_allowed_companies'"
    );

    $hasUniqueOnPair = collect($indexes)
        ->filter(fn ($index) => str_contains(strtolower($index->indexdef), 'unique')
            && str_contains($index->indexdef, 'user_id')
            && str_contains($index->indexdef, 'company_id'))
        ->isNotEmpty();

    // The defect: no unique index covers (user_id, company_id).
    expect($hasUniqueOnPair)->toBeFalse();
});
