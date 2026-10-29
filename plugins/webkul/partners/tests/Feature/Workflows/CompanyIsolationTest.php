<?php

use Webkul\Partner\Models\Partner;

require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

it('hides partners owned by another company', function () {
    $companyA = CompanyHelper::company();
    $companyB = CompanyHelper::company();

    $ownPartner = Partner::factory()->company($companyA)->create();
    $otherPartner = Partner::factory()->company($companyB)->create();

    CompanyHelper::actingAsCompanyUser($companyA);

    $visible = Partner::query()->pluck('id');

    expect($visible)->toContain($ownPartner->id)
        ->not->toContain($otherPartner->id);
});

/**
 * Inverted 2026-10-29. This used to pin "leaves a new partner shared instead of
 * stamping the active company", which was upstream's design (57e234223). Clients
 * are unrelated businesses, so a contact now belongs to the company that created
 * it (user decision; docs/company-admin-role-plan.md §4k).
 */
it('stamps the active company on a new partner', function () {
    $companyB = CompanyHelper::company();

    CompanyHelper::actingAsCompanyUser($companyB);

    $partner = Partner::factory()->create();

    expect($partner->company_id)->toBe($companyB->getKey());
});

it('keeps a new partner out of another company', function () {
    $companyA = CompanyHelper::company();
    $companyB = CompanyHelper::company();

    CompanyHelper::actingAsCompanyUser($companyA);

    $partner = Partner::factory()->create();

    CompanyHelper::actingAsCompanyUser($companyB);

    expect(Partner::query()->pluck('id'))->not->toContain($partner->id);
});

/**
 * A contact with no company is still visible everywhere. That is how the
 * example data created by seeders stays shared (decided 2026-10-29), so it is
 * created here the same way: with nobody acting, when nothing is stamped.
 */
it('shows a partner with no company from every company', function () {
    $companyA = CompanyHelper::company();
    $companyB = CompanyHelper::company();

    $partner = Partner::factory()->create(['company_id' => null]);

    expect($partner->company_id)->toBeNull();

    CompanyHelper::actingAsCompanyUser($companyA);

    expect(Partner::query()->pluck('id'))->toContain($partner->id);

    CompanyHelper::actingAsCompanyUser($companyB);

    expect(Partner::query()->pluck('id'))->toContain($partner->id);
});
