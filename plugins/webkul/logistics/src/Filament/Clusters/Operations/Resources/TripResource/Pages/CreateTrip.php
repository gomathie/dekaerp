<?php

namespace Webkul\Logistics\Filament\Clusters\Operations\Resources\TripResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Webkul\Logistics\Filament\Clusters\Operations\Resources\TripResource;
use Webkul\Logistics\Support\LogisticsAccess;

class CreateTrip extends CreateRecord
{
    protected static string $resource = TripResource::class;

    /**
     * The switch is re-checked here, not just in the form: the company select
     * only lists enabled companies, but a crafted request could still post a
     * company that has Logistics switched off.
     */
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
