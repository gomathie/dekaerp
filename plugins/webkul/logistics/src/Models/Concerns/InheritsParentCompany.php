<?php

namespace Webkul\Logistics\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Webkul\Logistics\Exceptions\CompanyMismatchException;
use Webkul\Support\Models\Scopes\CompanyScope;

/**
 * For child rows (cargo lines, stops, charges, events...): the company comes from
 * the parent record, never from the user's current company.
 *
 * BelongsToCompany fills company_id on "creating" from the session context. The
 * "saving" event fires before "creating", so the parent's company is set first
 * and the trait leaves it alone. A child whose company differs from its parent's
 * is refused outright.
 *
 * Using models declare: protected static string $parentCompanyModel and
 * protected static string $parentCompanyKey.
 */
trait InheritsParentCompany
{
    public static function bootInheritsParentCompany(): void
    {
        static::saving(function (Model $model): void {
            $parentId = $model->getAttribute(static::$parentCompanyKey);

            if (! $parentId) {
                return;
            }

            if (! $model->isDirty(static::$parentCompanyKey) && ! $model->isDirty('company_id') && $model->exists) {
                return;
            }

            $parentModel = static::$parentCompanyModel;

            // The parent may belong to a company that isn't active in this session
            // (e.g. a system process); its company must still be read.
            $query = $parentModel::withoutGlobalScope(CompanyScope::class);

            if (method_exists($parentModel, 'bootSoftDeletes')) {
                $query->withTrashed();
            }

            $parentCompanyId = $query->whereKey($parentId)->value('company_id');

            if ($parentCompanyId === null) {
                return;
            }

            if ($model->company_id === null) {
                $model->company_id = $parentCompanyId;

                return;
            }

            if ((int) $model->company_id !== (int) $parentCompanyId) {
                throw CompanyMismatchException::between(class_basename($model), class_basename($parentModel));
            }
        });
    }
}
