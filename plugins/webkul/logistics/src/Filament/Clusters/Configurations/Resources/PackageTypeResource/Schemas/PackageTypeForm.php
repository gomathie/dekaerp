<?php

namespace Webkul\Logistics\Filament\Clusters\Configurations\Resources\PackageTypeResource\Schemas;

use Filament\Schemas\Schema;
use Webkul\Logistics\Filament\Clusters\Configurations\Resources\PackageTypeResource;

class PackageTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                PackageTypeResource::nameField(),
                PackageTypeResource::codeField(),
                PackageTypeResource::companyField(),
                PackageTypeResource::activeField(),
            ])
            ->columns(1);
    }
}
