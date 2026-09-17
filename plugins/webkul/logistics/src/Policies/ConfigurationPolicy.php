<?php

namespace Webkul\Logistics\Policies;

use Illuminate\Database\Eloquent\Model;
use Webkul\Security\Models\User;
use Webkul\Support\Services\CompanyContext;

/**
 * Configuration (service, vehicle and package types, expense categories) is set
 * up before a company switches Logistics on, so it doesn't depend on the switch.
 *
 * Rows with no company are shared by every company: only users who see all
 * companies may change them. A company's own rows can be changed by users
 * allowed in that company.
 */
abstract class ConfigurationPolicy extends LogisticsPolicy
{
    protected function canList(): bool
    {
        return true;
    }

    protected function canCreate(): bool
    {
        return true;
    }

    protected function writable(Model $record): bool
    {
        $context = app(CompanyContext::class);

        if ($record->company_id === null) {
            return $context->seesAllCompanies();
        }

        return in_array((int) $record->company_id, array_map('intval', $context->allowedIds()), true);
    }

    public function reorder(User $user): bool
    {
        return $this->allows($user, 'reorder');
    }
}
