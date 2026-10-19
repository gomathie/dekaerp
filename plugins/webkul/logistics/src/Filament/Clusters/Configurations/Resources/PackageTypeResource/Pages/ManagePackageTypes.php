<?php

namespace Webkul\Logistics\Filament\Clusters\Configurations\Resources\PackageTypeResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Webkul\Logistics\Filament\Clusters\Configurations\Resources\PackageTypeResource;

class ManagePackageTypes extends ManageRecords
{
    protected static string $resource = PackageTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->icon('heroicon-o-plus-circle'),
        ];
    }
}
