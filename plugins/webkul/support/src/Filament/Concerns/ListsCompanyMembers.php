<?php

namespace Webkul\Support\Filament\Concerns;

use Webkul\Security\Models\User;

/**
 * Filter options listing people, for the module dashboards.
 *
 * User is not company-scoped, so upstream's User::pluck('name', 'id') listed
 * every user on the installation, other tenants' staff included. This lists
 * only members of the companies the viewer is working in.
 */
trait ListsCompanyMembers
{
    /**
     * @return array<int, string>
     */
    protected static function companyMemberOptions(): array
    {
        return User::query()
            ->whereHas('allowedCompanies', fn ($query) => $query->whereIn('companies.id', allowed_company_ids()))
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }
}
