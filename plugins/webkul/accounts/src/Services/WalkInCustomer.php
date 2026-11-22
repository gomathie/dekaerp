<?php

namespace Webkul\Account\Services;

use Webkul\Partner\Enums\AccountType;
use Webkul\Partner\Models\Partner;

/**
 * The customer a direct sale starts with (user, 2026-11-22): a cash sale to
 * someone who gives no name still needs a real contact, so sales reports keep
 * a customer to group by.
 *
 * One per company, found by its reference rather than its name: the name is
 * translated, so a name lookup would create a second one whenever someone
 * works in another language.
 */
class WalkInCustomer
{
    public const REFERENCE = 'WALK-IN';

    public function forCompany(int $companyId): Partner
    {
        // Past the global scopes, or a user whose ownership scope hides the
        // contact would create a duplicate; deleted ones are left deleted.
        $existing = Partner::query()
            ->withoutGlobalScopes()
            ->whereNull('deleted_at')
            ->where('company_id', $companyId)
            ->where('reference', self::REFERENCE)
            ->first();

        if ($existing instanceof Partner) {
            return $existing;
        }

        return Partner::query()->create([
            'name'         => __('accounts::filament/resources/invoice/pages/create-invoice.direct-sale.walk-in-customer'),
            'reference'    => self::REFERENCE,
            'account_type' => AccountType::INDIVIDUAL,
            'company_id'   => $companyId,
        ]);
    }
}
