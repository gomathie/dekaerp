<?php

namespace Webkul\Logistics\Policies;

class ServiceTypePolicy extends ConfigurationPolicy
{
    protected function subject(): string
    {
        return 'service::type';
    }
}
