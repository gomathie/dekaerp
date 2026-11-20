<?php

namespace Webkul\Inventory\Filament\Clusters\Operations\Resources\DeliveryResource\Schemas;

use Filament\Schemas\Schema;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\OperationResource;

class DeliveryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return OperationResource::infolist($schema);
    }
}
