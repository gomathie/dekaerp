<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Webkul\PlatformBilling\Models\Invoice;
use Webkul\PlatformBilling\Models\Plan;
use Webkul\PlatformBilling\Models\Price;
use Webkul\PlatformBilling\Models\Subscription;
use Webkul\PlatformBilling\Services\CompanyAccountType;
use Webkul\PlatformBilling\Services\CompanyReadOnlyWriteGuard;
use Webkul\PlatformBilling\Services\SubscriptionDrafts;
use Webkul\PlatformBilling\Services\SubscriptionLifecycle;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Services\PluginEntitlementService;
use Webkul\PluginManager\Support\PluginAccess;
use Webkul\Security\Models\User;
use Webkul\Security\Services\MultiCompanyAdminRoleProvisioner;
use Webkul\Support\Models\Calendar;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensureERPInstalled();
});

function trialCompany(string $endsAt): Company
{
    $company = Company::factory()->create([
        'account_type' => 'test',
        'test_ends_at' => new DateTimeImmutable($endsAt),
    ]);

    return $company->fresh() ?? $company;
}

function actAsInstallationStaff(User $user): void
{
    Auth::shouldUse('web');
    Auth::guard('web')->login($user);
}

function trialPrice(): Price
{
    $plan = Plan::query()->create([
        'name' => 'Trial switch plan',
        'code' => 'trial-'.uniqid(),
        'included_plugins' => [],
        'seat_limit' => 3,
    ]);

    return Price::query()->create([
        'plan_id' => $plan->id,
        'currency' => 'USD',
        'interval_months' => 1,
        'amount_minor' => 1000,
    ]);
}

it('makes an expired test company read-only and leaves an open trial writable', function () {
    // Create while the trial is still open. Saving the company also saves its
    // partner, and that write is refused once the trial has ended.
    $company = trialCompany('2026-12-01T00:00:00Z');
    DB::table('companies')->where('id', $company->getKey())->update([
        'test_ends_at' => '2026-11-01 00:00:00+00',
    ]);
    $company->refresh();
    $lifecycle = app(SubscriptionLifecycle::class);

    expect($lifecycle->isReadOnly($company, new DateTimeImmutable('2026-10-31T00:00:00Z')))->toBeFalse()
        ->and($lifecycle->isReadOnly($company, new DateTimeImmutable('2026-11-01T00:00:00Z')))->toBeTrue();

    $tenant = SecurityHelper::authenticateWithPermissions([]);

    expect($tenant->isSuperAdmin())->toBeFalse()
        ->and(fn () => Calendar::query()->create([
            'name' => 'expired trial write',
            'timezone' => 'UTC',
            'company_id' => $company->getKey(),
        ]))->toThrow(ValidationException::class, CompanyReadOnlyWriteGuard::MESSAGE);

    expect(Company::withoutGlobalScopes()->whereKey($company->getKey())->exists())->toBeTrue();
});

it('lets installation staff add days and refuses a company administrator', function () {
    $company = trialCompany('2026-12-01T00:00:00Z');
    DB::table('companies')->where('id', $company->getKey())->update([
        'test_ends_at' => '2026-11-01 00:00:00+00',
    ]);
    $company->refresh();
    $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();
    $service = app(CompanyAccountType::class);
    $at = new DateTimeImmutable('2026-11-09T00:00:00Z');

    // The company save also updates its partner. The guard allows that only
    // while the installation staff member is the authenticated user.
    actAsInstallationStaff($admin);
    $extended = $service->addDays($admin, $company, 10, $at);

    expect($extended->test_ends_at->format('Y-m-d'))->toBe('2026-11-19');

    // Created while the Super Admin is signed in, so this company is assigned
    // to them. Creating it after the Multi-Company Admin signs in would assign
    // it to that admin and the refusal would never run.
    $stranger = Company::factory()->create(['account_type' => 'test', 'test_ends_at' => $at->modify('+2 days')]);

    $mca = User::withoutEvents(fn (): User => User::factory()->create([
        'default_company_id' => $company->getKey(),
        'is_active' => true,
        'is_default' => false,
    ]));
    $mca->allowedCompanies()->sync([$company->getKey()]);
    $mca->syncRoles([app(MultiCompanyAdminRoleProvisioner::class)->provision()->getKey()]);
    $mca->unsetRelation('roles');
    actAsInstallationStaff($mca);

    expect($service->addDays($mca, $company, 5, $at)->fresh()->test_ends_at->format('Y-m-d'))->toBe('2026-11-24');

    expect(fn () => $service->addDays($mca, $stranger, 1, $at))
        ->toThrow(AuthorizationException::class, CompanyAccountType::ONLY_STAFF);

    $clerk = SecurityHelper::authenticateWithPermissions(['create_security_company']);

    expect(fn () => $service->addDays($clerk, $company, 1, $at))
        ->toThrow(AuthorizationException::class, CompanyAccountType::ONLY_STAFF);
});

it('switches a test company to commercial at the next period and refuses the reverse', function () {
    $company = trialCompany('2026-12-01T00:00:00Z');
    DB::table('companies')->where('id', $company->getKey())->update([
        'test_ends_at' => '2026-11-01 00:00:00+00',
    ]);
    $company->refresh();
    $actor = User::query()->where('email', 'admin@example.com')->firstOrFail();
    $at = new DateTimeImmutable('2026-11-09T00:00:00Z');
    actAsInstallationStaff($actor);
    $switched = app(CompanyAccountType::class)->switchToCommercial($actor, $company, trialPrice(), $at);
    $subscription = Subscription::withoutGlobalScopes()->where('company_id', $company->getKey())->first();

    expect($switched->account_type)->toBe('commercial')
        ->and($switched->test_ends_at)->toBeNull()
        ->and(Invoice::withoutGlobalScopes()->where('company_id', $company->getKey())->count())->toBe(0)
        ->and($subscription->next_invoice_at > $at)->toBeTrue();

    expect(fn () => app(CompanyAccountType::class)->switchToCommercial($actor, $switched, trialPrice(), $at))
        ->toThrow(ValidationException::class, CompanyAccountType::ONLY_TEST_SWITCH);
});

it('lets an assigned multi-company admin switch a test company to commercial', function () {
    $company = trialCompany('2026-12-01T00:00:00Z');
    $at = new DateTimeImmutable('2026-11-09T00:00:00Z');
    $mca = User::withoutEvents(fn (): User => User::factory()->create([
        'default_company_id' => $company->getKey(),
        'is_active' => true,
        'is_default' => false,
    ]));
    $mca->allowedCompanies()->sync([$company->getKey()]);
    $mca->syncRoles([app(MultiCompanyAdminRoleProvisioner::class)->provision()->getKey()]);
    $mca->unsetRelation('roles');
    actAsInstallationStaff($mca);
    $price = trialPrice();

    $switched = app(CompanyAccountType::class)->switchToCommercial($mca, $company, $price, $at);

    expect($switched->account_type)->toBe('commercial')
        ->and($switched->test_ends_at)->toBeNull()
        ->and(Invoice::withoutGlobalScopes()->where('company_id', $company->getKey())->count())->toBe(0)
        ->and(Subscription::withoutGlobalScopes()->where('company_id', $company->getKey())->where('status', 'active')->exists())->toBeTrue();

    expect(fn () => app(SubscriptionDrafts::class)->create($mca, $company, $price, $at))
        ->toThrow(AuthorizationException::class, 'Only a Super Admin may change subscriptions.');

    expect(fn () => app(PluginEntitlementService::class)->syncForCompany($company, [], $mca))
        ->toThrow(AuthorizationException::class, 'Only a platform administrator may change plugin entitlements.');
});

it('refuses a multi-company admin switching a company they are not assigned to', function () {
    $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();
    actAsInstallationStaff($admin);
    $stranger = trialCompany('2026-12-01T00:00:00Z');
    $assigned = trialCompany('2026-12-01T00:00:00Z');
    $at = new DateTimeImmutable('2026-11-09T00:00:00Z');
    $mca = User::withoutEvents(fn (): User => User::factory()->create([
        'default_company_id' => $assigned->getKey(),
        'is_active' => true,
        'is_default' => false,
    ]));
    $mca->allowedCompanies()->sync([$assigned->getKey()]);
    $mca->syncRoles([app(MultiCompanyAdminRoleProvisioner::class)->provision()->getKey()]);
    $mca->unsetRelation('roles');
    actAsInstallationStaff($mca);

    expect(fn () => app(CompanyAccountType::class)->switchToCommercial($mca, $stranger, trialPrice(), $at))
        ->toThrow(AuthorizationException::class, CompanyAccountType::ONLY_SWITCH_STAFF);

    expect(fn () => app(PluginEntitlementService::class)->syncForCommercialSwitch($stranger, [], $mca))
        ->toThrow(AuthorizationException::class, CompanyAccountType::ONLY_DURING_SWITCH);

    $clerk = SecurityHelper::authenticateWithPermissions(['create_security_company']);

    expect(fn () => app(CompanyAccountType::class)->switchToCommercial($clerk, $assigned, trialPrice(), $at))
        ->toThrow(AuthorizationException::class, CompanyAccountType::ONLY_SWITCH_STAFF);
});

it('refuses a multi-company admin using the commercial switch entries on a commercial company', function () {
    $company = Company::factory()->create([
        'account_type' => 'commercial',
        'test_ends_at' => null,
    ]);
    $at = new DateTimeImmutable('2026-11-09T00:00:00Z');
    $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();
    $price = trialPrice();
    $draft = app(SubscriptionDrafts::class)->create($admin, $company, $price, $at);
    $mca = User::withoutEvents(fn (): User => User::factory()->create([
        'default_company_id' => $company->getKey(),
        'is_active' => true,
        'is_default' => false,
    ]));
    $mca->allowedCompanies()->sync([$company->getKey()]);
    $mca->syncRoles([app(MultiCompanyAdminRoleProvisioner::class)->provision()->getKey()]);
    $mca->unsetRelation('roles');
    actAsInstallationStaff($mca);

    expect(fn () => app(SubscriptionDrafts::class)->createForCommercialSwitch($mca, $company, $price, $at))
        ->toThrow(AuthorizationException::class, CompanyAccountType::ONLY_DURING_SWITCH)
        ->and(fn () => app(SubscriptionLifecycle::class)->activateForCommercialSwitch($draft, $mca, $at))
        ->toThrow(AuthorizationException::class, CompanyAccountType::ONLY_DURING_SWITCH)
        ->and(fn () => app(PluginEntitlementService::class)->syncForCommercialSwitch($company, [], $mca))
        ->toThrow(AuthorizationException::class, CompanyAccountType::ONLY_DURING_SWITCH);

    expect(Subscription::withoutGlobalScopes()->where('company_id', $company->getKey())->where('status', 'active')->exists())->toBeFalse();
});

it('keeps a manually granted module when a test company switches to commercial', function () {
    $company = trialCompany('2026-12-01T00:00:00Z');
    $plugin = Plugin::query()->updateOrCreate(
        ['name' => 'saas-switch-kept'],
        ['is_installed' => true, 'is_active' => true],
    );
    DB::table('company_plugin')->insert([
        'company_id' => $company->getKey(),
        'plugin_id' => $plugin->getKey(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    PluginAccess::flush();
    $before = app(PluginEntitlementService::class)->grantedPluginIds((int) $company->getKey());
    $mca = User::withoutEvents(fn (): User => User::factory()->create([
        'default_company_id' => $company->getKey(),
        'is_active' => true,
        'is_default' => false,
    ]));
    $mca->allowedCompanies()->sync([$company->getKey()]);
    $mca->syncRoles([app(MultiCompanyAdminRoleProvisioner::class)->provision()->getKey()]);
    $mca->unsetRelation('roles');
    actAsInstallationStaff($mca);

    $switched = app(CompanyAccountType::class)->switchToCommercial(
        $mca,
        $company,
        trialPrice(),
        new DateTimeImmutable('2026-11-09T00:00:00Z'),
    );
    $after = app(PluginEntitlementService::class)->grantedPluginIds((int) $company->getKey());

    expect($switched->account_type)->toBe('commercial')
        ->and($before)->toContain((int) $plugin->getKey())
        ->and($after)->toContain((int) $plugin->getKey())
        ->and(array_diff($before, $after))->toBe([]);
});
