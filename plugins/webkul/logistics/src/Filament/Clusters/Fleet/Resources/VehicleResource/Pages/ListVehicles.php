<?php

namespace Webkul\Logistics\Filament\Clusters\Fleet\Resources\VehicleResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Gate;
use Webkul\Logistics\Filament\Clusters\Fleet\Resources\VehicleResource;
use Webkul\Logistics\Models\Vehicle;
use Webkul\Logistics\Services\FleetImporter;
use Webkul\Maintenance\Models\Equipment;
use Webkul\PluginManager\Package;

class ListVehicles extends ListRecords
{
    protected static string $resource = VehicleResource::class;

    protected static string $lang = 'logistics::filament/clusters/fleet/resources/vehicle';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('importMaintenanceEquipment')
                ->label(__(static::$lang.'.actions.import-maintenance-equipment.label'))
                ->icon('heroicon-o-arrow-down-tray')
                ->visible(fn (): bool => Package::isPluginInstalled('maintenance') && (auth()->user()?->can('create', Vehicle::class) ?? false))
                ->form([
                    Select::make('equipment_ids')
                        ->label(__(static::$lang.'.actions.import-maintenance-equipment.fields.equipment'))
                        ->options(fn (): array => Equipment::query()->orderBy('name')->pluck('name', 'id')->all())
                        ->multiple()
                        ->searchable()
                        ->required(),
                ])
                ->action(function (array $data): void {
                    Gate::authorize('create', Vehicle::class);

                    $result = app(FleetImporter::class)->vehiclesFromMaintenanceEquipment($data['equipment_ids'] ?? []);

                    Notification::make()
                        ->success()
                        ->title(__(static::$lang.'.actions.import-maintenance-equipment.notification.title'))
                        ->body(__(static::$lang.'.actions.import-maintenance-equipment.notification.body', $result))
                        ->send();
                }),
            CreateAction::make()
                ->icon('heroicon-o-plus-circle'),
        ];
    }
}
