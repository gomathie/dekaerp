<?php

namespace Webkul\Logistics\Filament\Clusters\Configurations\Resources\VehicleTypeResource\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Webkul\Logistics\Filament\Clusters\Configurations\Resources\VehicleTypeResource;

class VehicleTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                VehicleTypeResource::nameField(),
                VehicleTypeResource::codeField(),
                TextInput::make('capacity_kg')
                    ->label(__(VehicleTypeResource::$lang.'.form.fields.capacity-kg'))
                    ->numeric()
                    ->minValue(0)
                    ->suffix('kg'),
                TextInput::make('capacity_m3')
                    ->label(__(VehicleTypeResource::$lang.'.form.fields.capacity-m3'))
                    ->numeric()
                    ->minValue(0)
                    ->suffix('m³'),
                VehicleTypeResource::companyField(),
                VehicleTypeResource::activeField(),
            ])
            ->columns(1);
    }
}
