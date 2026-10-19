<?php

namespace Webkul\Logistics\Policies;

use Illuminate\Database\Eloquent\Model;
use Webkul\Logistics\Support\LogisticsAccess;
use Webkul\Security\Models\User;

/**
 * Records that live inside a shipment (cargo lines, stops, charges, events,
 * proofs, stop links) are governed by the shipment's permissions: seeing them
 * needs "view shipment", changing them needs "update shipment".
 */
abstract class ShipmentChildPolicy extends LogisticsPolicy
{
    protected function subject(): string
    {
        return 'shipment';
    }

    public function view(User $user, Model $record): bool
    {
        return $this->allows($user, 'view');
    }

    public function create(User $user): bool
    {
        return $this->allows($user, 'update') && LogisticsAccess::enabledForCurrent();
    }

    public function update(User $user, Model $record): bool
    {
        return $this->allows($user, 'update') && $this->writable($record);
    }

    public function delete(User $user, Model $record): bool
    {
        return $this->allows($user, 'update') && $this->writable($record);
    }

    public function deleteAny(User $user): bool
    {
        return $this->allows($user, 'update') && LogisticsAccess::enabledForCurrent();
    }
}
