<?php

use Illuminate\Support\Facades\Route;
use Webkul\Support\Models\Company;
use Webkul\Support\Services\CompanyContext;

require_once __DIR__.'/../../../../support/tests/Helpers/SecurityHelper.php';

/**
 * SAAS-00 characterization: an API request has no explicit company selector
 * today. When a multi-company user has no session selection, CompanyContext
 * resolves the user's default company. SAAS-02 should replace this implicit
 * behavior with an authorized selector while retaining a compatibility path.
 */
it('falls back to the user default company for a multi-company API request', function () {
    $user = SecurityHelper::authenticateWithPermissions([]);
    $defaultCompanyId = (int) $user->default_company_id;
    $otherCompany = Company::factory()->create();

    $user->allowedCompanies()->sync([$defaultCompanyId, $otherCompany->getKey()]);

    session()->forget(CompanyContext::SESSION_KEY);
    app()->forgetInstance(CompanyContext::class);

    Route::middleware('auth:sanctum')->get(
        '/_saas-characterization/current-company',
        fn () => response()->json([
            'company_id' => app(CompanyContext::class)->currentId(),
        ]),
    );

    $allowedCompanyIds = $user->allowedCompanies()
        ->pluck('companies.id')
        ->map(fn ($id): int => (int) $id)
        ->sort()
        ->values()
        ->all();

    expect($allowedCompanyIds)->toBe(collect([$defaultCompanyId, $otherCompany->getKey()])->sort()->values()->all())
        ->and($otherCompany->getKey())->not->toBe($defaultCompanyId)
        ->and(session()->has(CompanyContext::SESSION_KEY))->toBeFalse();

    $this->getJson('/_saas-characterization/current-company')
        ->assertOk()
        ->assertJsonPath('company_id', $defaultCompanyId);
});
