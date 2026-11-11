<?php

namespace Webkul\Support\Filament\Concerns;

use Webkul\Support\Filament\CompanyMembers;

/**
 * Filter options listing people, for the module dashboards.
 *
 * User is not company-scoped, so upstream's User::pluck('name', 'id') listed
 * every user on the installation, other tenants' staff included. The rule
 * lives in CompanyMembers; this keeps the dashboards' existing call.
 */
trait ListsCompanyMembers
{
    /**
     * @return array<int, string>
     */
    protected static function companyMemberOptions(): array
    {
        return CompanyMembers::options();
    }
}
