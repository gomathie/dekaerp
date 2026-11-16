<?php

namespace Webkul\Support\Traits;

use Webkul\Security\Models\User;
use Webkul\Support\Exceptions\CrossCompanyException;
use Webkul\Support\Exceptions\MissingCompanyContextException;
use Webkul\Support\Models\Scopes\CompanyScope;
use Webkul\Support\Services\CompanyContext;

trait BelongsToCompany
{
    public static function bootBelongsToCompany(): void
    {
        static::addGlobalScope(new CompanyScope);

        // Never into another client's company (upstream #1575 adapted,
        // 2026-11-16): the API took a body company_id unchecked. The rule is
        // CompanyContext::mayWriteTo(); a refusal is a 422 on the API.
        static::creating(function ($model) {
            if (! app(CompanyContext::class)->mayWriteTo($model->company_id)) {
                throw CrossCompanyException::forCompany((int) $model->company_id);
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('company_id') && ! app(CompanyContext::class)->mayWriteTo($model->company_id)) {
                throw CrossCompanyException::forCompany((int) $model->company_id);
            }
        });

        static::creating(function ($model) {
            if (! empty($model->company_id) || ! static::autoAssignsCompany()) {
                return;
            }

            $context = app(CompanyContext::class);

            $model->company_id = $context->currentId();

            /*
             * Fail closed for an acting user. A null here is not "no tenant": the
             * scope reads a null company as *shared*, so the row would appear in
             * every company. Nobody is acting (seeders, installers, console
             * bootstrapping) means the row is created outside a request, and the
             * caller sets the company explicitly where it matters.
             */
            if ($model->company_id === null && $context->internalUser() instanceof User) {
                throw MissingCompanyContextException::forModel(static::class);
            }
        });
    }

    public static function autoAssignsCompany(): bool
    {
        return true;
    }
}
