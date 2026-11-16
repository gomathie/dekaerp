<?php

namespace Webkul\PlatformBilling\Policies;

use Webkul\Security\Models\User;

/**
 * Platform data is not a tenant's to touch (D-SAAS-06).
 *
 * Shield's super-admin gate is off (`define_via_gate` false), and
 * `SecurityServiceProvider`'s `Gate::before` returns true only for a bare
 * permission check. A model or class argument falls through to this policy.
 * Returning false for every ability therefore locked the Super Admin out of
 * the billing screens as well as every tenant. View, create and update are
 * allowed for an active Super Admin only. Delete stays refused: plans and
 * prices are deactivated, and posted invoices, payments, allocations,
 * reversals and extensions are immutable. The services still check the actor.
 * Do not turn the Shield gate back on to "fix" this; that would skip the
 * per-record checks this policy is here to keep.
 */
class DocumentNumberPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return null;
    }

    public function viewAny(User $user): bool
    {
        return $this->superAdmin($user);
    }

    public function view(User $user): bool
    {
        return $this->superAdmin($user);
    }

    public function create(User $user): bool
    {
        return $this->superAdmin($user);
    }

    public function update(User $user): bool
    {
        return $this->superAdmin($user);
    }

    public function delete(User $user): bool
    {
        return false;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }

    public function restore(User $user): bool
    {
        return false;
    }

    public function forceDelete(User $user): bool
    {
        return false;
    }

    /**
     * An inactive Super Admin is not a platform actor. `users.is_active` is
     * what `canAccessPanel()` already requires.
     */
    protected function superAdmin(User $user): bool
    {
        return $user->is_active && $user->isSuperAdmin();
    }
}
