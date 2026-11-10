<?php

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\DiscountCodeResource\Pages\CreateDiscountCode;
use Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\DiscountCodeResource\Pages\ListDiscountCodes;
use Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\DiscountCodeResource\Pages\ViewDiscountCode;
use Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\InvoiceResource\Pages\ListInvoices;
use Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\InvoiceResource\Pages\ViewInvoice;
use Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\PaymentResource\Pages\CreatePayment;
use Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\PaymentResource\Pages\ViewPayment;
use Webkul\PlatformBilling\Filament\Pages\BillingAudit;
use Webkul\PlatformBilling\Filament\Pages\CompanyAccount;
use Webkul\PlatformBilling\Models\DiscountCode;
use Webkul\PlatformBilling\Models\Invoice;
use Webkul\PlatformBilling\Models\Payment;
use Webkul\PlatformBilling\Services\OverdueExtensions;
use Webkul\PlatformBilling\Services\PlanCatalog;
use Webkul\PlatformBilling\Services\RecurringInvoiceGenerator;
use Webkul\PlatformBilling\Services\SubscriptionDrafts;
use Webkul\PlatformBilling\Services\SubscriptionLifecycle;
use Webkul\Security\Models\Invitation;
use Webkul\Security\Models\Permission;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\User;
use Webkul\Security\Services\MultiCompanyAdminRoleProvisioner;
use Webkul\Support\Models\Company;
use Webkul\Support\Services\CompanyContext;

require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/FilamentHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensureERPInstalled();
    config([
        'platform-billing.issuer.name'        => 'DEKA',
        'platform-billing.issuer.tax_rate'    => '15.0000',
        'platform-billing.issuer.tax_enabled' => true,
    ]);
});

function screenAdmin(): User
{
    $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();
    Auth::shouldUse('web');
    Auth::guard('web')->login($admin);
    FilamentHelper::bootAdminPanel();

    return $admin;
}

function screenCompany(array $attributes = []): Company
{
    return Company::factory()->create(array_merge([
        'phone'  => '+260211234567',
        'mobile' => '+260977234567',
    ], $attributes));
}

function screenSession(Company $company): void
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

function screenInvoice(User $admin, Company $company): Invoice
{
    $plan = app(PlanCatalog::class)->createPlan($admin, [
        'name'             => 'Screen '.$company->getKey(),
        'code'             => 'screen-'.$company->getKey().'-'.uniqid(),
        'included_plugins' => [],
        'seat_limit'       => 2,
        'amount'           => '100.00',
        'interval_months'  => 1,
    ]);
    $draft = app(SubscriptionDrafts::class)->create(
        $admin,
        $company,
        $plan->prices()->firstOrFail(),
        new DateTimeImmutable('2026-01-01T00:00:00Z'),
    );
    $active = app(SubscriptionLifecycle::class)->activate($draft, $admin, new DateTimeImmutable('2026-01-01T00:00:00Z'));
    app(RecurringInvoiceGenerator::class)->generateDue((int) $active->getKey(), new DateTimeImmutable('2026-02-01T00:00:00Z'));

    return Invoice::withoutGlobalScopes()->where('subscription_id', $active->getKey())->firstOrFail();
}

function screenCompanyAdmin(Company $company): User
{
    $user = User::withoutEvents(fn (): User => User::factory()->create([
        'default_company_id' => $company->getKey(),
        'is_active'          => true,
        'is_default'         => false,
    ]));
    $user->allowedCompanies()->sync([$company->getKey()]);
    Auth::shouldUse('web');
    $role = Role::query()->whereRaw('LOWER(name) = ?', [mb_strtolower(Role::COMPANY_ADMIN)])->first()
        ?? Role::create(['name' => Role::COMPANY_ADMIN, 'guard_name' => 'web']);
    $user->syncRoles([$role->getKey()]);
    $user->unsetRelation('roles');
    Permission::findOrCreate('page_platform_billing_company_account', 'web');
    app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    app(\Webkul\Security\PermissionRegistrar::class)->forgetCachedPermissions();
    $user->givePermissionTo('page_platform_billing_company_account');
    Auth::guard('web')->login($user->fresh());
    FilamentHelper::bootAdminPanel();

    return $user->fresh();
}

it('shows an empty invoice list and the posted snapshot', function () {
    $admin = screenAdmin();
    $company = screenCompany();
    screenSession($company);

    Livewire::test(ListInvoices::class)
        ->assertSee(__('platform-billing::filament/billing.invoices.empty'));

    expect(Livewire::test(ListInvoices::class)->instance()->getTable()->isPaginated())->toBeTrue();

    $invoice = screenInvoice($admin, $company);

    Livewire::test(ViewInvoice::class, ['record' => $invoice->getRouteKey()])
        ->assertSee($invoice->number)
        ->assertSee('100.00')
        ->assertSee('DEKA')
        ->assertSee('USD');
});

it('posts a payment once for the same reference, then allocates, reverses and refunds', function () {
    $admin = screenAdmin();
    $company = screenCompany();
    screenSession($company);
    $invoice = screenInvoice($admin, $company);
    screenAdmin();
    screenSession($company);

    Livewire::test(CreatePayment::class)
        ->fillForm([
            'company_id'  => $company->getKey(),
            'amount'      => '40.00',
            'reference'   => 'SCREEN-PAY',
            'received_at' => '2026-02-02 00:00:00',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    Livewire::test(CreatePayment::class)
        ->fillForm([
            'company_id'  => $company->getKey(),
            'amount'      => '40.00',
            'reference'   => 'SCREEN-PAY',
            'received_at' => '2026-02-02 00:00:00',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Payment::withoutGlobalScopes()->where('company_id', $company->getKey())->count())->toBe(1);

    $payment = Payment::withoutGlobalScopes()->where('idempotency_key', 'SCREEN-PAY')->firstOrFail();

    Livewire::test(ViewPayment::class, ['record' => $payment->getRouteKey()])
        ->assertSee('40.00')
        ->callAction('allocate', data: ['invoice_id' => $invoice->getKey(), 'amount' => '10.00']);

    expect(app(\Webkul\PlatformBilling\Services\PlatformPayments::class)->invoiceBalanceMinor($invoice->fresh()))
        ->toBe((int) $invoice->total_minor - 1000);

    Livewire::test(ViewPayment::class, ['record' => $payment->getRouteKey()])
        ->callAction('reversePayment', data: ['reference' => 'SCREEN-REV', 'reason' => '   '])
        ->assertNotified(\Webkul\PlatformBilling\Services\PlatformPayments::REASON_REQUIRED);

    Livewire::test(ViewPayment::class, ['record' => $payment->getRouteKey()])
        ->callAction('refundCredit', data: [
            'amount'    => '5.00',
            'reference' => 'SCREEN-REFUND',
            'reason'    => 'Customer overpaid',
        ]);

    Livewire::test(ViewPayment::class, ['record' => $payment->getRouteKey()])
        ->callAction('refundCredit', data: [
            'amount'    => '5.00',
            'reference' => 'SCREEN-REFUND',
            'reason'    => 'Customer overpaid',
        ]);

    expect(\Webkul\PlatformBilling\Models\Reversal::withoutGlobalScopes()->where('payment_id', $payment->getKey())->count())->toBe(1);
});

it('refuses a multi-company admin and a company admin the platform money screens', function () {
    $company = screenCompany();
    $user = User::withoutEvents(fn (): User => User::factory()->create([
        'default_company_id' => $company->getKey(),
        'is_active'          => true,
        'is_default'         => false,
    ]));
    $user->allowedCompanies()->sync([$company->getKey()]);
    Auth::shouldUse('web');
    $user->syncRoles([app(MultiCompanyAdminRoleProvisioner::class)->provision()->getKey()]);
    $user->unsetRelation('roles');
    Auth::guard('web')->login($user->fresh());
    FilamentHelper::bootAdminPanel();

    Livewire::test(ListInvoices::class)->assertForbidden();
    Livewire::test(CreatePayment::class)->assertForbidden();
    Livewire::test(ListDiscountCodes::class)->assertForbidden();
    Livewire::test(BillingAudit::class)->assertForbidden();
    Livewire::test(CompanyAccount::class)->assertForbidden();

    $clerk = screenCompanyAdmin($company);
    screenSession($company);

    expect($clerk->can('page_platform_billing_company_account'))->toBeTrue();
    Livewire::test(ListInvoices::class)->assertForbidden();
    Livewire::test(CreatePayment::class)->assertForbidden();
});

it('creates and deactivates a discount code from the screen', function () {
    screenAdmin();

    Livewire::test(CreateDiscountCode::class)
        ->fillForm([
            'code'             => 'screen10',
            'discount_type'    => 'percentage',
            'discount_value'   => '10.00',
            'duration_type'    => 'one_invoice',
            'eligibility_type' => 'any_company',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $code = DiscountCode::query()->where('code', 'SCREEN10')->firstOrFail();
    expect($code->discount_value)->toBe(1000)
        ->and($code->is_active)->toBeTrue();

    Livewire::test(ViewDiscountCode::class, ['record' => $code->getRouteKey()])
        ->callAction('deactivate');

    expect($code->fresh()->is_active)->toBeFalse();
});

it('shows one company its own bill and refuses the other company and the invitation change', function () {
    $admin = screenAdmin();
    $own = screenCompany(['name' => 'Own billing screen']);
    $other = screenCompany(['name' => 'Other billing screen']);
    screenSession($own);
    $ownInvoice = screenInvoice($admin, $own);
    screenAdmin();
    screenSession($other);
    $otherInvoice = screenInvoice($admin, $other);

    Invitation::query()->create([
        'email'              => 'own-pending@example.com',
        'default_company_id' => $own->getKey(),
        'company_ids'        => [$own->getKey()],
        'role_ids'           => [],
        'expires_at'         => now()->addDays(7),
    ]);
    Invitation::query()->create([
        'email'              => 'other-pending@example.com',
        'default_company_id' => $other->getKey(),
        'company_ids'        => [$other->getKey()],
        'role_ids'           => [],
        'expires_at'         => now()->addDays(7),
    ]);

    screenCompanyAdmin($own);
    screenSession($own);

    Livewire::test(CompanyAccount::class)
        ->assertSee($ownInvoice->number)
        ->assertDontSee($otherInvoice->number)
        ->assertSee('own-pending@example.com')
        ->assertDontSee('other-pending@example.com')
        ->callAction('extend', data: ['invoice_id' => $ownInvoice->getKey()]);

    expect(\Webkul\PlatformBilling\Models\OverdueExtension::withoutGlobalScopes()->where('invoice_id', $ownInvoice->getKey())->where('kind', 'company_admin')->exists())->toBeTrue();

    Livewire::test(CompanyAccount::class)
        ->callAction('extend', data: ['invoice_id' => $ownInvoice->getKey()])
        ->assertNotified(OverdueExtensions::ALREADY_COMPANY_ADMIN);

    $invitation = Invitation::query()->where('email', 'own-pending@example.com')->firstOrFail();

    Livewire::test(CompanyAccount::class)
        ->callAction('revokeInvitation', data: ['invitation_id' => $invitation->getKey()])
        ->assertNotified('You may not manage invitations for this company.');

    expect($invitation->fresh()->isPending())->toBeTrue();

    expect(fn () => Livewire::test(CompanyAccount::class)
        ->callAction('extend', data: ['invoice_id' => $otherInvoice->getKey()]))
        ->toThrow(NotFoundHttpException::class);
});

it('refuses a blank super admin extension reason and records the billing audit', function () {
    $admin = screenAdmin();
    $company = screenCompany();
    screenSession($company);
    $invoice = screenInvoice($admin, $company);
    screenAdmin();
    screenSession($company);

    Livewire::test(ViewInvoice::class, ['record' => $invoice->getRouteKey()])
        ->callAction('extendAsSuperAdmin', data: [
            'length' => 2,
            'unit'   => 'week',
            'reason' => '   ',
        ])
        ->assertNotified(OverdueExtensions::REASON_REQUIRED);

    Livewire::test(BillingAudit::class)
        ->assertSee('platform_billing.');

    $pages = collect(FilamentShield::getPages())
        ->flatMap(fn (array $page): array => array_keys($page['permissions'] ?? []))
        ->all();
    $resources = collect(FilamentShield::getResources())
        ->flatMap(function (array $resource): array {
            return collect($resource['permissions'] ?? [])
                ->map(fn (mixed $permission): ?string => is_array($permission) ? ($permission['key'] ?? null) : null)
                ->filter()
                ->all();
        })
        ->all();

    expect($pages)->toContain('page_platform_billing_billing_audit')
        ->and($pages)->toContain('page_platform_billing_company_account')
        ->and($pages)->not->toContain('page_platform_billing_billing')
        ->and($resources)->toContain('view_any_platform_billing_invoice')
        ->and($resources)->toContain('view_any_platform_billing_payment')
        ->and($resources)->toContain('view_any_platform_billing_discount::code');
});

it('refuses a direct catalog call from a company admin', function () {
    $company = screenCompany();
    $clerk = screenCompanyAdmin($company);

    expect(fn () => app(PlanCatalog::class)->createPlan($clerk, [
        'name'            => 'No',
        'code'            => 'no-clerk',
        'amount'          => '10.00',
        'interval_months' => 1,
    ]))->toThrow(AuthorizationException::class, PlanCatalog::ONLY_SUPER_ADMIN);
});
