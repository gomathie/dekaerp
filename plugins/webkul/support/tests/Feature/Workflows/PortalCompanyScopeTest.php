<?php

use Illuminate\Support\Facades\Auth;
use Webkul\Partner\Models\Partner;
use Webkul\Support\Models\Calendar;
use Webkul\Support\Services\CompanyContext;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

/**
 * The customer portal authenticates Partners on the "customer" guard, and
 * Filament's Authenticate middleware calls Auth::shouldUse() with the panel
 * guard - so on portal routes auth()->user() is a Partner, not a User.
 *
 * The company scopes ask the authenticated user for allowedCompanies(), which
 * Partner does not have, so before CompanyContext::internalUser() existed any
 * portal page touching a company-scoped model raised BadMethodCallException.
 *
 * Nothing else in the suite authenticates on the customer guard, so without
 * these two the portal path is unexercised.
 */
beforeEach(function () {
    TestBootstrapHelper::ensureERPInstalled();
});

it('does not mistake a portal partner for an internal user', function () {
    $partner = Partner::factory()->create();

    Auth::guard('customer')->setUser($partner);
    Auth::shouldUse('customer');

    expect(auth()->user())->toBeInstanceOf(Partner::class)
        ->and(app(CompanyContext::class)->internalUser())->toBeNull();
});

it('queries a company-scoped model on the portal guard without erroring', function () {
    $partner = Partner::factory()->create();

    Auth::guard('customer')->setUser($partner);
    Auth::shouldUse('customer');

    // Calendar carries CompanyScope. Before the fix this raised
    // BadMethodCallException: Partner::allowedCompanies() does not exist.
    expect(fn () => Calendar::query()->count())->not->toThrow(BadMethodCallException::class);
});
