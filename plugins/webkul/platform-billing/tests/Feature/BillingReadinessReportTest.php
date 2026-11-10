<?php

use Webkul\PlatformBilling\Models\Subscription;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensureERPInstalled();
});

/**
 * The report replaces shadow mode (SAAS-14, 2026-11-10). It must name a
 * company that enforcement would lock, and it must write nothing.
 */
it('reports a company whose trial has ended as read-only and writes nothing', function () {
    // Created writable, then ended: a company created already expired is
    // refused its own onboarding writes by the read-only guard.
    $company = Company::factory()->create(['name' => 'Expired Trial Co']);
    $company->forceFill(['account_type' => 'test', 'test_ends_at' => now()->subDay()])->saveQuietly();

    $companiesBefore = Company::withoutGlobalScopes()->count();
    $subscriptionsBefore = Subscription::withoutGlobalScopes()->count();

    $this->artisan('platform-billing:readiness-report')
        ->expectsOutputToContain('Expired Trial Co')
        ->expectsOutputToContain('read-only now')
        ->assertSuccessful();

    expect(Company::withoutGlobalScopes()->count())->toBe($companiesBefore)
        ->and(Subscription::withoutGlobalScopes()->count())->toBe($subscriptionsBefore)
        ->and($company->fresh()->account_type)->toBe('test');
});
