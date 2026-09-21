<?php

namespace Webkul\Logistics\Filament\Clusters\Fleet\Resources\DriverResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Logistics\Filament\Clusters\Fleet\Resources\DriverResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ViewDriver extends ViewRecord
{
    use HasRecordNavigationTabs;

    protected static string $resource = DriverResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make(),
        ];
    }
}
