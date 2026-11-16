<?php

use Webkul\Partner\Models\Partner;
use Webkul\Support\Exceptions\CrossCompanyException;
use Webkul\Support\Models\Company;
use Webkul\Support\Services\CompanyContext;

require_once __DIR__.'/../../Helpers/SecurityHelper.php';
require_once __DIR__.'/../../Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../Helpers/CompanyHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensureERPInstalled();
});

/**
 * A user may not write a record into a company they are not a member of
 * (upstream PR #1575 adapted, hotfix v1.11.3, 2026-11-16). Before, the API
 * took a body company_id unchecked, so company A's user could stamp a record
 * with company B. The rule is CompanyContext::mayWriteTo().
 */
it('refuses creating a record in another client\'s company', function () {
    [$mine, $theirs] = Company::factory()->count(2)->create();
    CompanyHelper::actingAsCompanyUser($mine);

    expect(fn () => Partner::create(['name' => 'Smuggled', 'company_id' => $theirs->getKey()]))
        ->toThrow(CrossCompanyException::class, __('support::support.cross-company.company', ['company' => $theirs->getKey()]));

    expect(Partner::withoutGlobalScopes()->where('name', 'Smuggled')->exists())->toBeFalse();
});

it('refuses moving a record into another client\'s company', function () {
    [$mine, $theirs] = Company::factory()->count(2)->create();
    CompanyHelper::actingAsCompanyUser($mine);

    $partner = Partner::create(['name' => 'Mine', 'company_id' => $mine->getKey()]);

    expect(fn () => $partner->update(['company_id' => $theirs->getKey()]))
        ->toThrow(CrossCompanyException::class);

    expect($partner->fresh()->company_id)->toBe($mine->getKey());
});

it('still allows own-company and shared records, and untouched edits', function () {
    [$mine] = Company::factory()->count(2)->create();
    CompanyHelper::actingAsCompanyUser($mine);

    $own = Partner::create(['name' => 'Own', 'company_id' => $mine->getKey()]);
    $own->update(['name' => 'Own renamed']);

    expect($own->fresh()->name)->toBe('Own renamed')
        ->and(app(CompanyContext::class)->mayWriteTo(null))->toBeTrue();
});

it('lets jobs with no acting user and all-company staff write anywhere', function () {
    [, $theirs] = Company::factory()->count(2)->create();

    $fromJob = CompanyHelper::withoutCompanyContext(
        fn () => Partner::create(['name' => 'From job', 'company_id' => $theirs->getKey()])
    );

    SecurityHelper::authenticateWithPermissions([], bypassCompanyScope: true);
    app()->forgetInstance(CompanyContext::class);

    $byStaff = Partner::create(['name' => 'By staff', 'company_id' => $theirs->getKey()]);

    expect($fromJob->company_id)->toBe($theirs->getKey())
        ->and($byStaff->company_id)->toBe($theirs->getKey());
});

it('lets a new company set up its own records before its creator is a member', function () {
    [$mine] = Company::factory()->count(2)->create();
    CompanyHelper::actingAsCompanyUser($mine);

    $context = app(CompanyContext::class);
    $context->markProvisioned(999001);

    expect($context->mayWriteTo(999001))->toBeTrue()
        ->and($context->mayWriteTo(999002))->toBeFalse();
});
