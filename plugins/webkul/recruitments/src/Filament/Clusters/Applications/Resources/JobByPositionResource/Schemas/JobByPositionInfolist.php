<?php

namespace Webkul\Recruitment\Filament\Clusters\Applications\Resources\JobByPositionResource\Schemas;

use Filament\Schemas\Schema;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\JobPositionResource;

class JobByPositionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return JobPositionResource::infolist($schema);
    }
}
