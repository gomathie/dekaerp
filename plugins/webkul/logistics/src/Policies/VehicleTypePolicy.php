<?php

namespace Webkul\Logistics\Policies;

class VehicleTypePolicy extends ConfigurationPolicy
{
    protected function subject(): string
    {
        return 'vehicle::type';
    }
}
