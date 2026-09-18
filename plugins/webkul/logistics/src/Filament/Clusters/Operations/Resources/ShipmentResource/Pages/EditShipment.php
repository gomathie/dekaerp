<?php

namespace Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Webkul\Chatter\Filament\Actions\ChatterAction;
use Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource;
use Webkul\Logistics\Services\ShipmentTotals;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class EditShipment extends EditRecord
{
    use HasRecordNavigationTabs;

    protected static string $resource = ShipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ChatterAction::make()
                ->resource(static::$resource)
                ->activityPlans($this->getRecord()->activityPlans()),
            DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        app(ShipmentTotals::class)->recalculate($this->getRecord());
    }
}
