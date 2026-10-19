<?php

namespace Webkul\Logistics\Filament\Clusters\Fleet\Resources\VehicleResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Logistics\Filament\Clusters\Fleet\Resources\VehicleResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ViewVehicle extends ViewRecord
{
    use HasRecordNavigationTabs;

    protected static string $resource = VehicleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make(),
        ];
    }
}
