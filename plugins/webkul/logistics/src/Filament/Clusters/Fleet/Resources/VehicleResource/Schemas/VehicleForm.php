<?php

namespace Webkul\Logistics\Filament\Clusters\Fleet\Resources\VehicleResource\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;
use Webkul\Logistics\Enums\VehicleOwnership;
use Webkul\Logistics\Models\Driver;
use Webkul\Logistics\Models\Vehicle;
use Webkul\Logistics\Models\VehicleType;
use Webkul\Logistics\Support\LogisticsAccess;
use Webkul\Maintenance\Models\Equipment;
use Webkul\Partner\Enums\AccountType;
use Webkul\PluginManager\Package;
use Webkul\Support\Models\Company;
use Webkul\Support\Services\CompanyContext;

class VehicleForm
{
    protected static string $lang = 'logistics::filament/clusters/fleet/resources/vehicle';

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('registration_no')
                    ->label(__(static::$lang.'.form.fields.registration-no'))
                    ->required()
                    ->maxLength(255)
                    ->rules([
                        fn (Get $get, ?Vehicle $record): Unique => Rule::unique('logistics_vehicles', 'registration_no')
                            ->where('company_id', $get('company_id'))
                            ->ignore($record?->getKey()),
                    ]),
                TextInput::make('name')
                    ->label(__(static::$lang.'.form.fields.name'))
                    ->maxLength(255),
                Select::make('company_id')
                    ->label(__(static::$lang.'.form.fields.company'))
                    ->options(fn (): array => static::enabledCompanyOptions())
                    ->default(fn (): ?int => app(CompanyContext::class)->currentId())
                    ->required()
                    ->live()
                    ->disabledOn('edit'),
                Select::make('vehicle_type_id')
                    ->label(__(static::$lang.'.form.fields.vehicle-type'))
                    ->options(fn (): array => VehicleType::query()->active()->orderBy('sort')->pluck('name', 'id')->all())
                    ->searchable()
                    ->preload(),
                Select::make('ownership')
                    ->label(__(static::$lang.'.form.fields.ownership'))
                    ->options(VehicleOwnership::class)
                    ->default(VehicleOwnership::OWNED)
                    ->required()
                    ->live()
                    ->native(false),
                Select::make('carrier_id')
                    ->label(__(static::$lang.'.form.fields.carrier'))
                    ->relationship('carrier', 'name', fn (Builder $query) => $query->where('account_type', '!=', AccountType::ADDRESS))
                    ->searchable()
                    ->preload()
                    ->visible(fn (Get $get): bool => $get('ownership') === VehicleOwnership::THIRD_PARTY || $get('ownership') === VehicleOwnership::THIRD_PARTY->value),
                Select::make('default_driver_id')
                    ->label(__(static::$lang.'.form.fields.default-driver'))
                    ->options(fn (Get $get): array => Driver::query()
                        ->when($get('company_id'), fn (Builder $query, $companyId) => $query->where('company_id', $companyId))
                        ->active()
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->all())
                    ->searchable(),
                Select::make('equipment_id')
                    ->label(__(static::$lang.'.form.fields.equipment'))
                    ->options(fn (Get $get): array => static::equipmentOptions($get('company_id')))
                    ->visible(fn (): bool => Package::isPluginInstalled('maintenance'))
                    ->searchable(),
                TextInput::make('telematics_device_ref')
                    ->label(__(static::$lang.'.form.fields.telematics-device-ref'))
                    ->maxLength(255),
                TextInput::make('capacity_kg')
                    ->label(__(static::$lang.'.form.fields.capacity-kg'))
                    ->numeric()
                    ->minValue(0)
                    ->suffix('kg'),
                TextInput::make('capacity_m3')
                    ->label(__(static::$lang.'.form.fields.capacity-m3'))
                    ->numeric()
                    ->minValue(0)
                    ->suffix('m³'),
                Toggle::make('is_active')
                    ->label(__(static::$lang.'.form.fields.is-active'))
                    ->default(true),
            ])
            ->columns(2);
    }

    /**
     * @return array<int, string>
     */
    protected static function enabledCompanyOptions(): array
    {
        return Company::query()
            ->whereIn('id', LogisticsAccess::enabledCompanyIds())
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }

    /**
     * @return array<int, string>
     */
    protected static function equipmentOptions(mixed $companyId): array
    {
        if (! Package::isPluginInstalled('maintenance')) {
            return [];
        }

        return Equipment::query()
            ->when($companyId, fn (Builder $query, $companyId) => $query->where('company_id', $companyId))
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }
}
