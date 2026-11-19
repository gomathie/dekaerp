<?php

use Illuminate\Database\Migrations\Migration;
use Webkul\Account\Services\AccountingSetupService;
use Webkul\Support\Models\Company;

/**
 * Companies created before accounting was set up automatically had no
 * journals: they could not invoice or take payments (user, 2026-11-19).
 * Set each one up once. setUp() skips a company already set up, so this is
 * safe to re-run. Follows the same switch as the company observer, so test
 * databases are left as they are.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! config('app.accounting_auto_setup', true)) {
            return;
        }

        $service = app(AccountingSetupService::class);

        Company::withoutGlobalScopes()
            ->whereNull('deleted_at')
            ->orderBy('id')
            ->each(function (Company $company) use ($service): void {
                $service->setUp($company);
            });
    }

    public function down(): void
    {
        // A company's chart and journals may already carry entries.
    }
};
