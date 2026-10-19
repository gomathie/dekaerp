<?php

namespace Webkul\Logistics\Filament\Clusters\Configurations\Resources\VehicleTypeResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Webkul\Logistics\Filament\Clusters\Configurations\Resources\VehicleTypeResource;

class ManageVehicleTypes extends ManageRecords
{
    protected static string $resource = VehicleTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->icon('heroicon-o-plus-circle'),
        ];
    }
}
