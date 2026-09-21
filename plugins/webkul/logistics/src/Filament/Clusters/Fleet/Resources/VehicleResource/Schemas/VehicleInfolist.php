<?php

namespace Webkul\Logistics\Filament\Clusters\Fleet\Resources\VehicleResource\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Webkul\PluginManager\Package;

class VehicleInfolist
{
    protected static string $lang = 'logistics::filament/clusters/fleet/resources/vehicle';

    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__(static::$lang.'.infolist.sections.general'))
                ->columns(3)
                ->schema([
                    TextEntry::make('registration_no')->label(__(static::$lang.'.form.fields.registration-no')),
                    TextEntry::make('name')->label(__(static::$lang.'.form.fields.name'))->placeholder('—'),
                    TextEntry::make('ownership')->label(__(static::$lang.'.form.fields.ownership'))->badge(),
                    TextEntry::make('vehicleType.name')->label(__(static::$lang.'.form.fields.vehicle-type'))->placeholder('—'),
                    TextEntry::make('carrier.name')->label(__(static::$lang.'.form.fields.carrier'))->placeholder('—'),
                    TextEntry::make('defaultDriver.name')->label(__(static::$lang.'.form.fields.default-driver'))->placeholder('—'),
                    TextEntry::make('company.name')->label(__(static::$lang.'.form.fields.company')),
                    IconEntry::make('is_active')->label(__(static::$lang.'.form.fields.is-active'))->boolean(),
                ]),
            Section::make(__(static::$lang.'.infolist.sections.capacity'))
                ->columns(2)
                ->schema([
                    TextEntry::make('capacity_kg')->label(__(static::$lang.'.form.fields.capacity-kg'))->numeric()->suffix(' kg')->placeholder('—'),
                    TextEntry::make('capacity_m3')->label(__(static::$lang.'.form.fields.capacity-m3'))->numeric()->suffix(' m³')->placeholder('—'),
                ]),
            Section::make(__(static::$lang.'.infolist.sections.integrations'))
                ->columns(2)
                ->schema([
                    TextEntry::make('equipment_id')
                        ->label(__(static::$lang.'.form.fields.equipment'))
                        ->placeholder('—')
                        ->visible(fn (): bool => Package::isPluginInstalled('maintenance')),
                    TextEntry::make('telematics_device_ref')
                        ->label(__(static::$lang.'.form.fields.telematics-device-ref'))
                        ->placeholder('—'),
                ]),
        ]);
    }
}
