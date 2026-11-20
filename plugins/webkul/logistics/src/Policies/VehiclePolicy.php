<?php

namespace Webkul\Logistics\Policies;

class VehiclePolicy extends LogisticsPolicy
{
    protected function subject(): string
    {
        return 'vehicle';
    }
}
