<?php

namespace Webkul\Security\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Security\Models\User;
use Webkul\Security\Services\MultiCompanyAdminService;
use Webkul\Security\Traits\HasScopedPermissions;

class UserPolicy
{
    use HandlesAuthorization, HasScopedPermissions;

    public function __construct(protected MultiCompanyAdminService $administration) {}

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_security_user');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $record): bool
    {
        if (! $user->can('view_security_user')) {
            return false;
        }

        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isMultiCompanyAdmin()) {
            return $this->administration->canManageUser($user, $record);
        }

        if ($this->isProtectedAdministrator($record)) {
            return false;
        }

        return $this->sharesCompanies($user, $record) && $this->hasAccess($user, $record, 'creator');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_security_user');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $record): bool
    {
        if (! $user->can('update_security_user')) {
            return false;
        }

        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isMultiCompanyAdmin()) {
            return $this->administration->canManageUser($user, $record);
        }

        if ($this->isProtectedAdministrator($record)) {
            return false;
        }

        return $this->sharesCompanies($user, $record) && $this->hasAccess($user, $record, 'creator');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $record): bool
    {
        if (! $user->can('delete_security_user')) {
            return false;
        }

        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isMultiCompanyAdmin()) {
            return $this->administration->canManageUser($user, $record);
        }

        if ($this->isProtectedAdministrator($record)) {
            return false;
        }

        return $this->sharesCompanies($user, $record) && $this->hasAccess($user, $record, 'creator');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_security_user');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, User $record): bool
    {
        if ($user->isMultiCompanyAdmin()) {
            return false;
        }

        if (! $user->can('force_delete_security_user')) {
            return false;
        }

        if ($user->id === $record->id) {
            return false;
        }

        if (! $user->isSuperAdmin() && $this->isProtectedAdministrator($record)) {
            return false;
        }

        return $this->sharesCompanies($user, $record) && $this->hasAccess($user, $record, 'creator');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        if ($user->isMultiCompanyAdmin()) {
            return false;
        }

        return $user->can('force_delete_any_security_user');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, User $record): bool
    {
        if (! $user->can('restore_security_user')) {
            return false;
        }

        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isMultiCompanyAdmin()) {
            return $this->administration->canManageUser($user, $record);
        }

        if ($this->isProtectedAdministrator($record)) {
            return false;
        }

        return $this->sharesCompanies($user, $record) && $this->hasAccess($user, $record, 'creator');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_security_user');
    }

    /**
     * The company boundary, applied to every per-record decision here.
     *
     * `hasAccess()` answers "does your resource permission reach this record",
     * which for a `global` permission is always yes - it knows nothing about
     * companies. That is what let a customer's administrator open and edit
     * another tenant's users. This is the same rule the Users list is built
     * from (`MultiCompanyAdminService::scopeManageableUsers`), so a record that
     * is invisible in the list cannot be reached by its URL either.
     *
     * See docs/company-admin-role-plan.md.
     */
    protected function sharesCompanies(User $user, User $record): bool
    {
        return $this->administration->sharesAssignedCompanies($user, $record);
    }

    protected function isProtectedAdministrator(User $record): bool
    {
        return $record->isSuperAdmin() || $record->isMultiCompanyAdmin();
    }
}
