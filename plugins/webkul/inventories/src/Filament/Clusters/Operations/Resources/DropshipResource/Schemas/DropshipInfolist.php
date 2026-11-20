<?php

namespace Webkul\Inventory\Filament\Clusters\Operations\Resources\DropshipResource\Schemas;

use Filament\Schemas\Schema;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\OperationResource;

class DropshipInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return OperationResource::infolist($schema);
    }
}
