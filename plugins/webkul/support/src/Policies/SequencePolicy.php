<?php

namespace Webkul\Support\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Security\Models\User;
use Webkul\Security\Services\MultiCompanyAdminService;
use Webkul\Support\Models\Sequence;

/**
 * Document numbering (UF-029, user decision 2026-11-25).
 *
 * Why: the Sequences screen had no policy, and Filament allows every action
 * on a resource without one, so any signed-in user could edit, reset or
 * delete their company's numbering - and the shared counters (company_id
 * null), which number documents for every company that has none of its own.
 *
 * The rule, all in this class ("Company Admin and up"):
 *  - Installation staff (Super Admin, or a Multi-Company Admin within the
 *    companies assigned to them) holding the permission.
 *  - A member of the sequence's company whose role grants the permission in
 *    that company. Company Admin gets it from its baseline
 *    (CompanyAdminRoleProvisioner); any other role only when someone ticks it
 *    on the Roles screen. Membership is the tenant boundary: a global role
 *    answers true for every company id.
 *  - A shared sequence (no company) is created, changed or deleted only by
 *    installation staff. A tenant administrator editing it would renumber
 *    every other client's documents.
 *  - Everyone else is refused. Nothing here falls back to "allowed".
 *
 * There is no Gate::before that returns true for a check carrying a model or
 * class (SecurityServiceProvider grants a Multi-Company Admin bare
 * permission checks only), so this policy is what decides; the screen calls
 * no service that would need to repeat it.
 */
class SequencePolicy
{
    use HandlesAuthorization;

    public function __construct(protected MultiCompanyAdminService $administration) {}

    public function viewAny(User $user): bool
    {
        return $this->mayManage($user, 'view_any_support_sequence', current_company_id());
    }

    /**
     * A shared sequence may be read by whoever may open the list in their
     * current company, since it numbers their documents until they have their
     * own; changing it stays with staff (update/delete).
     */
    public function view(User $user, Sequence $sequence): bool
    {
        return $this->mayManage($user, 'view_support_sequence', $this->companyOf($sequence) ?? current_company_id());
    }

    public function create(User $user): bool
    {
        return $this->mayManage($user, 'create_support_sequence', current_company_id());
    }

    /**
     * Creating a sequence for one company, or a shared one (null). The create
     * action calls this with the submitted company, so a tampered company id
     * is refused on the server and not only hidden in the form.
     */
    public function createIn(User $user, ?int $companyId): bool
    {
        return $this->mayManage($user, 'create_support_sequence', $companyId);
    }

    public function update(User $user, Sequence $sequence): bool
    {
        return $this->mayManage($user, 'update_support_sequence', $this->companyOf($sequence));
    }

    public function delete(User $user, Sequence $sequence): bool
    {
        return $this->mayManage($user, 'delete_support_sequence', $this->companyOf($sequence));
    }

    /**
     * Bulk delete stays with installation staff, like bulk user deletion for
     * a Company Admin; the bulk action also checks delete() on every row, so
     * a selection cannot carry a shared or another company's sequence.
     */
    public function deleteAny(User $user): bool
    {
        return $this->isInstallationStaff($user) && $user->can('delete_any_support_sequence');
    }

    protected function companyOf(Sequence $sequence): ?int
    {
        return $sequence->company_id === null ? null : (int) $sequence->company_id;
    }

    /**
     * A null company is a shared sequence, or no company in the session for
     * the list and create checks: either way, installation staff only.
     */
    protected function mayManage(User $user, string $permission, ?int $companyId): bool
    {
        if (! $user->is_active) {
            return false;
        }

        if ($this->isInstallationStaff($user)) {
            if (! $user->can($permission)) {
                return false;
            }

            return $companyId === null || $this->administration->mayAdministerCompany($user, $companyId);
        }

        if ($companyId === null) {
            return false;
        }

        // Membership first: hasPermissionInCompany() counts a global role in
        // every company, so without it one client's administrator could
        // renumber another client's documents.
        return $this->administration->isCompanyAssigned($user, $companyId)
            && $user->hasPermissionInCompany($permission, $companyId);
    }

    /**
     * Super Admin or Multi-Company Admin: the same people
     * CompanyContext::canSwitchCompanies() names.
     */
    protected function isInstallationStaff(User $user): bool
    {
        return $user->is_active && ($user->isSuperAdmin() || $user->isMultiCompanyAdmin());
    }
}
