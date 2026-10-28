<?php

namespace Webkul\PlatformBilling\Policies;

use Webkul\Security\Models\User;

/**
 * Platform data is not a tenant's to touch (D-SAAS-06).
 *
 * Every ability is denied explicitly. That is not decoration: the tenant panels
 * auto-discover policies, and a missing or permissive one would let a Company
 * Admin whose role happens to carry a similarly named permission reach the
 * installation's numbering or, later, its invoices.
 *
 * Super Admin is not handled here on purpose - `SecurityServiceProvider`'s
 * `Gate::before` grants them bare ability checks and skips policies, so platform
 * staff keep working while every tenant actor is refused. The `before()` hook
 * below keeps Multi-Company Admin staff out too: that Gate only answers when it
 * receives no model argument, and returning `null` here makes this policy the
 * decider for a record.
 */
class DocumentNumberPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return null;
    }

    public function viewAny(User $user): bool
    {
        return false;
    }

    public function view(User $user): bool
    {
        return false;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user): bool
    {
        return false;
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
}
