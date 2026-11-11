<?php

namespace Webkul\Support\Filament;

use Illuminate\Database\Eloquent\Builder;
use Webkul\Security\Models\User;
use Webkul\Support\Services\CompanyContext;

/**
 * The one rule for which people a picker or filter may list.
 *
 * User is not a company-scoped model, so `User::pluck()` and a plain
 * `->relationship('user', 'name')` listed every user on the installation,
 * other tenants' staff included (found again 2026-11-10 on the Project
 * dashboard). This lists members of the companies the viewer is working in
 * now: the selected company, or every company in "All companies" mode.
 */
class CompanyMembers
{
    public static function scope(Builder $query): Builder
    {
        $companyIds = app(CompanyContext::class)->activeIds();

        return $query->whereHas(
            'allowedCompanies',
            fn (Builder $companies): Builder => $companies->whereIn('companies.id', $companyIds),
        );
    }

    /**
     * @return array<int, string>
     */
    public static function options(): array
    {
        return static::scope(User::query())
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }
}
