<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Spatie\Permission\PermissionRegistrar;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\PaymentState;
use Webkul\Account\Filament\Resources\BillResource\Pages\EditBill;
use Webkul\Account\Filament\Resources\InvoiceResource\Actions\PayAction;
use Webkul\Account\Filament\Resources\InvoiceResource\Pages\CreateInvoice;
use Webkul\Account\Filament\Resources\InvoiceResource\Pages\EditInvoice;
use Webkul\Account\Models\Move;
use Webkul\Account\Models\Payment;
use Webkul\Account\Services\QuickPayment;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\Security\Models\Permission;
use Webkul\Security\Models\Role;
use Webkul\Security\Services\RoleTemplateProvisioner;
use Webkul\Security\Services\TenantRoleAssigner;
use Webkul\Security\Services\TenantRoleResolver;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../Helpers/AccountHelper.php';

/**
 * "Record payment", Zoho style (user, 2026-11-26): the Pay button on a
 * confirmed invoice or bill is renamed, made the main action, and needs the
 * Invoices module's payment permission. Before, anyone who could open the
 * document could record money against it. "Paid now" follows the same rule.
 */
beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('accounts');

    DB::table('plugins')->updateOrInsert(
        ['name' => 'accounts'],
        ['is_installed' => true, 'is_active' => true, 'updated_at' => now()],
    );

    Package::$plugins = Plugin::all()->keyBy('name');

    URL::resolveMissingNamedRoutesUsing(fn () => '#');
});

function recordPaymentInvoice(): Move
{
    AccountHelper::bankJournal();

    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE);
    AccountHelper::productLine($invoice, AccountHelper::account('income'), qty: 2, priceUnit: 100);

    return AccountHelper::post($invoice);
}

it('hides record payment from a user without the payment permission and refuses a direct call', function () {
    FilamentHelper::actingAs(['view_any_account_invoice', 'update_account_invoice']);

    $invoice = recordPaymentInvoice();
    $paymentsBefore = Payment::query()->withoutGlobalScopes()->count();

    Livewire::test(EditInvoice::class, ['record' => $invoice->id])
        ->assertOk()
        ->assertActionHidden(PayAction::class)
        // What a crafted request sends; callAction() would stop at "hidden".
        ->call('mountAction', PayAction::getDefaultName());

    expect($invoice->refresh()->payment_state)->toBe(PaymentState::NOT_PAID)
        ->and(Payment::query()->withoutGlobalScopes()->count())->toBe($paymentsBefore);
});

it('lets a user with the payment permission record a payment under the new name', function () {
    FilamentHelper::actingAs(['view_any_account_invoice', 'update_account_invoice', QuickPayment::PERMISSION]);

    $invoice = recordPaymentInvoice();

    Livewire::test(EditInvoice::class, ['record' => $invoice->id])
        ->assertActionVisible(PayAction::class)
        ->assertActionHasLabel(PayAction::class, __('accounts::filament/resources/invoice/actions/pay-action.title'))
        ->callAction(PayAction::class);

    expect(__('accounts::filament/resources/invoice/actions/pay-action.title'))->toBe('Record payment')
        ->and($invoice->refresh()->payment_state)->toBeIn([PaymentState::PAID, PaymentState::IN_PAYMENT]);
});

it('names the bill button record payment too and applies the same permission', function () {
    FilamentHelper::actingAs(['view_any_account_bill', 'update_account_bill']);

    AccountHelper::bankJournal();
    $bill = AccountHelper::invoice(MoveType::IN_INVOICE, null, null, ['invoice_date' => now()]);
    AccountHelper::productLine($bill, AccountHelper::account('expense'), qty: 1, priceUnit: 100);
    $bill = AccountHelper::post($bill);

    Livewire::test(EditBill::class, ['record' => $bill->id])
        ->assertActionHidden(PayAction::class);

    FilamentHelper::actingAs(['view_any_account_bill', 'update_account_bill', QuickPayment::PERMISSION]);

    Livewire::test(EditBill::class, ['record' => $bill->id])
        ->assertActionVisible(PayAction::class)
        ->assertActionHasLabel(PayAction::class, 'Record payment');
});

it('neither shows nor presets paid now without the payment permission', function () {
    FilamentHelper::actingAs(['view_any_account_invoice', 'create_account_invoice']);

    Livewire::withQueryParams(['direct_sale' => 1])
        ->test(CreateInvoice::class)
        ->assertFormSet(['paid_now' => false])
        ->assertFormFieldHidden('paid_now');

    expect(fn () => app(QuickPayment::class)->confirmAndPay(recordPaymentInvoice(), 1))
        ->toThrow(ValidationException::class, 'You are not allowed to record payments.');
});

/**
 * User, 2026-11-26: sales staff take money at the counter, so the Sales
 * Manager template records payments. An existing Sales Manager role gets it
 * once, from its migration; a later removal on the Roles screen survives
 * the next sync, like any other edit to a template role.
 */
it('gives the Sales Manager template the payment permission, and an existing Sales Manager role once', function () {
    $permission = Permission::query()->firstOrCreate(['name' => QuickPayment::PERMISSION, 'guard_name' => 'web']);
    $provisioner = app(RoleTemplateProvisioner::class);
    $heldBy = fn (Role $role): array => $role->fresh()->permissions()->pluck('name')->all();

    Role::query()->whereRaw('LOWER(name) = ?', ['sales manager'])->get()->each->delete();
    $existing = Role::query()->create(['name' => 'Sales Manager', 'guard_name' => 'web']);

    (require __DIR__.'/../../../../security/database/migrations/2026_11_26_000001_grant_sales_manager_payment_permission.php')->up();

    expect($heldBy($existing))->toContain(QuickPayment::PERMISSION)
        ->and($provisioner->addExtraPermissions('Sales Manager'))->toBe(0);

    $existing->revokePermissionTo($permission);
    $provisioner->provision();

    expect($heldBy($existing))->not->toContain(QuickPayment::PERMISSION);

    $existing->delete();
    $provisioner->provision();

    expect($heldBy(Role::query()->where('name', 'Sales Manager')->firstOrFail()))->toContain(QuickPayment::PERMISSION);
});

/**
 * A Company Admin holds the permission through the administrators' business
 * role, which erp:sync-permissions fills with every template permission; the
 * finance templates carry the Invoices module's payment permission.
 */
it('gives the administrators business role the payment permission', function () {
    TestBootstrapHelper::ensurePluginInstalled('invoices');

    $provisioner = app(RoleTemplateProvisioner::class);
    $provisioner->provision();

    $business = Role::query()->create(['name' => 'Business Test '.uniqid(), 'guard_name' => 'web']);
    $provisioner->coverBusinessRole((int) $business->getKey());

    expect($provisioner->grants(RoleTemplateProvisioner::TEMPLATES['Finance Manager'], QuickPayment::PERMISSION))->toBeTrue()
        ->and($business->fresh()->permissions()->pluck('name')->all())->toContain(QuickPayment::PERMISSION);

    $admin = FilamentHelper::actingAs([]);
    // A panel request, on the web guard the permission row belongs to (the
    // helper leaves Sanctum as the default, as for API tests).
    Auth::shouldUse('web');

    expect(QuickPayment::mayRecordPayment())->toBeFalse();

    // Tenant roles belong to a company membership (SAAS-01), which is how a
    // Company Admin receives the business role; a global assignment would
    // not count. The membership is in the session's company, which is what
    // the check reads.
    $companyId = (int) current_company_id();
    $admin->allowedCompanies()->syncWithoutDetaching([$companyId]);
    app(TenantRoleAssigner::class)->sync($admin, [$business->getKey()], [$companyId]);
    $admin->unsetRelation('roles')->unsetRelation('permissions');
    // Two registrars hold separate caches in this fork; flush both. The role
    // is given in an earlier request in real use, so start a fresh resolver.
    app(PermissionRegistrar::class)->forgetCachedPermissions();
    app(Webkul\Security\PermissionRegistrar::class)->forgetCachedPermissions();
    app()->forgetInstance(TenantRoleResolver::class);

    expect(QuickPayment::mayRecordPayment())->toBeTrue();
});
