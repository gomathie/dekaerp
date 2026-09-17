<?php

namespace Webkul\Logistics\Policies;

class PackageTypePolicy extends ConfigurationPolicy
{
    protected function subject(): string
    {
        return 'package::type';
    }
}
