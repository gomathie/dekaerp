<?php

namespace Webkul\Logistics\Filament\Clusters\Configurations\Resources\ServiceTypeResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Webkul\Logistics\Filament\Clusters\Configurations\Resources\ServiceTypeResource;

class ManageServiceTypes extends ManageRecords
{
    protected static string $resource = ServiceTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->icon('heroicon-o-plus-circle'),
        ];
    }
}
