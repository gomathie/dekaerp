<?php

namespace Webkul\Support\Database\Factories\Concerns;

use Webkul\Support\Models\Company;
use Webkul\Support\Traits\InheritsParentCompany;

trait HasCompanyDefault
{
    public function configure(): static
    {
        return $this->afterMaking(function ($model): void {
            if (! empty($model->company_id)) {
                return;
            }

            if (method_exists($model, 'autoAssignsCompany') && ! $model::autoAssignsCompany()) {
                return;
            }

            // A child with its parent set takes the parent's company in its own
            // saving hook. Filling the session's company here first made the
            // trait see a mismatch and refuse a correct child (a product variant
            // made while acting in another company).
            if (
                in_array(InheritsParentCompany::class, class_uses_recursive($model), true)
                && filled($model->getAttribute($model::parentCompanyKey()))
            ) {
                return;
            }

            $model->company_id = current_company_id()
                ?? Company::query()->value('id')
                ?? Company::factory()->create()->id;
        });
    }

    public function company(Company|int $company): static
    {
        return $this->state([
            'company_id' => $company instanceof Company ? $company->id : $company,
        ]);
    }
}
