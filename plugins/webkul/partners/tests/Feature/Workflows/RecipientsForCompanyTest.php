<?php

use Illuminate\Validation\ValidationException;
use Webkul\Partner\Models\Partner;

require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

/**
 * Who a company's document may be emailed to (user decision, 2026-10-30; §4k
 * read audit in docs/company-admin-role-plan.md).
 *
 * Partner::recipientsForCompany() is the one rule for the invoice, sales order
 * and purchase order email actions: the record's own contacts and shared ones,
 * and a refusal naming any other id. A scoped query alone dropped an ordinary
 * user's out-of-company id silently, and widened for a super admin or a
 * Multi-Company Admin with several companies active.
 */
beforeEach(function () {
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function recipientsSharedContact(): Partner
{
    $partner = Partner::factory()->create();

    Partner::withoutGlobalScopes()->whereKey($partner->id)->update(['company_id' => null]);

    return $partner;
}

it('accepts the company\'s own contacts and shared ones', function () {
    $company = CompanyHelper::company();

    CompanyHelper::actingAsCompanyUser($company);

    $own = Partner::factory()->create();
    $shared = recipientsSharedContact();

    $recipients = Partner::recipientsForCompany([$own->id, $shared->id], $company->id);

    expect($recipients->modelKeys())->toEqualCanonicalizing([$own->id, $shared->id]);
});

it('refuses another company\'s contact and names it', function () {
    $companyA = CompanyHelper::company();
    $companyB = CompanyHelper::company();

    CompanyHelper::actingAsCompanyUser($companyB);
    $foreign = Partner::factory()->create();

    CompanyHelper::actingAsCompanyUser($companyA);
    $own = Partner::factory()->create();

    expect(fn () => Partner::recipientsForCompany([$own->id, $foreign->id], $companyA->id))
        ->toThrow(
            ValidationException::class,
            "These recipients do not belong to this company and cannot be emailed: {$foreign->id}.",
        );
});

it('refuses another company\'s contact even for an actor who sees every company', function () {
    $companyA = CompanyHelper::company();
    $companyB = CompanyHelper::company();

    CompanyHelper::actingAsCompanyUser($companyB);
    $foreign = Partner::factory()->create();

    // bypass_company_scope: a scoped query would have returned the contact.
    SecurityHelper::authenticateWithPermissions([], bypassCompanyScope: true);

    expect(Partner::query()->whereKey($foreign->id)->exists())->toBeTrue()
        ->and(fn () => Partner::recipientsForCompany([$foreign->id], $companyA->id))
        ->toThrow(ValidationException::class);
});

it('refuses an id that does not exist instead of ignoring it', function () {
    $company = CompanyHelper::company();

    CompanyHelper::actingAsCompanyUser($company);

    expect(fn () => Partner::recipientsForCompany([999999999], $company->id))
        ->toThrow(ValidationException::class);
});
