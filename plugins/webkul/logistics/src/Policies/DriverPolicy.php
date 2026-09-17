<?php

namespace Webkul\Logistics\Policies;

class DriverPolicy extends LogisticsPolicy
{
    protected function subject(): string
    {
        return 'driver';
    }
}
