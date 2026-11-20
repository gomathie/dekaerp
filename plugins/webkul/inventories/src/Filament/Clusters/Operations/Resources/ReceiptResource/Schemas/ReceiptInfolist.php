<?php

namespace Webkul\Inventory\Filament\Clusters\Operations\Resources\ReceiptResource\Schemas;

use Filament\Schemas\Schema;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\OperationResource;

class ReceiptInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return OperationResource::infolist($schema);
    }
}
