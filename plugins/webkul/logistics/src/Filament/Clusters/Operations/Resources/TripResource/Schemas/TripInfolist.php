<?php

namespace Webkul\Logistics\Filament\Clusters\Operations\Resources\TripResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Webkul\Logistics\Models\Trip;
use Webkul\Logistics\Services\DispatchService;

class TripInfolist
{
    protected static string $lang = 'logistics::filament/clusters/operations/resources/trip';

    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__(static::$lang.'.infolist.summary'))
                ->columns(3)
                ->schema([
                    TextEntry::make('name')->label(__(static::$lang.'.form.fields.number')),
                    TextEntry::make('state')->label(__(static::$lang.'.table.columns.state'))->badge(),
                    TextEntry::make('company.name')->label(__(static::$lang.'.form.fields.company')),
                    TextEntry::make('vehicle.registration_no')->label(__(static::$lang.'.form.fields.vehicle'))->placeholder('—'),
                    TextEntry::make('driver.name')->label(__(static::$lang.'.form.fields.driver'))->placeholder('—'),
                    TextEntry::make('dispatcher.name')->label(__(static::$lang.'.form.fields.dispatcher'))->placeholder('—'),
                ]),

            Section::make(__(static::$lang.'.infolist.schedule'))
                ->columns(3)
                ->schema([
                    TextEntry::make('planned_start_at')->label(__(static::$lang.'.form.fields.planned-start-at'))->dateTime()->placeholder('—'),
                    TextEntry::make('actual_start_at')->label(__(static::$lang.'.infolist.actual-start-at'))->dateTime()->placeholder('—'),
                    TextEntry::make('planned_end_at')->label(__(static::$lang.'.form.fields.planned-end-at'))->dateTime()->placeholder('—'),
                    TextEntry::make('actual_end_at')->label(__(static::$lang.'.infolist.actual-end-at'))->dateTime()->placeholder('—'),
                    TextEntry::make('odometer_start')->label(__(static::$lang.'.form.fields.odometer-start'))->numeric()->placeholder('—'),
                    TextEntry::make('odometer_end')->label(__(static::$lang.'.form.fields.odometer-end'))->numeric()->placeholder('—'),
                ]),

            Section::make(__(static::$lang.'.infolist.load'))
                ->columns(2)
                ->schema([
                    TextEntry::make('shipments_count')
                        ->label(__(static::$lang.'.table.columns.shipments'))
                        ->state(fn (Trip $record): int => $record->shipments()->count()),

                    // Shown, not enforced. Over capacity is a warning here and
                    // in DispatchService: it never blocks a dispatch (D5).
                    TextEntry::make('capacity_warnings')
                        ->label(__(static::$lang.'.infolist.capacity'))
                        ->state(fn (Trip $record): string => implode(' ', app(DispatchService::class)->capacityWarnings($record))
                            ?: __(static::$lang.'.infolist.capacity-ok'))
                        ->color(fn (Trip $record): string => app(DispatchService::class)->capacityWarnings($record) === [] ? 'success' : 'warning'),
                ]),
        ]);
    }
}
