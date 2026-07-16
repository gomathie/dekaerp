<?php

namespace Webkul\Support\Models\Scopes;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;
use Webkul\Support\Services\CurrentCompany;

class CompanyScope implements Scope
{
    /**
     * Only queries made by an authenticated user inside the admin panel are
     * scoped, so console commands, queues, seeders, APIs, and the customer
     * panel keep their existing behavior. Records without a company
     * (company_id = null) are treated as shared and always visible.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (app()->runningInConsole()) {
            return;
        }

        if (! Auth::check()) {
            return;
        }

        if (Filament::getCurrentPanel()?->getId() !== 'admin') {
            return;
        }

        $allowedIds = CurrentCompany::allowedIds();

        if (empty($allowedIds)) {
            return;
        }

        $column = $model->qualifyColumn('company_id');

        if (CurrentCompany::isAll()) {
            $builder->where(function (Builder $query) use ($column, $allowedIds) {
                $query->whereIn($column, $allowedIds)->orWhereNull($column);
            });

            return;
        }

        $companyId = CurrentCompany::id();

        if (! $companyId) {
            return;
        }

        $builder->where(function (Builder $query) use ($column, $companyId) {
            $query->where($column, $companyId)->orWhereNull($column);
        });
    }

    /**
     * Attach the scope to a model class and auto-fill company_id with the
     * active company when new records are created without one.
     */
    public static function applyTo(string $modelClass): void
    {
        $modelClass::addGlobalScope(new static);

        $modelClass::creating(function (Model $model) {
            if (! Auth::check()) {
                return;
            }

            if (! empty($model->company_id)) {
                return;
            }

            $model->company_id = CurrentCompany::id();
        });
    }
}
