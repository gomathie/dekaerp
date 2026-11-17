<?php

namespace Webkul\Inventory\Filament\Clusters\Operations\Resources\InternalResource\Schemas;

use Filament\Schemas\Schema;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\OperationResource;

class InternalForm
{
    public static function configure(Schema $schema): Schema
    {
        return OperationResource::form($schema);
    }
}
