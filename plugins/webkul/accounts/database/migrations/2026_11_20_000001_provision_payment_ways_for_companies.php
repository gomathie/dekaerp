<?php

use Illuminate\Database\Migrations\Migration;
use Webkul\Account\Services\AccountingSetupService;
use Webkul\Account\Services\PaymentWaysProvisioner;
use Webkul\Support\Models\Company;

/**
 * Cash, Bank transfer and Mobile Money for every company already set up
 * (user, 2026-11-20). PaymentWaysProvisioner renames only untouched default
 * journal names and adds Mobile Money with its own account once; existing
 * entries are not changed. Same switch as automatic setup, so test
 * databases are left as they are.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! config('app.accounting_auto_setup', true)) {
            return;
        }

        $setup = app(AccountingSetupService::class);
        $ways = app(PaymentWaysProvisioner::class);

        Company::withoutGlobalScopes()
            ->whereNull('deleted_at')
            ->orderBy('id')
            ->each(function (Company $company) use ($setup, $ways): void {
                if ($setup->isSetUp($company)) {
                    $ways->provision($company);
                }
            });
    }

    public function down(): void
    {
        // Mobile Money may already carry payments.
    }
};
