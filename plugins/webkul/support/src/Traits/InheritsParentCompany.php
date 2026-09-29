<?php

namespace Webkul\Support\Traits;

use Illuminate\Database\Eloquent\Model;
use Webkul\Support\Exceptions\CompanyMismatchException;

/**
 * For child rows (order lines, stock quantities, charges, stops...): the company
 * comes from the parent record, never from the user's current company.
 *
 * `BelongsToCompany` fills company_id on "creating" from the session context. The
 * "saving" event fires before "creating", so the parent's company is set first and
 * that trait then leaves it alone. A child whose company differs from its parent's
 * is refused outright rather than filed under the wrong tenant.
 *
 * Using models declare: protected static string $parentCompanyModel and
 * protected static string $parentCompanyKey.
 *
 * Moved here from `Webkul\Logistics\Models\Concerns` on 2026-09-29. It was written
 * for Logistics but states a rule that applies to every plugin, and core packages
 * cannot depend on an optional one - Logistics can be uninstalled.
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

            /*
             * withoutGlobalScopes(), not withoutGlobalScope(CompanyScope::class).
             *
             * The parent may belong to a company that is not active in this session
             * (a system process, a queue worker, a user acting across companies) and
             * its company must still be read. It may equally be hidden by
             * `OwnershipScope`, which several parents carry - accounts `Move`,
             * inventories `Operation`, manufacturing and sales `Order`, `Project`.
             * Dropping only the company scope left those parents invisible to anyone
             * who does not own them, which is the trap `Project\Models\Task` fell
             * into: company_id then came from the actor instead of the parent.
             *
             * Which company a row belongs to is a fact about its parent, not a
             * question about what the actor may look at. Authorization is the
             * policy's job and has already run by the time a saving hook fires.
             */
            $query = $parentModel::withoutGlobalScopes();

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
