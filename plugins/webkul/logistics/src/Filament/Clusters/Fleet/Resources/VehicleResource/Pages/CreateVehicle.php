<?php

namespace Webkul\Logistics\Filament\Clusters\Fleet\Resources\VehicleResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Webkul\Logistics\Filament\Clusters\Fleet\Resources\VehicleResource;
use Webkul\Logistics\Support\LogisticsAccess;

class CreateVehicle extends CreateRecord
{
    protected static string $resource = VehicleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        LogisticsAccess::ensureEnabled((int) ($data['company_id'] ?? 0));

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
}
