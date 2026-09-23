<?php

namespace Webkul\Logistics\Filament\Clusters\Finance\Resources\ExpenseResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Webkul\Logistics\Filament\Clusters\Finance\Resources\ExpenseResource;
use Webkul\Logistics\Support\LogisticsAccess;

class CreateExpense extends CreateRecord
{
    protected static string $resource = ExpenseResource::class;

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
