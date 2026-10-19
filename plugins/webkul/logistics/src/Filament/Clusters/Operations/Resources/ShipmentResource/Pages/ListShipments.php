<?php

namespace Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource;

class ListShipments extends ListRecords
{
    protected static string $resource = ShipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->icon('heroicon-o-plus-circle'),
        ];
    }
}
