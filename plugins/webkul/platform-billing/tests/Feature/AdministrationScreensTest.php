<?php

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Filament\Actions\Testing\TestAction;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;
use Spatie\Permission\PermissionRegistrar;
use Webkul\PlatformBilling\BillingCompanyContributions;
use Webkul\PlatformBilling\Filament\Clusters\Billing;
use Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\PlanResource\Pages\CreatePlan;
use Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\PlanResource\Pages\ListPlans;
use Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\PlanResource\Pages\ViewPlan;
use Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\SubscriptionResource\Pages\CreateSubscription;
use Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\SubscriptionResource\Pages\ListSubscriptions;
use Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\SubscriptionResource\Pages\ViewSubscription;
use Webkul\PlatformBilling\Models\Plan;
use Webkul\PlatformBilling\Models\Subscription;
use Webkul\PlatformBilling\Services\CompanyAccountType;
use Webkul\PlatformBilling\Services\PlanCatalog;
use Webkul\PlatformBilling\Services\SubscriptionDrafts;
use Webkul\PluginManager\CompanyContributions;
use Webkul\PluginManager\Models\Plugin;
use Webkul\Security\Filament\Resources\CompanyResource\Pages\EditCompany;
use Webkul\Security\Models\Permission;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\User;
use Webkul\Security\Services\MultiCompanyAdminRoleProvisioner;
use Webkul\Security\Services\SeatCapacityService;
use Webkul\Support\Models\Company;
use Webkul\Support\Services\CompanyContext;

require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/FilamentHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensureERPInstalled();
});

function billingAdmin(): User
{
    $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();
    billingAct($admin);

    return $admin;
}

function billingAct(User $user): void
{
    Auth::shouldUse('web');
    Auth::guard('web')->login($user);
    FilamentHelper::bootAdminPanel();
}

function billingCompany(array $attributes = []): Company
{
    return Company::factory()->create(array_merge([
        'phone'  => '+260211234567',
        'mobile' => '+260977234567',
    ], $attributes));
}

function billingSession(Company $company): void
{
    $user = Auth::user();

    if ($user instanceof User) {
        $user->allowedCompanies()->syncWithoutDetaching([$company->getKey()]);
    }

    session([
        CompanyContext::SESSION_KEY            => [$company->getKey()],
        CompanyContext::SCOPE_MODE_SESSION_KEY => CompanyContext::SCOPE_MODE_COMPANY,
    ]);
    app()->forgetInstance(CompanyContext::class);
}

function billingMultiCompanyAdmin(Company $company): User
{
    $user = User::withoutEvents(fn (): User => User::factory()->create([
        'default_company_id' => $company->getKey(),
        'is_active'          => true,
        'is_default'         => false,
    ]));
    $user->allowedCompanies()->sync([$company->getKey()]);
    Auth::shouldUse('web');
    $user->syncRoles([app(MultiCompanyAdminRoleProvisioner::class)->provision()->getKey()]);
    $user->unsetRelation('roles');
    Permission::findOrCreate('view_any_support_company', 'web');
    Permission::findOrCreate('view_support_company', 'web');
    Permission::findOrCreate('update_support_company', 'web');
    Permission::findOrCreate('view_any_platform_billing_plan', 'web');
    app(PermissionRegistrar::class)->forgetCachedPermissions();
    app(Webkul\Security\PermissionRegistrar::class)->forgetCachedPermissions();
    $user->givePermissionTo([
        'view_any_support_company',
        'view_support_company',
        'update_support_company',
        'view_any_platform_billing_plan',
    ]);
    billingAct($user->fresh());

    return $user->fresh();
}

function billingCompanyAction(string $name): TestAction
{
    return TestAction::make($name)->schemaComponent('platformBilling', 'form');
}

it('lets an active super admin open the catalog and still refuses delete', function () {
    $admin = billingAdmin();

    expect(Gate::forUser($admin)->allows('viewAny', Plan::class))->toBeTrue()
        ->and(Gate::forUser($admin)->allows('create', Plan::class))->toBeTrue()
        ->and(Gate::forUser($admin)->allows('delete', Plan::class))->toBeFalse();

    $inactive = User::withoutEvents(fn (): User => User::factory()->create([
        'is_active'  => false,
        'is_default' => false,
    ]));
    $role = Role::query()->whereRaw('LOWER(name) = ?', ['admin'])->firstOrFail();
    Auth::shouldUse('web');
    $inactive->syncRoles([$role->getKey()]);
    $inactive->unsetRelation('roles');

    expect($inactive->fresh()->isSuperAdmin())->toBeTrue()
        ->and(Gate::forUser($inactive->fresh())->allows('viewAny', Plan::class))->toBeFalse();
});

it('shows an empty plan list and paginates it', function () {
    billingAdmin();

    $page = Livewire::test(ListPlans::class);

    expect($page->instance()->getTable()->isPaginated())->toBeTrue()
        ->and(Plan::query()->count())->toBe(0);

    $page->assertSee(__('platform-billing::filament/billing.plans.empty'));
});

it('creates a plan from the screen and shows the stored price in dollars', function () {
    billingAdmin();
    $plugin = Plugin::query()->where('is_installed', true)->value('name');

    Livewire::test(CreatePlan::class)
        ->fillForm([
            'name'             => 'Growth',
            'code'             => 'growth-screen',
            'included_plugins' => $plugin ? [$plugin] : [],
            'seat_limit'       => 4,
            'amount'           => '12.34',
            'interval_months'  => 1,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $plan = Plan::query()->where('code', 'growth-screen')->firstOrFail();
    $price = $plan->prices()->firstOrFail();

    expect($price->amount_minor)->toBe(1234)
        ->and($price->currency)->toBe('USD')
        ->and($plan->seat_limit)->toBe(4);

    Livewire::test(ViewPlan::class, ['record' => $plan->getRouteKey()])
        ->assertSee('12.34')
        ->assertSee('USD');
});

it('refuses a duplicate plan code with the catalog message', function () {
    $admin = billingAdmin();
    app(PlanCatalog::class)->createPlan($admin, [
        'name'            => 'Taken',
        'code'            => 'taken-screen',
        'amount'          => '10.00',
        'interval_months' => 1,
    ]);

    Livewire::test(CreatePlan::class)
        ->fillForm([
            'name'            => 'Taken again',
            'code'            => 'taken-screen',
            'amount'          => '11.00',
            'interval_months' => 1,
        ])
        ->call('create')
        ->assertNotified(PlanCatalog::CODE_TAKEN);

    expect(Plan::query()->where('code', 'taken-screen')->count())->toBe(1);
});

it('adds a price and deactivates the price and the plan', function () {
    $admin = billingAdmin();
    $plan = app(PlanCatalog::class)->createPlan($admin, [
        'name'            => 'Retire',
        'code'            => 'retire-screen',
        'amount'          => '10.00',
        'interval_months' => 1,
    ]);

    Livewire::test(ViewPlan::class, ['record' => $plan->getRouteKey()])
        ->callAction('addPrice', data: ['amount' => '80.00', 'interval_months' => 12])
        ->assertNotified(__('platform-billing::filament/billing.plans.actions.saved'));

    $yearly = $plan->prices()->where('interval_months', 12)->firstOrFail();
    expect($yearly->amount_minor)->toBe(8000);

    Livewire::test(ViewPlan::class, ['record' => $plan->getRouteKey()])
        ->callAction('deactivatePrice', data: ['price_id' => $yearly->getKey()])
        ->assertNotified(__('platform-billing::filament/billing.plans.actions.saved'));

    expect($yearly->fresh()->is_active)->toBeFalse();

    Livewire::test(ViewPlan::class, ['record' => $plan->getRouteKey()])
        ->callAction('deactivatePlan')
        ->assertNotified(__('platform-billing::filament/billing.plans.actions.saved'));

    expect($plan->fresh()->is_active)->toBeFalse();
});

it('refuses a multi-company admin and a company admin the plan screens', function () {
    $company = billingCompany();
    $mca = billingMultiCompanyAdmin($company);

    expect($mca->can('view_any_platform_billing_plan'))->toBeTrue()
        ->and(Gate::forUser($mca)->allows('viewAny', Plan::class))->toBeFalse()
        ->and(Billing::canAccess())->toBeFalse();

    Livewire::test(ListPlans::class)->assertForbidden();
    Livewire::test(CreatePlan::class)->assertForbidden();
    Livewire::test(ListSubscriptions::class)->assertForbidden();
    Livewire::test(CreateSubscription::class)->assertForbidden();

    expect(fn () => app(PlanCatalog::class)->createPlan($mca, [
        'name'            => 'No',
        'code'            => 'no-mca',
        'amount'          => '10.00',
        'interval_months' => 1,
    ]))->toThrow(AuthorizationException::class, PlanCatalog::ONLY_SUPER_ADMIN);

    $clerk = User::withoutEvents(fn (): User => User::factory()->create([
        'default_company_id' => $company->getKey(),
        'is_active'          => true,
        'is_default'         => false,
    ]));
    Auth::shouldUse('web');
    $role = Role::query()->whereRaw('LOWER(name) = ?', [mb_strtolower(Role::COMPANY_ADMIN)])->first()
        ?? Role::create(['name' => Role::COMPANY_ADMIN, 'guard_name' => 'web']);
    $clerk->syncRoles([$role->getKey()]);
    $clerk->unsetRelation('roles');
    billingAct($clerk->fresh());

    expect(Billing::canAccess())->toBeFalse();
    Livewire::test(ListPlans::class)->assertForbidden();
    Livewire::test(ListSubscriptions::class)->assertForbidden();
});

it('drafts, activates, schedules, cancels and reactivates a subscription from the screen', function () {
    $company = billingCompany();
    $admin = billingAdmin();
    billingSession($company);
    $plan = app(PlanCatalog::class)->createPlan($admin, [
        'name'            => 'Sold',
        'code'            => 'sold-screen',
        'seat_limit'      => 3,
        'amount'          => '12.34',
        'interval_months' => 1,
    ]);
    $yearly = app(PlanCatalog::class)->addPrice($admin, $plan, '80.00', 12);

    Livewire::test(CreateSubscription::class)
        ->fillForm([
            'company_id' => $company->getKey(),
            'price_id'   => $plan->prices()->where('interval_months', 1)->value('id'),
            'anchor_at'  => '2026-11-10 00:00:00',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $subscription = Subscription::query()->where('company_id', $company->getKey())->firstOrFail();

    expect($subscription->status)->toBe('draft')
        ->and($subscription->amount_minor)->toBe(1234)
        ->and($subscription->currency)->toBe('USD');

    Livewire::test(ViewSubscription::class, ['record' => $subscription->getRouteKey()])
        ->assertSee('12.34')
        ->assertSee('USD')
        ->callAction('activate')
        ->assertNotified(__('platform-billing::filament/billing.subscriptions.actions.saved'));

    expect($subscription->fresh()->status)->toBe('active');

    Livewire::test(ViewSubscription::class, ['record' => $subscription->getRouteKey()])
        ->callAction('schedulePrice', data: ['price_id' => $yearly->getKey()])
        ->assertNotified(__('platform-billing::filament/billing.subscriptions.actions.saved'));

    expect($subscription->fresh()->scheduled_price_id)->toBe($yearly->getKey())
        ->and($subscription->fresh()->amount_minor)->toBe(1234);

    Livewire::test(ViewSubscription::class, ['record' => $subscription->getRouteKey()])
        ->callAction('cancelAtPeriodEnd')
        ->assertNotified(__('platform-billing::filament/billing.subscriptions.actions.saved'));

    expect($subscription->fresh()->cancel_at_period_end)->toBeTrue()
        ->and($subscription->fresh()->scheduled_price_id)->toBeNull();

    Livewire::test(ViewSubscription::class, ['record' => $subscription->getRouteKey()])
        ->callAction('reactivate')
        ->assertNotified(__('platform-billing::filament/billing.subscriptions.actions.saved'));

    expect($subscription->fresh()->cancel_at_period_end)->toBeFalse()
        ->and($subscription->fresh()->status)->toBe('active');
});

it('allows a second draft because drafts have no idempotency key', function () {
    $company = billingCompany();
    $admin = billingAdmin();
    billingSession($company);
    app(PlanCatalog::class)->createPlan($admin, [
        'name'            => 'Twice',
        'code'            => 'twice-screen',
        'amount'          => '10.00',
        'interval_months' => 1,
    ]);
    $priceId = Plan::query()->where('code', 'twice-screen')->firstOrFail()->prices()->value('id');

    Livewire::test(CreateSubscription::class)
        ->fillForm([
            'company_id' => $company->getKey(),
            'price_id'   => $priceId,
            'anchor_at'  => '2026-11-10 00:00:00',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    Livewire::test(CreateSubscription::class)
        ->fillForm([
            'company_id' => $company->getKey(),
            'price_id'   => $priceId,
            'anchor_at'  => '2026-11-10 00:00:00',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Subscription::query()->where('company_id', $company->getKey())->count())->toBe(2);
});

it('shows the lifecycle refusal when a draft is cancelled from the screen', function () {
    $company = billingCompany();
    $admin = billingAdmin();
    billingSession($company);
    $plan = app(PlanCatalog::class)->createPlan($admin, [
        'name'            => 'Early cancel',
        'code'            => 'early-cancel',
        'amount'          => '10.00',
        'interval_months' => 1,
    ]);
    $price = $plan->prices()->firstOrFail();
    $draft = app(SubscriptionDrafts::class)->create(
        $admin,
        $company,
        $price,
        new DateTimeImmutable('2026-11-10T00:00:00Z'),
    );

    Livewire::test(ViewSubscription::class, ['record' => $draft->getRouteKey()])
        ->callAction('cancelAtPeriodEnd')
        ->assertNotified('Subscription must be active.');

    expect($draft->fresh()->status)->toBe('draft');
});

it('adds trial days, switches an assigned company, and refuses the other company actions', function () {
    $company = billingCompany([
        'account_type' => 'test',
        'test_ends_at' => '2026-12-01 00:00:00',
    ]);
    $admin = billingAdmin();
    billingSession($company);
    $plan = app(PlanCatalog::class)->createPlan($admin, [
        'name'             => 'Switch',
        'code'             => 'switch-screen',
        'included_plugins' => [],
        'seat_limit'       => 2,
        'amount'           => '15.00',
        'interval_months'  => 1,
    ]);
    $priceId = $plan->prices()->value('id');
    $before = $company->fresh()->test_ends_at;

    Livewire::test(EditCompany::class, ['record' => $company->getRouteKey()])
        ->assertSee(__('platform-billing::filament/billing.company.description'))
        ->callAction(billingCompanyAction('addTrialDays'), data: ['test_days' => 5])
        ->assertNotified(__('platform-billing::filament/billing.company.saved'));

    expect($company->fresh()->test_ends_at->greaterThan($before))->toBeTrue();

    Livewire::test(EditCompany::class, ['record' => $company->getRouteKey()])
        ->callAction(billingCompanyAction('setSeatLimit'), data: ['seat_limit' => 6])
        ->assertNotified(__('platform-billing::filament/billing.company.saved'));

    expect(app(SeatCapacityService::class)->limitFor($company->fresh()))->toBe(6);

    $stranger = billingCompany(['name' => 'Stranger billing']);
    $mca = billingMultiCompanyAdmin($company);
    $beforeMca = $company->fresh()->test_ends_at;

    Livewire::test(EditCompany::class, ['record' => $company->getRouteKey()])
        ->callAction(billingCompanyAction('addTrialDays'), data: ['test_days' => 3])
        ->assertNotified(__('platform-billing::filament/billing.company.saved'));

    expect($company->fresh()->test_ends_at->greaterThan($beforeMca))->toBeTrue();

    Livewire::test(EditCompany::class, ['record' => $company->getRouteKey()])
        ->callAction(billingCompanyAction('setSeatLimit'), data: ['seat_limit' => 9])
        ->assertNotified(BillingCompanyContributions::ONLY_SUPER_ADMIN_SEATS);

    expect(app(SeatCapacityService::class)->limitFor($company->fresh()))->toBe(6);

    // Security's company page (the one production serves since 2026-11-11)
    // lists only a Multi-Company Admin's assigned companies, so another
    // company is not found rather than forbidden: its existence is not shown.
    expect(fn () => Livewire::test(EditCompany::class, ['record' => $stranger->getRouteKey()]))
        ->toThrow(ModelNotFoundException::class);

    Livewire::test(EditCompany::class, ['record' => $company->getRouteKey()])
        ->callAction(billingCompanyAction('switchToCommercial'), data: ['price_id' => $priceId])
        ->assertNotified(__('platform-billing::filament/billing.company.saved'));

    expect($company->fresh()->account_type)->toBe('commercial')
        ->and(Subscription::withoutGlobalScopes()->where('company_id', $company->getKey())->where('status', 'active')->exists())->toBeTrue();

    $commercial = billingCompany(['name' => 'Already commercial']);
    billingAct($admin);
    billingSession($commercial);

    Livewire::test(EditCompany::class, ['record' => $commercial->getRouteKey()])
        ->callAction(billingCompanyAction('addTrialDays'), data: ['test_days' => 5])
        ->assertNotified(CompanyAccountType::ONLY_TEST);

    Livewire::test(EditCompany::class, ['record' => $commercial->getRouteKey()])
        ->callAction(billingCompanyAction('switchToCommercial'), data: ['price_id' => $priceId])
        ->assertNotified(CompanyAccountType::ONLY_TEST_SWITCH);

    expect($commercial->fresh()->account_type)->toBe('commercial')
        ->and(Subscription::withoutGlobalScopes()->where('company_id', $commercial->getKey())->exists())->toBeFalse();
});

it('hides billing actions from a company admin and does not let them change modules', function () {
    $company = billingCompany([
        'account_type' => 'test',
        'test_ends_at' => '2026-12-01 00:00:00',
    ]);
    $plugin = Plugin::query()->updateOrCreate(
        ['name' => 'screens-mca-module'],
        ['is_installed' => true, 'is_active' => true],
    );
    $before = DB::table('company_plugin')->where('company_id', $company->getKey())->pluck('plugin_id')->all();

    $clerk = User::withoutEvents(fn (): User => User::factory()->create([
        'default_company_id' => $company->getKey(),
        'is_active'          => true,
        'is_default'         => false,
    ]));
    $clerk->allowedCompanies()->sync([$company->getKey()]);
    Auth::shouldUse('web');
    $role = Role::query()->whereRaw('LOWER(name) = ?', [mb_strtolower(Role::COMPANY_ADMIN)])->first()
        ?? Role::create(['name' => Role::COMPANY_ADMIN, 'guard_name' => 'web']);
    $clerk->syncRoles([$role->getKey()]);
    $clerk->unsetRelation('roles');
    Permission::findOrCreate('view_support_company', 'web');
    Permission::findOrCreate('update_support_company', 'web');
    app(PermissionRegistrar::class)->forgetCachedPermissions();
    app(Webkul\Security\PermissionRegistrar::class)->forgetCachedPermissions();
    $clerk->givePermissionTo(['view_support_company', 'update_support_company']);
    billingAct($clerk->fresh());
    billingSession($company);

    Livewire::test(EditCompany::class, ['record' => $company->getRouteKey()])
        ->assertDontSee(__('platform-billing::filament/billing.company.description'));

    CompanyContributions::syncGrants($company, [$plugin->getKey()]);

    expect(DB::table('company_plugin')->where('company_id', $company->getKey())->pluck('plugin_id')->all())
        ->toBe($before);
});

it('generates plan and subscription permissions and no billing cluster page permission', function () {
    billingAdmin();

    $pages = collect(FilamentShield::getPages())
        ->flatMap(fn (array $page): array => array_keys($page['permissions'] ?? []))
        ->all();

    $resources = collect(FilamentShield::getResources())
        ->flatMap(function (array $resource): array {
            return collect($resource['permissions'] ?? [])
                ->map(fn (mixed $permission): ?string => is_array($permission) ? ($permission['key'] ?? null) : (is_string($permission) ? $permission : null))
                ->filter()
                ->values()
                ->all();
        })
        ->all();

    expect($pages)->not->toContain('page_platform_billing_billing')
        ->and($resources)->toContain('view_any_platform_billing_plan')
        ->and($resources)->toContain('view_any_platform_billing_subscription');
});
