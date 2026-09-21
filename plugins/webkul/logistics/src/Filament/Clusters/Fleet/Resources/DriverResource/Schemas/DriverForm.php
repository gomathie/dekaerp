<?php

namespace Webkul\Logistics\Filament\Clusters\Fleet\Resources\DriverResource\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Employee\Models\Employee;
use Webkul\Logistics\Support\LogisticsAccess;
use Webkul\Partner\Enums\AccountType;
use Webkul\Partner\Models\Partner;
use Webkul\Support\Models\Company;
use Webkul\Support\Services\CompanyContext;

class DriverForm
{
    protected static string $lang = 'logistics::filament/clusters/fleet/resources/driver';

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('company_id')
                    ->label(__(static::$lang.'.form.fields.company'))
                    ->options(fn (): array => static::enabledCompanyOptions())
                    ->default(fn (): ?int => app(CompanyContext::class)->currentId())
                    ->required()
                    ->live()
                    ->disabledOn('edit'),
                Select::make('employee_id')
                    ->label(__(static::$lang.'.form.fields.employee'))
                    ->options(fn (Get $get): array => static::employeeOptions($get('company_id')))
                    ->searchable()
                    ->live()
                    ->required(fn (Get $get): bool => blank($get('partner_id')))
                    ->afterStateUpdated(function (?int $state, Set $set): void {
                        if (! $state) {
                            return;
                        }

                        $employee = Employee::query()->find($state);

                        $set('partner_id', null);
                        $set('name', $employee?->name);
                        $set('phone', $employee?->work_phone ?? $employee?->mobile_phone);
                    }),
                Select::make('partner_id')
                    ->label(__(static::$lang.'.form.fields.partner'))
                    ->options(fn (Get $get): array => static::partnerOptions($get('company_id')))
                    ->searchable()
                    ->live()
                    ->required(fn (Get $get): bool => blank($get('employee_id')))
                    ->afterStateUpdated(function (?int $state, Set $set): void {
                        if (! $state) {
                            return;
                        }

                        $partner = Partner::query()->find($state);

                        $set('employee_id', null);
                        $set('name', $partner?->name);
                        $set('phone', $partner?->phone ?? $partner?->mobile);
                    }),
                TextInput::make('name')
                    ->label(__(static::$lang.'.form.fields.name'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('phone')
                    ->label(__(static::$lang.'.form.fields.phone'))
                    ->tel()
                    ->maxLength(255),
                TextInput::make('license_number')
                    ->label(__(static::$lang.'.form.fields.license-number'))
                    ->maxLength(255),
                TextInput::make('license_class')
                    ->label(__(static::$lang.'.form.fields.license-class'))
                    ->maxLength(255),
                DatePicker::make('license_expires_at')
                    ->label(__(static::$lang.'.form.fields.license-expires-at'))
                    ->native(false),
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
    protected static function employeeOptions(mixed $companyId): array
    {
        return Employee::query()
            ->when($companyId, fn (Builder $query, $companyId) => $query->where('company_id', $companyId))
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }

    /**
     * @return array<int, string>
     */
    protected static function partnerOptions(mixed $companyId): array
    {
        return Partner::query()
            ->where('account_type', '!=', AccountType::ADDRESS)
            ->when($companyId, fn (Builder $query, $companyId) => $query->where('company_id', $companyId))
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }
}
