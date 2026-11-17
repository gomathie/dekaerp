<?php

namespace Webkul\Logistics\Filament\Clusters\Configurations\Resources\ServiceTypeResource\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Webkul\Logistics\Enums\TransportMode;
use Webkul\Logistics\Filament\Clusters\Configurations\Resources\ServiceTypeResource;

class ServiceTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                ServiceTypeResource::nameField(),
                ServiceTypeResource::codeField(),
                Select::make('transport_mode')
                    ->label(__(ServiceTypeResource::$lang.'.form.fields.transport-mode'))
                    ->options(TransportMode::class)
                    ->default(TransportMode::ROAD)
                    ->required()
                    ->native(false),
                TextInput::make('product_reference')
                    ->label(__(ServiceTypeResource::$lang.'.form.fields.product-reference'))
                    ->helperText(__(ServiceTypeResource::$lang.'.form.fields.product-reference-helper'))
                    ->maxLength(64),
                ServiceTypeResource::companyField(),
                ServiceTypeResource::activeField(),
            ])
            ->columns(1);
    }
}
