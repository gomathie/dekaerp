<?php

namespace Webkul\Logistics\Filament\Clusters\Operations\Resources\TripResource\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Logistics\Models\Driver;
use Webkul\Logistics\Models\Vehicle;
use Webkul\Logistics\Support\LogisticsAccess;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Services\CompanyContext;

class TripForm
{
    protected static string $lang = 'logistics::filament/clusters/operations/resources/trip';

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__(static::$lang.'.form.sections.crew'))
                    ->columns(2)
                    ->schema([
                        Select::make('company_id')
                            ->label(__(static::$lang.'.form.fields.company'))
                            ->options(fn (): array => static::enabledCompanyOptions())
                            ->default(fn (): ?int => app(CompanyContext::class)->currentId())
                            ->required()
                            ->live()
                            // Locked after creation: the vehicle, driver and
                            // stops are all scoped to it, so changing it later
                            // would strand them in another company.
                            ->disabledOn('edit'),

                        TextInput::make('name')
                            ->label(__(static::$lang.'.form.fields.number'))
                            ->maxLength(255)
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder(__(static::$lang.'.form.fields.number-placeholder')),

                        // Only this company's active fleet. An archived vehicle
                        // or driver cannot be dispatched (DispatchService
                        // refuses), so offering them here would only produce a
                        // failure later.
                        Select::make('vehicle_id')
                            ->label(__(static::$lang.'.form.fields.vehicle'))
                            ->relationship(
                                'vehicle',
                                'registration_no',
                                fn (Builder $query, Get $get) => $query
                                    ->where('is_active', true)
                                    ->when($get('company_id'), fn (Builder $q, $company) => $q->where('company_id', $company)),
                            )
                            ->searchable()
                            ->preload(),

                        Select::make('driver_id')
                            ->label(__(static::$lang.'.form.fields.driver'))
                            ->relationship(
                                'driver',
                                'name',
                                fn (Builder $query, Get $get) => $query
                                    ->where('is_active', true)
                                    ->when($get('company_id'), fn (Builder $q, $company) => $q->where('company_id', $company)),
                            )
                            ->searchable()
                            ->preload(),

                        Select::make('dispatcher_id')
                            ->label(__(static::$lang.'.form.fields.dispatcher'))
                            ->options(fn (): array => User::query()->pluck('name', 'id')->all())
                            ->searchable()
                            ->preload(),
                    ]),

                Section::make(__(static::$lang.'.form.sections.schedule'))
                    ->columns(2)
                    ->schema([
                        DateTimePicker::make('planned_start_at')
                            ->label(__(static::$lang.'.form.fields.planned-start-at'))
                            ->native(false),

                        DateTimePicker::make('planned_end_at')
                            ->label(__(static::$lang.'.form.fields.planned-end-at'))
                            ->native(false)
                            ->afterOrEqual('planned_start_at'),

                        TextInput::make('odometer_start')
                            ->label(__(static::$lang.'.form.fields.odometer-start'))
                            ->numeric()
                            ->minValue(0),

                        TextInput::make('odometer_end')
                            ->label(__(static::$lang.'.form.fields.odometer-end'))
                            ->numeric()
                            ->minValue(0)
                            ->gte('odometer_start'),

                        Textarea::make('notes')
                            ->label(__(static::$lang.'.form.fields.notes'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    /**
     * Only companies the user may act in AND that have Logistics switched on.
     *
     * @return array<int, string>
     */
    protected static function enabledCompanyOptions(): array
    {
        return Company::query()
            ->whereIn('id', LogisticsAccess::enabledCompanyIds())
            ->pluck('name', 'id')
            ->all();
    }
}
