<?php

use Filament\Actions\CreateAction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Once;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Webkul\Account\Settings\TaxesSettings;
use Webkul\PlatformBilling\Models\Plan;
use Webkul\PlatformBilling\Models\Price;
use Webkul\PlatformBilling\Models\Subscription;
use Webkul\PlatformBilling\Services\CompanyReadOnlyWriteGuard;
use Webkul\PlatformBilling\Services\SubscriptionDrafts;
use Webkul\PlatformBilling\Services\SubscriptionLifecycle;
use Webkul\Project\Models\Task;
use Webkul\Project\Models\TaskStage;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\User;
use Webkul\Support\Filament\Resources\CalendarResource\Pages\ListCalendars;
use Webkul\Support\Models\Calendar;
use Webkul\Support\Models\Company;
use Webkul\Support\Services\CompanyContext;

require_once __DIR__.'/../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

class WriteCalendarForCompanyJob implements ShouldQueue
{
    public function __construct(public int $companyId) {}

    public function handle(CompanyContext $companyContext): void
    {
        $companyContext->runFor($this->companyId, function (): void {
            Calendar::query()->create([
                'name'       => 'queued read-only write',
                'timezone'   => 'UTC',
                'company_id' => $this->companyId,
            ]);
        });
    }
}

beforeEach(function (): void {
    TestBootstrapHelper::ensureERPInstalled();
    TestBootstrapHelper::ensurePluginInstalled('projects');
});

function makeCompanyWithActiveSubscription(): array
{
    $company = Company::factory()->create();
    $staff = User::query()->where('email', 'admin@example.com')->firstOrFail();
    $plan = Plan::query()->create([
        'name'             => 'Read only test plan',
        'code'             => 'readonly-'.fake()->unique()->numerify('######'),
        'included_plugins' => [],
    ]);
    $price = Price::query()->create([
        'plan_id'         => $plan->getKey(),
        'currency'        => 'USD',
        'interval_months' => 1,
        'amount_minor'    => 1000,
    ]);
    $draft = app(SubscriptionDrafts::class)->create(
        $staff,
        $company,
        $price,
        new DateTimeImmutable('2026-01-01T00:00:00Z'),
    );
    $active = app(SubscriptionLifecycle::class)->activate($draft, $staff, new DateTimeImmutable('2026-01-01T00:00:00Z'));

    return [$company, $staff, $active];
}

function markCompanyReadOnly(Subscription $subscription, User $staff): void
{
    app(SubscriptionLifecycle::class)->recordOverdue(
        $subscription,
        $staff,
        new DateTimeImmutable('2026-10-01T00:00:00Z'),
    );
}

function authenticateForReadOnlyCompany(Company $company, array $permissions = []): User
{
    $user = SecurityHelper::authenticateWithPermissions($permissions);
    $user->allowedCompanies()->sync([$company->getKey()]);
    $user->forceFill(['default_company_id' => $company->getKey()])->saveQuietly();
    session([CompanyContext::SESSION_KEY => [$company->getKey()]]);
    app()->forgetInstance(CompanyContext::class);

    return $user;
}

function makeCompanyCalendar(Company $company, string $name = 'Existing calendar'): Calendar
{
    return Calendar::factory()->create([
        'company_id' => $company->getKey(),
        'name'       => $name,
    ]);
}

function expectReadOnlyRefusal(Closure $write): void
{
    try {
        $write();
    } catch (ValidationException $exception) {
        expect($exception->errors()['company_id'] ?? [])->toBe([CompanyReadOnlyWriteGuard::MESSAGE]);

        return;
    }

    throw new RuntimeException('The read-only company write was not refused.');
}

it('refuses a read-only company user from creating a tenant model with a recovery message', function () {
    [$company, $staff, $subscription] = makeCompanyWithActiveSubscription();
    markCompanyReadOnly($subscription, $staff);
    authenticateForReadOnlyCompany($company);

    expectReadOnlyRefusal(fn () => Calendar::query()->create([
        'name'       => 'Forbidden calendar',
        'timezone'   => 'UTC',
        'company_id' => $company->getKey(),
    ]));
});

it('refuses a read-only company user from updating a tenant model', function () {
    [$company, $staff, $subscription] = makeCompanyWithActiveSubscription();
    $calendar = makeCompanyCalendar($company);
    markCompanyReadOnly($subscription, $staff);
    authenticateForReadOnlyCompany($company);

    expectReadOnlyRefusal(fn () => $calendar->forceFill(['name' => 'Forbidden update'])->save());
});

it('refuses a read-only company user from deleting a tenant model', function () {
    [$company, $staff, $subscription] = makeCompanyWithActiveSubscription();
    $calendar = makeCompanyCalendar($company);
    markCompanyReadOnly($subscription, $staff);
    authenticateForReadOnlyCompany($company);

    expectReadOnlyRefusal(fn () => $calendar->delete());
});

it('refuses a read-only company Filament create action', function () {
    [$company, $staff, $subscription] = makeCompanyWithActiveSubscription();
    markCompanyReadOnly($subscription, $staff);
    authenticateForReadOnlyCompany($company, ['view_any_support_calendar', 'create_support_calendar']);

    Livewire::test(ListCalendars::class)
        ->callAction(CreateAction::class, [
            'name'          => 'Action calendar',
            'timezone'      => 'UTC',
            'company_id'    => $company->getKey(),
            'hours_per_day' => 8,
        ])
        ->assertNotified(CompanyReadOnlyWriteGuard::MESSAGE);

    expect(Calendar::withoutGlobalScopes()->where('name', 'Action calendar')->exists())->toBeFalse();
});

it('lets platform billing write its own subscription and invoice records for a read-only company', function () {
    [$company, $staff, $subscription] = makeCompanyWithActiveSubscription();
    markCompanyReadOnly($subscription, $staff);
    authenticateForReadOnlyCompany($company);

    expect(app(SubscriptionLifecycle::class)->isReadOnly($company))->toBeTrue();

    $subscription->refresh()->forceFill(['cancel_at_period_end' => true])->save();
    expect($subscription->refresh()->cancel_at_period_end)->toBeTrue();

    expectReadOnlyRefusal(fn () => Calendar::query()->create([
        'name'       => 'Still refused',
        'timezone'   => 'UTC',
        'company_id' => $company->getKey(),
    ]));
});

it('refuses a queued company write after the job restores its forced company context', function () {
    [$company, $staff, $subscription] = makeCompanyWithActiveSubscription();
    markCompanyReadOnly($subscription, $staff);
    authenticateForReadOnlyCompany($company);

    expectReadOnlyRefusal(fn () => dispatch_sync(new WriteCalendarForCompanyJob((int) $company->getKey())));
});

it('refuses an API write for a read-only company and keeps reads available', function () {
    [$company, $staff, $subscription] = makeCompanyWithActiveSubscription();
    $user = authenticateForReadOnlyCompany($company, ['create_project_task', 'view_project_task']);
    $existingTask = Task::factory()->create([
        'company_id' => $company->getKey(),
        'creator_id' => $user->getKey(),
    ]);
    markCompanyReadOnly($subscription, $staff);
    $stage = TaskStage::query()->whereKey($existingTask->stage_id)->firstOrFail();

    $this->getJson(route('admin.api.v1.projects.tasks.show', $existingTask))
        ->assertOk()
        ->assertJsonPath('data.id', $existingTask->getKey());

    $this->postJson(route('admin.api.v1.projects.tasks.store'), [
        'title'    => 'API write refused',
        'state'    => 'in_progress',
        'stage_id' => $stage->getKey(),
    ])
        ->assertUnprocessable()
        ->assertJsonPath('errors.company_id.0', CompanyReadOnlyWriteGuard::MESSAGE);
});

it('allows active companies and installation staff to write tenant models', function () {
    [$company, $staff, $subscription] = makeCompanyWithActiveSubscription();
    authenticateForReadOnlyCompany($company);

    $activeCalendar = Calendar::factory()->create([
        'name'       => 'Active company calendar',
        'company_id' => $company->getKey(),
    ]);
    expect($activeCalendar->exists)->toBeTrue();

    markCompanyReadOnly($subscription, $staff);
    Auth::guard('web')->login($staff);
    Auth::shouldUse('web');
    app()->forgetInstance(CompanyContext::class);

    $staffCalendar = Calendar::factory()->create([
        'name'       => 'Installation staff recovery calendar',
        'company_id' => $company->getKey(),
    ]);
    expect($staffCalendar->exists)->toBeTrue();
});

/**
 * A Multi-Company Admin used to pass in every company. They pass now only in
 * companies assigned to them (user, 2026-11-10), as every other rule limits
 * them to their assignment.
 */
it('lets a multi-company admin write only into the read-only companies assigned to them', function () {
    [$assigned, $staff, $assignedSubscription] = makeCompanyWithActiveSubscription();
    [$unassigned, , $unassignedSubscription] = makeCompanyWithActiveSubscription();
    markCompanyReadOnly($assignedSubscription, $staff);
    markCompanyReadOnly($unassignedSubscription, $staff);

    $admin = authenticateForReadOnlyCompany($assigned);
    $admin->assignRole(Role::query()->whereRaw('LOWER(name) = ?', [mb_strtolower(Role::MULTI_COMPANY_ADMIN)])->firstOrFail());
    $admin->unsetRelation('roles');
    app()->forgetInstance(CompanyContext::class);

    expect($admin->isMultiCompanyAdmin())->toBeTrue();

    $calendar = Calendar::factory()->create([
        'name'       => 'Assigned recovery calendar',
        'company_id' => $assigned->getKey(),
    ]);
    expect($calendar->exists)->toBeTrue();

    expectReadOnlyRefusal(fn () => Calendar::query()->create([
        'name'       => 'Unassigned calendar',
        'timezone'   => 'UTC',
        'company_id' => $unassigned->getKey(),
    ]));
});

/**
 * Company settings are rows written by query, so no model event reached the
 * guard and a locked company could still save them (2026-11-10).
 */
it('refuses a read-only company saving its own company settings', function () {
    TestBootstrapHelper::ensurePluginInstalled('accounts');

    [$company, $staff, $subscription] = makeCompanyWithActiveSubscription();
    markCompanyReadOnly($subscription, $staff);
    authenticateForReadOnlyCompany($company);

    expectReadOnlyRefusal(fn () => app(TaxesSettings::class)->save());
});

it('refuses expired and cancelled subscriptions while leaving active subscriptions writable', function () {
    [$expiredCompany, , $expired] = makeCompanyWithActiveSubscription();
    [$cancelledCompany, , $cancelled] = makeCompanyWithActiveSubscription();
    [$activeCompany] = makeCompanyWithActiveSubscription();
    $expired->forceFill(['status' => 'expired'])->saveQuietly();
    $cancelled->forceFill([
        'cancel_at_period_end' => true,
        'period_end_at'        => new DateTimeImmutable('2026-01-02T00:00:00Z'),
    ])->saveQuietly();

    expect(app(SubscriptionLifecycle::class)->isReadOnly($expiredCompany))->toBeTrue()
        ->and(app(SubscriptionLifecycle::class)->isReadOnly($cancelledCompany))->toBeTrue()
        ->and(app(SubscriptionLifecycle::class)->isReadOnly($activeCompany))->toBeFalse();
});

/**
 * An installation behind on migrations has no subscriptions table yet. A
 * company save in an older migration (accounts sequences) reached the guard,
 * which queried the missing table and stopped the upgrade (2026-11-12).
 */
it('treats a company as writable while the subscriptions table does not exist yet', function () {
    $company = Company::factory()->create();

    DB::statement('ALTER TABLE platform_billing_subscriptions RENAME TO platform_billing_subscriptions_hidden');

    try {
        Once::flush();

        expect(app(SubscriptionLifecycle::class)->isReadOnly($company))->toBeFalse();

        $company->forceFill(['name' => 'Saved before billing tables exist'])->save();

        expect($company->fresh()->name)->toBe('Saved before billing tables exist');
    } finally {
        DB::statement('ALTER TABLE platform_billing_subscriptions_hidden RENAME TO platform_billing_subscriptions');
        Once::flush();
    }
});
