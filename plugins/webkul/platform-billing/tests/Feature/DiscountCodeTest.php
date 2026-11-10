<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Webkul\PlatformBilling\Services\DiscountCodeService;
use Webkul\PlatformBilling\Support\Money;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\User;
use Webkul\Security\Services\MultiCompanyAdminRoleProvisioner;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensureERPInstalled();
});

function discountCodeTerms(array $overrides = []): array
{
    return array_replace([
        'code'               => 'SAAS09A-'.strtoupper(fake()->bothify('??????')),
        'discount_type'      => 'percentage',
        'discount_value'     => 1500,
        'duration_type'      => 'forever',
        'duration_months'    => null,
        'eligibility_type'   => 'any_company',
        'company_ids'        => [],
        'max_redemptions'    => null,
        'redemption_ends_at' => null,
    ], $overrides);
}

function discountCodeSuperAdmin(): User
{
    return User::query()->where('email', 'admin@example.com')->firstOrFail();
}

function discountCodeMca(array $companies): User
{
    $company = $companies[0];
    $role = app(MultiCompanyAdminRoleProvisioner::class)->provision();
    $actor = User::withoutEvents(fn (): User => User::factory()->create([
        'default_company_id' => $company->getKey(),
        'is_active'          => true,
        'is_default'         => false,
    ]));
    $actor->allowedCompanies()->sync(collect($companies)->map->getKey()->all());
    $actor->syncRoles([$role->getKey()]);
    $actor->unsetRelation('roles');

    return $actor;
}

function discountCodeCompanyAdmin(Company $company): User
{
    $actor = User::withoutEvents(fn (): User => User::factory()->create([
        'default_company_id' => $company->getKey(),
        'is_active'          => true,
        'is_default'         => false,
    ]));
    $actor->allowedCompanies()->sync([$company->getKey()]);
    $role = Role::query()->firstOrCreate(['name' => 'Company Admin', 'guard_name' => 'web'], ['is_default' => false]);
    DB::table('company_membership_roles')->insert([
        'user_id'    => $actor->getKey(),
        'company_id' => $company->getKey(),
        'role_id'    => $role->getKey(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return $actor;
}

it('allows a Super Admin to create a discount code for any company', function () {
    $code = app(DiscountCodeService::class)->create(discountCodeSuperAdmin(), discountCodeTerms());

    expect($code->eligibility_type)->toBe('any_company')
        ->and($code->code)->toStartWith('SAAS09A-');
});

it('limits Multi-Company Admin discount codes to their assigned company list', function () {
    $assigned = Company::factory()->create();
    $other = Company::factory()->create();
    $mca = discountCodeMca([$assigned]);
    $service = app(DiscountCodeService::class);

    $code = $service->create($mca, discountCodeTerms([
        'eligibility_type' => 'company_list',
        'company_ids'      => [$assigned->getKey()],
    ]));

    expect($code->eligibleCompanies()->pluck('companies.id')->all())->toBe([$assigned->getKey()])
        ->and(fn () => $service->create($mca, discountCodeTerms([
            'eligibility_type' => 'company_list',
            'company_ids'      => [$other->getKey()],
        ])))->toThrow(AuthorizationException::class, 'A Multi-Company Admin may create discount codes only for assigned companies.')
        ->and(fn () => $service->create($mca, discountCodeTerms()))
        ->toThrow(AuthorizationException::class, 'A Multi-Company Admin must limit discount codes to assigned companies.');
});

it('lets only the company administrator redeem for that company', function () {
    $company = Company::factory()->create();
    $otherCompany = Company::factory()->create();
    $admin = discountCodeCompanyAdmin($company);
    $code = app(DiscountCodeService::class)->create(discountCodeSuperAdmin(), discountCodeTerms([
        'eligibility_type' => 'company_list',
        'company_ids'      => [$company->getKey()],
    ]));
    $service = app(DiscountCodeService::class);

    expect($service->redeem($code->code, $company, $admin)->company_id)->toBe($company->getKey())
        ->and(fn () => $service->redeem($code->code, $otherCompany, $admin))
        ->toThrow(AuthorizationException::class, "Only this company's administrator may redeem its discount.");
});

it('allows a Multi-Company Admin to redeem only for an assigned eligible company', function () {
    $assigned = Company::factory()->create();
    $alsoAssigned = Company::factory()->create();
    $unassigned = Company::factory()->create();
    $mca = discountCodeMca([$assigned, $alsoAssigned]);
    $code = app(DiscountCodeService::class)->create($mca, discountCodeTerms([
        'eligibility_type' => 'company_list',
        'company_ids'      => [$assigned->getKey()],
    ]));
    $service = app(DiscountCodeService::class);

    expect($service->redeem($code->code, $assigned, $mca)->company_id)->toBe($assigned->getKey())
        ->and(fn () => $service->redeem($code->code, $alsoAssigned, $mca))
        ->toThrow(AuthorizationException::class, 'This discount code is not available to this company.')
        ->and(fn () => $service->redeem($code->code, $unassigned, $mca))
        ->toThrow(AuthorizationException::class, 'A Multi-Company Admin may redeem discounts only for assigned companies.');
});

it('refuses expired, exhausted and already redeemed discount codes with specific reasons', function () {
    $actor = discountCodeSuperAdmin();
    $firstCompany = Company::factory()->create();
    $secondCompany = Company::factory()->create();
    $service = app(DiscountCodeService::class);
    $expired = $service->create($actor, discountCodeTerms([
        'code'               => 'EXPIRED-'.strtoupper(fake()->bothify('????')),
        'redemption_ends_at' => new DateTimeImmutable('2026-01-01T00:00:00Z'),
    ]));
    $limited = $service->create($actor, discountCodeTerms([
        'code'            => 'LIMIT-'.strtoupper(fake()->bothify('????')),
        'max_redemptions' => 1,
    ]));
    $service->redeem($limited->code, $firstCompany, $actor, new DateTimeImmutable('2025-12-01T00:00:00Z'));

    expect(fn () => $service->redeem($expired->code, $firstCompany, $actor, new DateTimeImmutable('2026-01-02T00:00:00Z')))
        ->toThrow(ValidationException::class, 'This discount code has expired.')
        ->and(fn () => $service->redeem($limited->code, $secondCompany, $actor, new DateTimeImmutable('2025-12-02T00:00:00Z')))
        ->toThrow(ValidationException::class, 'This discount code has reached its redemption limit.')
        ->and(fn () => $service->redeem($limited->code, $firstCompany, $actor, new DateTimeImmutable('2025-12-02T00:00:00Z')))
        ->toThrow(ValidationException::class, 'This company has already redeemed this discount code.');
});

it('refuses stacking and allows a new code after a months-long redemption expires', function () {
    $company = Company::factory()->create();
    $actor = discountCodeSuperAdmin();
    $service = app(DiscountCodeService::class);
    $first = $service->create($actor, discountCodeTerms([
        'code'            => 'MONTHS-A-'.strtoupper(fake()->bothify('???')),
        'duration_type'   => 'months',
        'duration_months' => 1,
    ]));
    $second = $service->create($actor, discountCodeTerms(['code' => 'MONTHS-B-'.strtoupper(fake()->bothify('???'))]));
    $service->redeem($first->code, $company, $actor, new DateTimeImmutable('2026-01-31T10:00:00Z'));

    expect(fn () => $service->redeem($second->code, $company, $actor, new DateTimeImmutable('2026-02-15T00:00:00Z')))
        ->toThrow(ValidationException::class, 'This company already has an active discount.');

    expect($service->redeem($second->code, $company, $actor, new DateTimeImmutable('2026-02-28T10:00:00Z'))->exists)->toBeTrue();
});

it('calculates percentage and fixed discounts in USD before VAT and preserves redeemed terms after code expiry', function () {
    $company = Company::factory()->create();
    $actor = discountCodeSuperAdmin();
    $service = app(DiscountCodeService::class);
    $percentage = $service->create($actor, discountCodeTerms([
        'code'               => 'PCT-'.strtoupper(fake()->bothify('????')),
        'redemption_ends_at' => new DateTimeImmutable('2026-01-02T00:00:00Z'),
    ]));
    $redemption = $service->redeem($percentage->code, $company, $actor, new DateTimeImmutable('2026-01-01T00:00:00Z'));
    $percentage->update(['is_active' => false]);

    expect($service->calculateBeforeVat($redemption, Money::of(10000, 'USD'), new DateTimeImmutable('2026-03-01T00:00:00Z'))->minor)
        ->toBe(1500)
        ->and($service->calculateBeforeVat($redemption, Money::of(10000, 'USD'), new DateTimeImmutable('2026-03-01T00:00:00Z'), 2)->minor)
        ->toBe(1500);

    $fixed = $service->create($actor, discountCodeTerms([
        'code'           => 'FIXED-'.strtoupper(fake()->bothify('????')),
        'discount_type'  => 'fixed_usd',
        'discount_value' => 2500,
    ]));
    $companyB = Company::factory()->create();
    $fixedRedemption = $service->redeem($fixed->code, $companyB, $actor);

    expect($service->calculateBeforeVat($fixedRedemption, Money::of(1000, 'USD'), new DateTimeImmutable('2026-01-01T00:00:00Z'))->minor)->toBe(1000);
});

it('applies free months as a full pre-tax invoice discount only when the invoice calendar selects a free month', function () {
    $company = Company::factory()->create();
    $actor = discountCodeSuperAdmin();
    $service = app(DiscountCodeService::class);
    $code = $service->create($actor, discountCodeTerms([
        'code'           => 'FREE-'.strtoupper(fake()->bothify('????')),
        'discount_type'  => 'free_months',
        'discount_value' => 2,
    ]));
    $redemption = $service->redeem($code->code, $company, $actor);

    expect($service->calculateBeforeVat($redemption, Money::of(10000, 'USD'), new DateTimeImmutable('2026-01-01T00:00:00Z'), freeMonthApplies: true)->minor)
        ->toBe(10000)
        ->and($service->calculateBeforeVat($redemption, Money::of(10000, 'USD'), new DateTimeImmutable('2026-01-01T00:00:00Z'))->minor)
        ->toBe(0);
});

it('keeps discount redemptions immutable in the database', function () {
    $company = Company::factory()->create();
    $actor = discountCodeSuperAdmin();
    $code = app(DiscountCodeService::class)->create($actor, discountCodeTerms());
    $redemption = app(DiscountCodeService::class)->redeem($code->code, $company, $actor);

    expect(fn () => DB::transaction(fn () => DB::table('platform_billing_discount_redemptions')->where('id', $redemption->id)->update(['discount_value' => 1])))
        ->toThrow(QueryException::class, 'Discount redemptions are immutable.')
        ->and(fn () => DB::transaction(fn () => DB::table('platform_billing_discount_redemptions')->where('id', $redemption->id)->delete()))
        ->toThrow(QueryException::class, 'Discount redemptions are immutable.');
});

/**
 * Run a write and force the plugin's deferred checks to run now. Tests sit in
 * one rolled-back transaction, so a deferred trigger would otherwise never
 * fire. The savepoint keeps a refused write from poisoning the test.
 */
function discountCodeWriteWithChecks(callable $write): void
{
    try {
        DB::transaction(function () use ($write): void {
            $write();
            DB::statement('SET CONSTRAINTS ALL IMMEDIATE');
        });
    } finally {
        DB::statement('SET CONSTRAINTS ALL DEFERRED');
    }
}

/**
 * A company-list code that names no company can never be redeemed (user,
 * 2026-11-11). The service refused it; the database now refuses it from any
 * writer, including removing a code's last company.
 */
it('refuses a company-list discount code with no company in the database', function () {
    $company = Company::factory()->create();
    $code = app(DiscountCodeService::class)->create(discountCodeSuperAdmin(), discountCodeTerms([
        'eligibility_type' => 'company_list',
        'company_ids'      => [$company->getKey()],
    ]));

    // A valid code passes the deferred check.
    discountCodeWriteWithChecks(fn () => DB::table('platform_billing_discount_codes')->where('id', $code->id)->update(['eligibility_type' => 'company_list']));

    expect(fn () => discountCodeWriteWithChecks(fn () => DB::table('platform_billing_discount_codes')->insert([
        'code'          => 'EMPTY-LIST', 'discount_type' => 'percentage', 'discount_value' => 1000,
        'duration_type' => 'forever', 'eligibility_type' => 'company_list',
        'created_by'    => discountCodeSuperAdmin()->getKey(), 'is_active' => true,
        'created_at'    => now(), 'updated_at' => now(),
    ])))->toThrow(QueryException::class, 'A company-list discount code must name at least one company.')
        ->and(fn () => discountCodeWriteWithChecks(fn () => DB::table('platform_billing_discount_code_companies')->where('discount_code_id', $code->id)->delete()))
        ->toThrow(QueryException::class, 'A company-list discount code must name at least one company.');
});

/**
 * Terms lock once any company has redeemed the code (user, 2026-11-11), so
 * later redeemers cannot get different terms under the same code. Switching
 * it off stays possible.
 */
it('locks a discount code terms after the first redemption but still allows deactivating it', function () {
    $actor = discountCodeSuperAdmin();
    $service = app(DiscountCodeService::class);
    $unused = $service->create($actor, discountCodeTerms());
    $used = $service->create($actor, discountCodeTerms());

    $unused->forceFill(['discount_value' => 2000])->save();
    $service->redeem($used->code, Company::factory()->create(), $actor);
    $used->refresh();

    expect($unused->fresh()->discount_value)->toBe(2000)
        ->and(fn () => $used->forceFill(['discount_value' => 9000])->save())
        ->toThrow(ValidationException::class, 'This discount code has been used, so its terms can no longer change.')
        ->and($service->setActive($used, $actor, false)->is_active)->toBeFalse();
});

/**
 * The last redemption cannot be taken twice: the code's own row lock
 * serialises redemptions, and the database refuses a count above the limit
 * whatever path writes it (user, 2026-11-11, instead of a two-connection
 * test that would be timing-dependent).
 */
it('refuses a redemption count above the limit in the database', function () {
    $code = app(DiscountCodeService::class)->create(discountCodeSuperAdmin(), discountCodeTerms(['max_redemptions' => 1]));

    expect(fn () => DB::transaction(fn () => DB::table('platform_billing_discount_codes')->where('id', $code->id)->update(['redemption_count' => 2])))
        ->toThrow(QueryException::class, 'platform_billing_discount_codes_count_check');
});
