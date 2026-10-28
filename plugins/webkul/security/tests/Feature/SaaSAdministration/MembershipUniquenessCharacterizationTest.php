<?php

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

/**
 * SAAS-00 characterization, **inverted by SAAS-01**.
 *
 * It used to pin that `user_allowed_companies` had no unique constraint on
 * `(user_id, company_id)`, so a raw write, an import or a race could store the
 * same membership twice. SAAS-01 added the constraint
 * (`2026_10_28_000001_add_unique_membership_to_user_allowed_companies`), which
 * the per-company role table now references, so the duplicate is refused by the
 * database rather than avoided by Eloquent's `sync()`.
 *
 * See: docs/saas-administration-billing-audit-plan.md § Known related defects.
 */
it('refuses a duplicate membership row in user_allowed_companies', function () {
    expect(Schema::hasTable('user_allowed_companies'))->toBeTrue();

    $company = Company::factory()->create();
    $user = User::withoutEvents(fn (): User => User::factory()->create([
        'default_company_id' => $company->getKey(),
        'is_active'          => true,
    ]));

    // Precondition: exactly one membership exists through the normal path, so
    // the refusal below is about the duplicate and not about a missing row.
    $user->allowedCompanies()->sync([$company->getKey()]);

    expect(DB::table('user_allowed_companies')
        ->where('user_id', $user->getKey())
        ->where('company_id', $company->getKey())
        ->count())->toBe(1);

    // Inside DB::transaction() so the refused insert rolls back to a savepoint.
    // On PostgreSQL a failed statement otherwise aborts the transaction the test
    // runs in, and the count below dies with "current transaction is aborted".
    expect(fn () => DB::transaction(fn () => DB::table('user_allowed_companies')->insert([
        'user_id'    => $user->getKey(),
        'company_id' => $company->getKey(),
    ])))->toThrow(QueryException::class);

    expect(DB::table('user_allowed_companies')
        ->where('user_id', $user->getKey())
        ->where('company_id', $company->getKey())
        ->count())->toBe(1);
});

it('confirms the membership table has a unique index on user_id and company_id', function () {
    $indexes = DB::select(
        "SELECT indexname, indexdef FROM pg_indexes WHERE tablename = 'user_allowed_companies'"
    );

    $hasUniqueOnPair = collect($indexes)
        ->filter(fn ($index) => str_contains(strtolower($index->indexdef), 'unique')
            && str_contains($index->indexdef, 'user_id')
            && str_contains($index->indexdef, 'company_id'))
        ->isNotEmpty();

    expect($hasUniqueOnPair)->toBeTrue();
});
