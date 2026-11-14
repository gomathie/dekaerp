<?php

namespace Webkul\Security\Services;

use Webkul\Security\Models\Invitation;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

/**
 * Where a company stands in its setup (comprehensive review CR-7, 2026-11-13).
 *
 * Derived, never stored: a stored status would drift from the invitation and
 * user rows it summarises, and the acceptance rule is "no company shows Active
 * while its administrator cannot log in". So Active means an administrator
 * account that exists and is enabled - read through
 * CompanyAdministratorAccount::administrators(), the one rule for who
 * administers a company. Billing's read-only state stays on the subscription
 * screens; security does not reach into platform-billing.
 */
class CompanyProvisioningStatus
{
    public const ACTIVE = 'active';

    /** No usable administrator yet, but an invitation is still open. */
    public const INVITED = 'invited';

    /** No usable administrator, and every invitation lapsed without acceptance. */
    public const EXPIRED = 'expired';

    public const NO_ADMINISTRATOR = 'no-administrator';

    /** Archived or switched off: nobody should be signing in. */
    public const CLOSED = 'closed';

    public const ALL = [self::ACTIVE, self::INVITED, self::EXPIRED, self::NO_ADMINISTRATOR, self::CLOSED];

    public function __construct(protected CompanyAdministratorAccount $accounts) {}

    /** @return self::ACTIVE|self::INVITED|self::EXPIRED|self::NO_ADMINISTRATOR|self::CLOSED */
    public function status(Company $company): string
    {
        if ($company->trashed() || ! $company->is_active) {
            return self::CLOSED;
        }

        $hasUsableAdministrator = $this->accounts->administrators($company)
            ->contains(fn (User $user): bool => (bool) $user->is_active && $user->deleted_at === null);

        if ($hasUsableAdministrator) {
            return self::ACTIVE;
        }

        $invitations = Invitation::query()->where('default_company_id', $company->getKey());

        if ((clone $invitations)->pending()->exists()) {
            return self::INVITED;
        }

        return (clone $invitations)->whereNull('accepted_at')->whereNull('revoked_at')->exists()
            ? self::EXPIRED
            : self::NO_ADMINISTRATOR;
    }

    /** The newest invitation that can still be sent again, if any. */
    public function resendableInvitation(Company $company): ?Invitation
    {
        return Invitation::query()
            ->where('default_company_id', $company->getKey())
            ->pending()
            ->latest('id')
            ->first();
    }

    /**
     * Ids of the given companies whose status is one of $statuses. Used by the
     * list filter, so the filter and the column cannot disagree.
     *
     * @param  iterable<Company>  $companies
     * @param  list<string>  $statuses
     * @return list<int>
     */
    public function idsWithStatus(iterable $companies, array $statuses): array
    {
        $ids = [];

        foreach ($companies as $company) {
            if (in_array($this->status($company), $statuses, true)) {
                $ids[] = (int) $company->getKey();
            }
        }

        return $ids;
    }
}
