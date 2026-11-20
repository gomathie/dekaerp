<?php

namespace Webkul\Inventory\Filament\Clusters\Operations\Resources\InternalResource\Schemas;

use Filament\Schemas\Schema;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\OperationResource;

class InternalInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return OperationResource::infolist($schema);
    }
}
