<?php

namespace Webkul\PlatformBilling\Services;

use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
use Webkul\PlatformBilling\Models\Invoice;
use Webkul\PlatformBilling\Models\InvoiceLine;
use Webkul\PlatformBilling\Models\OverdueExtension;
use Webkul\PlatformBilling\Models\Subscription;
use Webkul\Security\Models\User;
use Webkul\Security\Services\MultiCompanyAdminService;
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

        // A Company Admin of this company may record the one overdue
        // extension after the lock (user, 2026-11-10). The model stays out
        // of EXEMPT_MODELS: a no-user job, another tenant, and every other
        // model stay refused. An existing row is not covered, so the grant
        // cannot be edited through this opening.
        if ($actor instanceof User && $this->companyAdminMayGrantExtension($actor, $model)) {
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
            $this->assertCompanyMayWrite($companyId);
        }
    }

    /**
     * The per-company half of the rule, for writes that are not company
     * models: company settings rows are written by query, so no model event
     * reaches the guard and a locked company could still save every settings
     * page (2026-11-10). Support fires `support.company-settings.writing`,
     * which calls this.
     */
    public function assertCompanyMayWrite(int $companyId): void
    {
        $actor = app(CompanyContext::class)->internalUser();

        if ($actor instanceof User && $actor->is_active && $actor->isSuperAdmin()) {
            return;
        }

        // A Multi-Company Admin passes only in companies assigned to them
        // (user, 2026-11-10). Exempting them everywhere let staff write into
        // a locked company they do not administer, while every other rule
        // limits them to their assignment.
        if ($actor instanceof User && $actor->is_active && $actor->isMultiCompanyAdmin()
            && app(MultiCompanyAdminService::class)->isCompanyAssigned($actor, $companyId)) {
            return;
        }

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

    /**
     * Only a new extension, and only for a company this person administers.
     * A Company Admin of another company stays refused once that company is read-only.
     */
    protected function companyAdminMayGrantExtension(User $actor, Model $model): bool
    {
        if (! $model instanceof OverdueExtension || $model->exists || ! $actor->is_active) {
            return false;
        }

        $companyId = $model->getAttribute('company_id');

        return $companyId !== null && $actor->isCompanyAdmin((int) $companyId);
    }
}
