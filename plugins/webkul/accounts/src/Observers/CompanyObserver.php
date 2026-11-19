<?php

namespace Webkul\Account\Observers;

use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Webkul\Account\Models\MoveLine;
use Webkul\Account\Services\AccountingSetupService;
use Webkul\Account\Services\BaseCurrencyChange;
use Webkul\PluginManager\Package;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Scopes\CompanyScope;

class CompanyObserver
{
    /**
     * A company without accounting set up could not invoice ("no journal
     * found") or take payments, and nothing pointed to the setup button
     * (user, 2026-11-19). Set it up at creation. setUp() does nothing for a
     * company already set up or when no template company exists, and inside
     * onboarding it runs in the same transaction, so a refused onboarding
     * rolls it back too.
     */
    public function created(Company $company): void
    {
        if (! config('app.accounting_auto_setup', true) || ! Package::isPluginInstalled('accounts')) {
            return;
        }

        app(AccountingSetupService::class)->setUp($company);
    }

    public function updating(Company $company): void
    {
        if (! Package::isPluginInstalled('accounts')) {
            return;
        }

        if (! $company->isDirty('currency_id')) {
            return;
        }

        // D-CR-4: the one audited exception. It holds only while
        // BaseCurrencyChange has checked the Super Admin and the reason.
        if (BaseCurrencyChange::authorizes($company)) {
            return;
        }

        if (! $this->hasAccountingEntries($company)) {
            return;
        }

        throw ValidationException::withMessages([
            'data.currency_id' => __('accounts::observers/company.currency-change'),
        ]);
    }

    protected function hasAccountingEntries(Company $company): bool
    {
        return MoveLine::withoutGlobalScope(CompanyScope::class)
            ->whereIn('company_id', $this->companyTreeIds($company))
            ->exists();
    }

    protected function companyTreeIds(Company $company): array
    {
        $rootId = $company->id;

        $parentId = $company->parent_id;

        while ($parentId) {
            $rootId = $parentId;

            $parentId = Company::withoutGlobalScopes()->whereKey($parentId)->value('parent_id');
        }

        return $this->descendantIds($rootId)->push($rootId)->unique()->all();
    }

    protected function descendantIds(int $companyId): Collection
    {
        return Company::withoutGlobalScopes()
            ->where('parent_id', $companyId)
            ->pluck('id')
            ->reduce(
                fn (Collection $ids, int $branchId) => $ids
                    ->push($branchId)
                    ->merge($this->descendantIds($branchId)),
                collect(),
            );
    }
}
