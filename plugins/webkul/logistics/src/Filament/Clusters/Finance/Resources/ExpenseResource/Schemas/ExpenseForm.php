<?php

namespace Webkul\Logistics\Filament\Clusters\Finance\Resources\ExpenseResource\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Logistics\Enums\ExpensePaidBy;
use Webkul\Logistics\Models\ExpenseCategory;
use Webkul\Logistics\Support\LogisticsAccess;
use Webkul\Support\Models\Company;
use Webkul\Support\Services\CompanyContext;

class ExpenseForm
{
    protected static string $lang = 'logistics::filament/clusters/finance/resources/expense';

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__(static::$lang.'.form.sections.details'))
                    ->columns(2)
                    ->schema([
                        Select::make('company_id')
                            ->label(__(static::$lang.'.form.fields.company'))
                            ->options(fn (): array => static::enabledCompanyOptions())
                            ->default(fn (): ?int => app(CompanyContext::class)->currentId())
                            ->required()
                            ->live()
                            ->disabledOn('edit'),
                        DatePicker::make('date')
                            ->label(__(static::$lang.'.form.fields.date'))
                            ->default(now())
                            ->required()
                            ->native(false),
                        TextInput::make('amount')
                            ->label(__(static::$lang.'.form.fields.amount'))
                            ->numeric()
                            ->minValue(0.0001)
                            ->required(),
                        Select::make('currency_id')
                            ->label(__(static::$lang.'.form.fields.currency'))
                            ->relationship('currency', 'name')
                            ->searchable()
                            ->preload(),
                        Select::make('category_id')
                            ->label(__(static::$lang.'.form.fields.category'))
                            ->options(fn (): array => ExpenseCategory::query()->active()->orderBy('sort')->orderBy('name')->pluck('name', 'id')->all())
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required(),
                        Select::make('paid_by')
                            ->label(__(static::$lang.'.form.fields.paid-by'))
                            ->options(ExpensePaidBy::class)
                            ->default(ExpensePaidBy::COMPANY)
                            ->required()
                            ->live()
                            ->native(false),
                        Select::make('employee_id')
                            ->label(__(static::$lang.'.form.fields.employee'))
                            ->relationship('employee', 'name', fn (Builder $query, Get $get) => $query->when($get('company_id'), fn (Builder $q, $companyId) => $q->where('company_id', $companyId)))
                            ->searchable()
                            ->preload()
                            ->visible(fn (Get $get): bool => $get('paid_by') === ExpensePaidBy::EMPLOYEE || $get('paid_by') === ExpensePaidBy::EMPLOYEE->value),
                        Select::make('payee_id')
                            ->label(__(static::$lang.'.form.fields.payee'))
                            ->relationship('payee', 'name', fn (Builder $query) => $query->where('account_type', '!=', 'address'))
                            ->searchable()
                            ->preload(),
                    ]),
                Section::make(__(static::$lang.'.form.sections.reference'))
                    ->columns(2)
                    ->schema([
                        Select::make('shipment_id')
                            ->label(__(static::$lang.'.form.fields.shipment'))
                            ->relationship('shipment', 'name', fn (Builder $query, Get $get) => $query->when($get('company_id'), fn (Builder $q, $companyId) => $q->where('company_id', $companyId)))
                            ->searchable()
                            ->preload()
                            ->required(fn (Get $get): bool => ! $get('trip_id') && ! $get('vehicle_id')),
                        Select::make('trip_id')
                            ->label(__(static::$lang.'.form.fields.trip'))
                            ->relationship('trip', 'name', fn (Builder $query, Get $get) => $query->when($get('company_id'), fn (Builder $q, $companyId) => $q->where('company_id', $companyId)))
                            ->searchable()
                            ->preload()
                            ->required(fn (Get $get): bool => ! $get('shipment_id') && ! $get('vehicle_id')),
                        Select::make('vehicle_id')
                            ->label(__(static::$lang.'.form.fields.vehicle'))
                            ->relationship('vehicle', 'registration_no', fn (Builder $query, Get $get) => $query->when($get('company_id'), fn (Builder $q, $companyId) => $q->where('company_id', $companyId)))
                            ->searchable()
                            ->preload()
                            ->required(fn (Get $get): bool => ! $get('shipment_id') && ! $get('trip_id')),
                        TextInput::make('vendor_reference')
                            ->label(__(static::$lang.'.form.fields.vendor-reference'))
                            ->maxLength(255),
                        Textarea::make('description')
                            ->label(__(static::$lang.'.form.fields.description'))
                            ->rows(3)
                            ->columnSpanFull(),
                        FileUpload::make('receipt_path')
                            ->label(__(static::$lang.'.form.fields.receipt'))
                            ->disk('public')
                            ->directory('logistics/expenses')
                            ->preserveFilenames(false)
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(10240)
                            ->required(fn (Get $get): bool => (bool) ExpenseCategory::query()->whereKey($get('category_id'))->value('requires_receipt'))
                            ->columnSpanFull(),
                    ]),
            ]);
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
}
