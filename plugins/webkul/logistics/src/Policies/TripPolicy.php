<?php

namespace Webkul\Logistics\Policies;

use Webkul\Logistics\Models\Trip;
use Webkul\Security\Models\User;

class TripPolicy extends LogisticsPolicy
{
    protected function subject(): string
    {
        return 'trip';
    }

    public function dispatch(User $user, Trip $trip): bool
    {
        return $this->recordAbility($user, 'dispatch', $trip);
    }

    public function complete(User $user, Trip $trip): bool
    {
        return $this->recordAbility($user, 'complete', $trip);
    }
}
