<?php

namespace Webkul\PlatformBilling\Services;

use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
use Webkul\PlatformBilling\Models\Invoice;
use Webkul\PlatformBilling\Models\InvoiceLine;
use Webkul\PlatformBilling\Models\Subscription;
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
    /**
     * Platform billing's own records (user decision, 2026-11-09). Refusing
     * them stopped a read-only company from ever receiving its next invoice,
     * and a cancelled subscription from expiring at period end, because the
     * invoice job runs with no user. Only these writes pass; every tenant
     * write stays refused. Pinned by SubscriptionReadOnlyEnforcementTest.
     *
     * @var list<class-string<Model>>
     */
    public const EXEMPT_MODELS = [
        Subscription::class,
        Invoice::class,
        InvoiceLine::class,
    ];

    public function assertMayWrite(Model $model): void
    {
        if (! in_array(BelongsToCompany::class, class_uses_recursive($model), true)) {
            return;
        }

        if (in_array($model::class, self::EXEMPT_MODELS, true)) {
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
                // In a Filament form the field is prefixed (for example
                // mountedActions.0.data.company_id), so the error matches no
                // field and the user saw nothing happen. Say it as a
                // notification too; outside a browser session it is an
                // unused session flash. (Review of 54d606a4b, 2026-11-09)
                Notification::make()->danger()->title(self::MESSAGE)->send();

                throw ValidationException::withMessages(['company_id' => self::MESSAGE]);
            }
        }
    }
}
