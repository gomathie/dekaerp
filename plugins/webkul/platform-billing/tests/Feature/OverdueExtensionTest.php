<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Webkul\PlatformBilling\Models\Invoice;
use Webkul\PlatformBilling\Models\OverdueExtension;
use Webkul\PlatformBilling\Models\Plan;
use Webkul\PlatformBilling\Models\Price;
use Webkul\PlatformBilling\Services\CompanyReadOnlyWriteGuard;
use Webkul\PlatformBilling\Services\OverdueExtensions;
use Webkul\PlatformBilling\Services\RecurringInvoiceGenerator;
use Webkul\PlatformBilling\Services\SubscriptionDrafts;
use Webkul\PlatformBilling\Services\SubscriptionLifecycle;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\User;
use Webkul\Security\Services\CompanyAdminRoleProvisioner;
use Webkul\Security\Services\MultiCompanyAdminRoleProvisioner;
use Webkul\Security\Services\TenantRoleAssigner;
use Webkul\Support\Models\Calendar;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensureERPInstalled();
    config([
        'platform-billing.issuer.name'        => 'DEKA',
        'platform-billing.issuer.tax_rate'    => '15.0000',
        'platform-billing.issuer.tax_enabled' => true,
    ]);
});

function overdueExtensionCompany(DateTimeImmutable $anchor): array
{
    $company = Company::factory()->create();
    $actor = User::query()->where('email', 'admin@example.com')->firstOrFail();
    $plan = Plan::query()->create([
        'name'             => 'Extension plan',
        'code'             => 'extend-'.uniqid(),
        'included_plugins' => [],
        'seat_limit'       => 3,
    ]);
    $price = Price::query()->create([
        'plan_id'         => $plan->id,
        'currency'        => 'USD',
        'interval_months' => 1,
        'amount_minor'    => 10000,
    ]);
    $draft = app(SubscriptionDrafts::class)->create($actor, $company, $price, $anchor);
    $active = app(SubscriptionLifecycle::class)->activate($draft, $actor, $anchor);
    app(RecurringInvoiceGenerator::class)->generateDue((int) $active->getKey(), new DateTimeImmutable('now', new DateTimeZone('UTC')));
    $invoice = Invoice::withoutGlobalScopes()->where('subscription_id', $active->getKey())->firstOrFail();
    $grace = DateTimeImmutable::createFromInterface($invoice->grace_starts_at);
    $active = app(SubscriptionLifecycle::class)->recordOverdue($active, $actor, $grace);

    return [$company, $actor, $active, $invoice, $grace];
}

function overdueExtensionAdmin(Company $company): User
{
    $admin = User::withoutEvents(fn (): User => User::factory()->create([
        'default_company_id' => $company->getKey(),
        'is_active'          => true,
        'is_default'         => false,
    ]));
    $admin->allowedCompanies()->sync([$company->getKey()]);
    $role = Role::query()->whereRaw('LOWER(name) = ?', [mb_strtolower(Role::COMPANY_ADMIN)])->first()
        ?? app(CompanyAdminRoleProvisioner::class)->provision();
    app(TenantRoleAssigner::class)->sync($admin, [(int) $role->getKey()], [(int) $company->getKey()]);
    $admin->unsetRelation('roles');

    return $admin;
}

function actAsOverdueExtensionUser(User $user): void
{
    Auth::shouldUse('web');
    Auth::guard('web')->login($user);
}

it('lets a company admin move the read-only instant by ten days once', function () {
    $now = new DateTimeImmutable('now', new DateTimeZone('UTC'));
    [$company, , , $invoice, $grace] = overdueExtensionCompany($now->modify('-12 days')->modify('-1 month'));
    $admin = overdueExtensionAdmin($company);
    actAsOverdueExtensionUser($admin);
    $lifecycle = app(SubscriptionLifecycle::class);
    $service = app(OverdueExtensions::class);

    expect($grace <= $now)->toBeTrue()
        ->and($grace->modify('+10 days') > $now)->toBeTrue()
        ->and($lifecycle->isReadOnly($company, $grace->modify('+10 days')))->toBeTrue();

    $extension = $service->extendAsCompanyAdmin($admin, $invoice, $now);

    expect($extension->length)->toBe(OverdueExtensions::COMPANY_ADMIN_DAYS)
        ->and($extension->unit)->toBe(OverdueExtension::UNIT_DAY)
        ->and($extension->kind)->toBe(OverdueExtension::KIND_COMPANY_ADMIN)
        ->and((int) $extension->company_id)->toBe((int) $company->getKey())
        ->and($lifecycle->isReadOnly($company, $grace->modify('+10 days')))->toBeFalse()
        ->and($lifecycle->isReadOnly($company, $grace->modify('+20 days')))->toBeTrue()
        ->and(fn () => $service->extendAsCompanyAdmin($admin, $invoice, $now))
        ->toThrow(ValidationException::class, OverdueExtensions::ALREADY_COMPANY_ADMIN)
        ->and(fn () => $extension->update(['length' => 11]))
        ->toThrow(QueryException::class, 'Posted overdue extensions are immutable.');
});

it('does not carry an extension from an earlier overdue episode into a later grace', function () {
    $now = new DateTimeImmutable('now', new DateTimeZone('UTC'));
    [$company, $superAdmin, $active, $invoice] = overdueExtensionCompany($now->modify('-12 days')->modify('-1 month'));
    $admin = overdueExtensionAdmin($company);
    actAsOverdueExtensionUser($admin);
    $lifecycle = app(SubscriptionLifecycle::class);

    app(OverdueExtensions::class)->extendAsCompanyAdmin($admin, $invoice, $now);

    actAsOverdueExtensionUser($superAdmin);
    $lifecycle->clearOverdue($active->fresh(), $superAdmin);
    $later = $now->modify('+1 day');
    $lifecycle->recordOverdue($active->fresh(), $superAdmin, $later);

    expect($lifecycle->isReadOnly($company->fresh(), $later->modify('+10 days')->modify('-1 second')))->toBeFalse()
        ->and($lifecycle->isReadOnly($company->fresh(), $later->modify('+10 days')))->toBeTrue();
});

it('refuses an extension before day 8 and from anyone who is not that company admin', function () {
    $now = new DateTimeImmutable('now', new DateTimeZone('UTC'));
    [$company, $superAdmin, , $invoice, $grace] = overdueExtensionCompany($now->modify('-12 days')->modify('-1 month'));
    $admin = overdueExtensionAdmin($company);
    $other = Company::factory()->create();
    $stranger = overdueExtensionAdmin($other);
    $mca = User::withoutEvents(fn (): User => User::factory()->create([
        'default_company_id' => $company->getKey(),
        'is_active'          => true,
        'is_default'         => false,
    ]));
    $mca->allowedCompanies()->sync([$company->getKey()]);
    $mca->syncRoles([app(MultiCompanyAdminRoleProvisioner::class)->provision()->getKey()]);
    $mca->unsetRelation('roles');
    actAsOverdueExtensionUser($admin);
    $service = app(OverdueExtensions::class);

    expect(fn () => $service->extendAsCompanyAdmin($admin, $invoice, $grace->modify('-1 day')))
        ->toThrow(ValidationException::class, OverdueExtensions::NOT_OVERDUE)
        ->and(fn () => $service->extendAsCompanyAdmin($stranger, $invoice, $now))
        ->toThrow(AuthorizationException::class, OverdueExtensions::ONLY_COMPANY_ADMIN)
        ->and(fn () => $service->extendAsCompanyAdmin($mca, $invoice, $now))
        ->toThrow(AuthorizationException::class, OverdueExtensions::ONLY_COMPANY_ADMIN)
        ->and(fn () => $service->extendAsSuperAdmin($mca, $invoice, 5, 'day', 'manual', $now))
        ->toThrow(AuthorizationException::class, OverdueExtensions::ONLY_SUPER_ADMIN)
        ->and(fn () => $service->extendAsSuperAdmin($admin, $invoice, 5, 'day', 'manual', $now))
        ->toThrow(AuthorizationException::class, OverdueExtensions::ONLY_SUPER_ADMIN)
        ->and(fn () => $service->extendAsSuperAdmin($superAdmin, $invoice, 0, 'day', 'manual', $now))
        ->toThrow(ValidationException::class, OverdueExtensions::LENGTH_REQUIRED)
        ->and(fn () => $service->extendAsSuperAdmin($superAdmin, $invoice, 5, 'year', 'manual', $now))
        ->toThrow(ValidationException::class, OverdueExtensions::UNIT_REQUIRED)
        ->and(fn () => $service->extendAsSuperAdmin($superAdmin, $invoice, 5, 'day', '  ', $now))
        ->toThrow(ValidationException::class, OverdueExtensions::REASON_REQUIRED);
});

it('keeps the super admin extension separate from the company admin one', function () {
    $now = new DateTimeImmutable('now', new DateTimeZone('UTC'));
    [$company, $superAdmin, , $invoice, $grace] = overdueExtensionCompany($now->modify('-12 days')->modify('-1 month'));
    $admin = overdueExtensionAdmin($company);
    actAsOverdueExtensionUser($admin);
    $service = app(OverdueExtensions::class);
    $lifecycle = app(SubscriptionLifecycle::class);

    $service->extendAsCompanyAdmin($admin, $invoice, $now);
    $manual = $service->extendAsSuperAdmin($superAdmin, $invoice, 5, 'days', 'recovery', $now);

    expect($manual->kind)->toBe(OverdueExtension::KIND_SUPER_ADMIN)
        ->and($manual->length)->toBe(5)
        ->and($manual->unit)->toBe(OverdueExtension::UNIT_DAY)
        ->and(OverdueExtension::withoutGlobalScopes()->where('invoice_id', $invoice->getKey())->count())->toBe(2)
        ->and($lifecycle->isReadOnly($company, $grace->modify('+20 days')))->toBeFalse()
        ->and($lifecycle->isReadOnly($company, $grace->modify('+25 days')))->toBeTrue()
        ->and(fn () => $service->extendAsSuperAdmin($superAdmin, $invoice, 3, 'week', 'again', $now))
        ->toThrow(ValidationException::class, OverdueExtensions::ALREADY_SUPER_ADMIN);
});

it('counts a super admin extension in weeks and in months', function () {
    $now = new DateTimeImmutable('now', new DateTimeZone('UTC'));
    [$company, $superAdmin, , $invoice, $grace] = overdueExtensionCompany($now->modify('-12 days')->modify('-1 month'));
    actAsOverdueExtensionUser($superAdmin);
    $service = app(OverdueExtensions::class);
    $lifecycle = app(SubscriptionLifecycle::class);
    $weeks = $service->extendAsSuperAdmin($superAdmin, $invoice, 2, 'weeks', 'two weeks', $now);
    $weekEnd = $grace->modify('+10 days')->modify('+2 weeks');

    expect($weeks->unit)->toBe(OverdueExtension::UNIT_WEEK)
        ->and($lifecycle->isReadOnly($company, $weekEnd->modify('-1 day')))->toBeFalse()
        ->and($lifecycle->isReadOnly($company, $weekEnd))->toBeTrue();

    [$other, $otherAdmin, , $otherInvoice, $otherGrace] = overdueExtensionCompany($now->modify('-12 days')->modify('-1 month'));
    actAsOverdueExtensionUser($otherAdmin);
    $month = $service->extendAsSuperAdmin($otherAdmin, $otherInvoice, 1, 'month', 'one month', $now);
    $monthEnd = $otherGrace->modify('+10 days')->modify('+1 month');

    expect($month->unit)->toBe(OverdueExtension::UNIT_MONTH)
        ->and($lifecycle->isReadOnly($other, $monthEnd->modify('-1 day')))->toBeFalse()
        ->and($lifecycle->isReadOnly($other, $monthEnd))->toBeTrue();
});

it('lets a company admin extend an invoice after the company is already read-only', function () {
    // This used to expect the read-only guard to refuse the grant. The user
    // decided on 2026-11-10 that the company admin may extend after the lock.
    $now = new DateTimeImmutable('now', new DateTimeZone('UTC'));
    [$company, , , $invoice, $grace] = overdueExtensionCompany($now->modify('-40 days')->modify('-1 month'));
    $admin = overdueExtensionAdmin($company);
    actAsOverdueExtensionUser($admin);
    $lifecycle = app(SubscriptionLifecycle::class);

    expect($grace->modify('+20 days') <= $now)->toBeTrue()
        ->and($lifecycle->isReadOnly($company, $now))->toBeTrue()
        ->and(fn () => Calendar::query()->create([
            'name'       => 'locked company write',
            'timezone'   => 'UTC',
            'company_id' => $company->getKey(),
        ]))->toThrow(ValidationException::class, CompanyReadOnlyWriteGuard::MESSAGE);

    $extension = app(OverdueExtensions::class)->extendAsCompanyAdmin($admin, $invoice, $now);

    expect($extension->length)->toBe(OverdueExtensions::COMPANY_ADMIN_DAYS)
        ->and($lifecycle->isReadOnly($company, $now))->toBeFalse()
        ->and($lifecycle->isReadOnly($company, $now->modify('+10 days')))->toBeTrue()
        ->and(fn () => app(OverdueExtensions::class)->extendAsCompanyAdmin($admin, $invoice, $now))
        ->toThrow(ValidationException::class, OverdueExtensions::ALREADY_COMPANY_ADMIN);
});
