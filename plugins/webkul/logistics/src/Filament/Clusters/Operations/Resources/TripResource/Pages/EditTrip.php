<?php

namespace Webkul\Logistics\Filament\Clusters\Operations\Resources\TripResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Webkul\Logistics\Filament\Clusters\Operations\Resources\TripResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class EditTrip extends EditRecord
{
    use HasRecordNavigationTabs;

    protected static string $resource = TripResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
