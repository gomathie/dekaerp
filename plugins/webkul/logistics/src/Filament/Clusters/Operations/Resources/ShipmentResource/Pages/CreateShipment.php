<?php

namespace Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource;
use Webkul\Logistics\Services\ShipmentTotals;
use Webkul\Logistics\Support\LogisticsAccess;

class CreateShipment extends CreateRecord
{
    protected static string $resource = ShipmentResource::class;

    /**
     * The company comes from the form, so the switch is checked for that company
     * and not only for the session (the policy checks the session).
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        LogisticsAccess::ensureEnabled((int) ($data['company_id'] ?? 0));

        return $data;
    }

    protected function afterCreate(): void
    {
        app(ShipmentTotals::class)->recalculate($this->getRecord());
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
}
