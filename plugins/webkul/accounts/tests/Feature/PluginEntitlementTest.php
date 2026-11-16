<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Webkul\Account\Models\Invoice;
use Webkul\Account\Models\PaymentRegister;
use Webkul\Account\Services\PaymentRegistrar;
use Webkul\PluginManager\Exceptions\PluginNotEntitledException;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Support\PluginAccess;
use Webkul\PluginManager\Support\PluginEntitlement;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('accounts');
    PluginAccess::flush();
});

it('enforces the accounts entitlement through a registered resource policy', function (): void {
    $companyA = Company::factory()->create();
    $companyB = Company::factory()->create();
    $user = CompanyHelper::actingAsCompanyUser($companyA, ['view_any_account_invoice']);
    $plugin = Plugin::query()->where('name', 'accounts')->firstOrFail();

    expect($plugin->is_installed)->toBeTrue()
        ->and(PluginEntitlement::enabledForCompany('accounts', (int) $companyA->getKey()))->toBeTrue()
        ->and(Gate::forUser($user)->allows('viewAny', Invoice::class))->toBeTrue();

    DB::table('company_plugin')
        ->where('company_id', $companyA->getKey())
        ->where('plugin_id', $plugin->getKey())
        ->delete();
    PluginAccess::flush();

    expect(Gate::forUser($user)->allows('viewAny', Invoice::class))->toBeFalse()
        ->and(PluginEntitlement::enabledForCompany('accounts', (int) $companyB->getKey()))->toBeTrue();
});

it('requires the payments entitlement when registering a tokenized payment', function (): void {
    TestBootstrapHelper::ensurePluginInstalled('payments');
    $company = Company::factory()->create();
    $paymentPlugin = Plugin::query()->where('name', 'payments')->firstOrFail();
    DB::table('company_plugin')
        ->where('company_id', $company->getKey())
        ->where('plugin_id', $paymentPlugin->getKey())
        ->delete();
    PluginAccess::flush();

    expect(PluginEntitlement::enabledForCompany('accounts', (int) $company->getKey()))->toBeTrue()
        ->and(PluginEntitlement::enabledForCompany('payments', (int) $company->getKey()))->toBeFalse()
        ->and(fn () => app(PaymentRegistrar::class)->register(new PaymentRegister([
            'company_id'       => $company->getKey(),
            'payment_token_id' => 1,
        ])))->toThrow(PluginNotEntitledException::class);
});
