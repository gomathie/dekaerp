<?php

namespace Webkul\Logistics\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Model;
use Webkul\Logistics\Support\LogisticsAccess;
use Webkul\Security\Models\User;

/**
 * Base policy for operational Logistics records.
 *
 * Permissions follow Shield's naming in this fork (PermissionManager):
 * "<affix>_logistics_<subject>". On top of the permission, listing and creating
 * need Logistics enabled for one of the user's active companies, and changing a
 * record needs it enabled for the record's company (the per-company switch).
 *
 * Note: the super-admin Gate::before bypass skips policies entirely, so services
 * repeat the switch check with LogisticsAccess::ensureEnabled().
 */
abstract class LogisticsPolicy
{
    use HandlesAuthorization;

    /**
     * The permission subject as Shield builds it, e.g. "shipment" or "service::type".
     */
    abstract protected function subject(): string;

    protected function allows(User $user, string $affix): bool
    {
        return $user->can($affix.'_logistics_'.$this->subject());
    }

    protected function canList(): bool
    {
        return LogisticsAccess::enabledForCurrent();
    }

    protected function canCreate(): bool
    {
        return LogisticsAccess::enabledForCurrent();
    }

    protected function writable(Model $record): bool
    {
        return LogisticsAccess::enabledFor($record->company_id);
    }

    public function viewAny(User $user): bool
    {
        return $this->allows($user, 'view_any') && $this->canList();
    }

    public function view(User $user, Model $record): bool
    {
        return $this->allows($user, 'view');
    }

    public function create(User $user): bool
    {
        return $this->allows($user, 'create') && $this->canCreate();
    }

    public function update(User $user, Model $record): bool
    {
        return $this->allows($user, 'update') && $this->writable($record);
    }

    public function delete(User $user, Model $record): bool
    {
        return $this->allows($user, 'delete') && $this->writable($record);
    }

    public function deleteAny(User $user): bool
    {
        return $this->allows($user, 'delete_any') && $this->canCreate();
    }

    public function restore(User $user, Model $record): bool
    {
        return $this->allows($user, 'restore') && $this->writable($record);
    }

    public function restoreAny(User $user): bool
    {
        return $this->allows($user, 'restore_any') && $this->canCreate();
    }

    public function forceDelete(User $user, Model $record): bool
    {
        return $this->allows($user, 'force_delete') && $this->writable($record);
    }

    public function forceDeleteAny(User $user): bool
    {
        return $this->allows($user, 'force_delete_any') && $this->canCreate();
    }

    /**
     * A custom per-record ability (e.g. "confirm", "mark_delivered").
     */
    protected function recordAbility(User $user, string $affix, Model $record): bool
    {
        return $this->allows($user, $affix) && $this->writable($record);
    }
}
