<?php

namespace Webkul\Logistics\Filament\Clusters\Fleet\Resources\DriverResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Gate;
use Webkul\Employee\Models\Employee;
use Webkul\Logistics\Filament\Clusters\Fleet\Resources\DriverResource;
use Webkul\Logistics\Models\Driver;
use Webkul\Logistics\Services\FleetImporter;

class ListDrivers extends ListRecords
{
    protected static string $resource = DriverResource::class;

    protected static string $lang = 'logistics::filament/clusters/fleet/resources/driver';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('addDriversFromEmployees')
                ->label(__(static::$lang.'.actions.add-drivers-from-employees.label'))
                ->icon('heroicon-o-user-plus')
                ->visible(fn (): bool => auth()->user()?->can('create', Driver::class) ?? false)
                ->form([
                    Select::make('employee_ids')
                        ->label(__(static::$lang.'.actions.add-drivers-from-employees.fields.employees'))
                        ->options(fn (): array => Employee::query()->orderBy('name')->pluck('name', 'id')->all())
                        ->multiple()
                        ->searchable()
                        ->required(),
                ])
                ->action(function (array $data): void {
                    Gate::authorize('create', Driver::class);

                    $result = app(FleetImporter::class)->driversFromEmployees($data['employee_ids'] ?? []);

                    Notification::make()
                        ->success()
                        ->title(__(static::$lang.'.actions.add-drivers-from-employees.notification.title'))
                        ->body(__(static::$lang.'.actions.add-drivers-from-employees.notification.body', $result))
                        ->send();
                }),
            CreateAction::make()
                ->icon('heroicon-o-plus-circle'),
        ];
    }
}
