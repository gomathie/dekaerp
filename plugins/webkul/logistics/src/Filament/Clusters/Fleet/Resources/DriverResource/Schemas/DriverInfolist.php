<?php

namespace Webkul\Logistics\Filament\Clusters\Fleet\Resources\DriverResource\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Webkul\Logistics\Models\Driver;

class DriverInfolist
{
    protected static string $lang = 'logistics::filament/clusters/fleet/resources/driver';

    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__(static::$lang.'.infolist.sections.general'))
                ->columns(3)
                ->schema([
                    TextEntry::make('name')->label(__(static::$lang.'.form.fields.name')),
                    TextEntry::make('phone')->label(__(static::$lang.'.form.fields.phone'))->placeholder('—'),
                    TextEntry::make('employee.name')->label(__(static::$lang.'.form.fields.employee'))->placeholder('—'),
                    TextEntry::make('partner.name')->label(__(static::$lang.'.form.fields.partner'))->placeholder('—'),
                    TextEntry::make('company.name')->label(__(static::$lang.'.form.fields.company')),
                    IconEntry::make('is_active')->label(__(static::$lang.'.form.fields.is-active'))->boolean(),
                ]),
            Section::make(__(static::$lang.'.infolist.sections.license'))
                ->columns(3)
                ->schema([
                    TextEntry::make('license_number')
                        ->label(__(static::$lang.'.form.fields.license-number'))
                        ->placeholder('—')
                        ->visible(fn (): bool => auth()->user()?->can('update_logistics_driver') ?? false),
                    TextEntry::make('license_class')
                        ->label(__(static::$lang.'.form.fields.license-class'))
                        ->placeholder('—'),
                    TextEntry::make('license_expires_at')
                        ->label(__(static::$lang.'.form.fields.license-expires-at'))
                        ->date()
                        ->badge()
                        ->color(fn (Driver $record): string => static::licenseColor($record))
                        ->placeholder('—'),
                ]),
        ]);
    }

    protected static function licenseColor(Driver $record): string
    {
        if ($record->isLicenseExpired()) {
            return 'danger';
        }

        if ($record->licenseExpiresWithin(30)) {
            return 'warning';
        }

        return 'success';
    }
}
