<?php

namespace Webkul\PlatformBilling\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Services\CompanyContext;
use Webkul\Support\Traits\BelongsToCompany;

class CompanyReadOnlyWriteGuard
{
    public const MESSAGE = 'This company is read-only because its subscription needs attention. Contact your administrator.';

    /**
     * Refuse tenant Eloquent writes at the model lifecycle boundary so the same
     * rule applies to UI actions, APIs, and queued jobs that save tenant models.
     */
    public function assertMayWrite(Model $model): void
    {
        if (! in_array(BelongsToCompany::class, class_uses_recursive($model), true)) {
            return;
        }

        $actor = app(CompanyContext::class)->internalUser();
        if ($actor instanceof User && $actor->is_active && ($actor->isSuperAdmin() || $actor->isMultiCompanyAdmin())) {
            return;
        }

        $companyIds = [];
        if ($model->exists && $model->getOriginal('company_id') !== null) {
            $companyIds[] = (int) $model->getOriginal('company_id');
        }
        if ($model->getAttribute('company_id') !== null) {
            $companyIds[] = (int) $model->getAttribute('company_id');
        } elseif (! $model->exists && $model::autoAssignsCompany()) {
            $currentCompanyId = app(CompanyContext::class)->currentId();
            if ($currentCompanyId !== null) {
                $companyIds[] = $currentCompanyId;
            }
        }

        foreach (array_unique($companyIds) as $companyId) {
            $company = Company::withoutGlobalScopes()->find($companyId);
            if ($company && app(SubscriptionLifecycle::class)->isReadOnly($company)) {
                // A user-facing field error survives both Livewire and JSON API
                // exception rendering and names the recovery cause.
                throw ValidationException::withMessages(['company_id' => self::MESSAGE]);
            }
        }
    }
}
