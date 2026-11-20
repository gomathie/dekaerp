<?php

namespace Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Logistics\Enums\LineType;
use Webkul\Logistics\Enums\ShipmentPriority;
use Webkul\Logistics\Enums\StopType;
use Webkul\Logistics\Enums\TransportMode;
use Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource;
use Webkul\Logistics\Models\PackageType;
use Webkul\Logistics\Models\ServiceType;
use Webkul\Partner\Enums\AccountType;
use Webkul\PluginManager\Package;
use Webkul\Product\Models\Product;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Currency;
use Webkul\Support\Services\CompanyContext;

class ShipmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make()->columnSpanFull()->tabs([

                Tab::make(__(ShipmentResource::$lang.'.form.tabs.general'))
                    ->icon('heroicon-o-clipboard-document-list')
                    ->schema([
                        TextInput::make('name')
                            ->label(__(ShipmentResource::$lang.'.form.fields.number'))
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder(__(ShipmentResource::$lang.'.form.fields.number-placeholder')),
                        Select::make('company_id')
                            ->label(__(ShipmentResource::$lang.'.form.fields.company'))
                            ->options(fn (): array => ShipmentResource::enabledCompanyOptions())
                            ->default(fn (): ?int => app(CompanyContext::class)->currentId())
                            ->required()
                            ->live()
                            ->disabledOn('edit'),
                        Select::make('customer_id')
                            ->label(__(ShipmentResource::$lang.'.form.fields.customer'))
                            ->relationship('customer', 'name', fn (Builder $query) => $query->where('account_type', '!=', AccountType::ADDRESS))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live(),
                        TextInput::make('customer_reference')
                            ->label(__(ShipmentResource::$lang.'.form.fields.customer-reference'))
                            ->maxLength(64),
                        Select::make('sale_order_id')
                            ->label(__(ShipmentResource::$lang.'.form.fields.sale-order'))
                            ->helperText(__(ShipmentResource::$lang.'.form.fields.sale-order-helper'))
                            ->options(fn (Get $get): array => ShipmentResource::saleOrderOptions($get('company_id'), $get('customer_id')))
                            ->visible(fn (): bool => Package::isPluginInstalled('sales'))
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(function (?string $state, Set $set): void {
                                ShipmentResource::fillFromSaleOrder($state, $set);
                            }),
                        Select::make('service_type_id')
                            ->label(__(ShipmentResource::$lang.'.form.fields.service-type'))
                            ->options(fn (): array => ServiceType::query()->active()->orderBy('sort')->pluck('name', 'id')->all())
                            ->searchable(),
                        Select::make('transport_mode')
                            ->label(__(ShipmentResource::$lang.'.form.fields.transport-mode'))
                            ->options(TransportMode::class)
                            ->default(TransportMode::ROAD)
                            ->required()
                            ->native(false),
                        Select::make('priority')
                            ->label(__(ShipmentResource::$lang.'.form.fields.priority'))
                            ->options(ShipmentPriority::class)
                            ->default(ShipmentPriority::NORMAL)
                            ->required()
                            ->native(false),
                        Select::make('currency_id')
                            ->label(__(ShipmentResource::$lang.'.form.fields.currency'))
                            // CR-6: only the company's permitted currencies, when it has a list.
                            ->options(fn (): array => Currency::query()->active()->permittedFor(current_company_id())->orderBy('name')->pluck('name', 'id')->all())
                            ->searchable(),
                    ])
                    ->columns(2),

                Tab::make(__(ShipmentResource::$lang.'.form.tabs.route'))
                    ->icon('heroicon-o-map-pin')
                    ->schema([
                        TextInput::make('origin_label')
                            ->label(__(ShipmentResource::$lang.'.form.fields.origin'))
                            ->maxLength(255),
                        TextInput::make('destination_label')
                            ->label(__(ShipmentResource::$lang.'.form.fields.destination'))
                            ->maxLength(255),
                        Select::make('pickup_address_id')
                            ->label(__(ShipmentResource::$lang.'.form.fields.pickup-address'))
                            ->options(fn (Get $get): array => ShipmentResource::addressOptions($get('customer_id')))
                            ->searchable(),
                        Select::make('delivery_address_id')
                            ->label(__(ShipmentResource::$lang.'.form.fields.delivery-address'))
                            ->options(fn (Get $get): array => ShipmentResource::addressOptions($get('customer_id')))
                            ->searchable(),
                        DateTimePicker::make('planned_pickup_at')
                            ->label(__(ShipmentResource::$lang.'.form.fields.planned-pickup-at'))
                            ->seconds(false),
                        DateTimePicker::make('expected_delivery_at')
                            ->label(__(ShipmentResource::$lang.'.form.fields.expected-delivery-at'))
                            ->seconds(false)
                            ->after('planned_pickup_at'),
                        Repeater::make('stops')
                            ->label(__(ShipmentResource::$lang.'.form.fields.stops'))
                            ->relationship()
                            ->columnSpanFull()
                            ->orderColumn('sequence')
                            ->defaultItems(0)
                            ->collapsed()
                            ->itemLabel(fn (array $state): ?string => $state['contact_name'] ?? null)
                            ->schema([
                                Select::make('type')
                                    ->label(__(ShipmentResource::$lang.'.form.fields.stop-type'))
                                    ->options(StopType::class)
                                    ->default(StopType::DELIVERY)
                                    ->required()
                                    ->native(false),
                                Select::make('address_id')
                                    ->label(__(ShipmentResource::$lang.'.form.fields.stop-address'))
                                    ->options(fn (Get $get): array => ShipmentResource::addressOptions($get('../../customer_id')))
                                    ->searchable(),
                                TextInput::make('contact_name')
                                    ->label(__(ShipmentResource::$lang.'.form.fields.stop-contact'))
                                    ->maxLength(255),
                                TextInput::make('contact_phone')
                                    ->label(__(ShipmentResource::$lang.'.form.fields.stop-phone'))
                                    ->tel()
                                    ->maxLength(64),
                                DateTimePicker::make('planned_arrival_at')
                                    ->label(__(ShipmentResource::$lang.'.form.fields.stop-planned-arrival'))
                                    ->seconds(false),
                                Textarea::make('instructions')
                                    ->label(__(ShipmentResource::$lang.'.form.fields.stop-instructions'))
                                    ->rows(2)
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),
                    ])
                    ->columns(2),

                Tab::make(__(ShipmentResource::$lang.'.form.tabs.cargo'))
                    ->icon('heroicon-o-cube')
                    ->schema([
                        TextInput::make('declared_value')
                            ->label(__(ShipmentResource::$lang.'.form.fields.declared-value'))
                            ->numeric()
                            ->minValue(0),
                        Toggle::make('is_fragile')
                            ->label(__(ShipmentResource::$lang.'.form.fields.is-fragile')),
                        Toggle::make('is_hazardous')
                            ->label(__(ShipmentResource::$lang.'.form.fields.is-hazardous'))
                            ->helperText(__(ShipmentResource::$lang.'.form.fields.is-hazardous-helper')),
                        Repeater::make('lines')
                            ->label(__(ShipmentResource::$lang.'.form.fields.cargo-lines'))
                            ->relationship()
                            ->columnSpanFull()
                            ->orderColumn('sort')
                            ->defaultItems(0)
                            ->itemLabel(fn (array $state): ?string => $state['description'] ?? null)
                            ->schema([
                                Select::make('line_type')
                                    ->label(__(ShipmentResource::$lang.'.form.fields.line-type'))
                                    ->options(LineType::class)
                                    ->default(LineType::External)
                                    ->required()
                                    ->native(false)
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, string $state): void {
                                        if ($state === LineType::External->value) {
                                            $set('product_id', null);
                                        }
                                    }),
                                Select::make('product_id')
                                    ->label(__(ShipmentResource::$lang.'.form.fields.line-product'))
                                    ->options(fn (): array => Product::query()->orderBy('name')->pluck('name', 'id')->all())
                                    ->searchable()
                                    ->visible(fn (Get $get): bool => $get('line_type') === LineType::Internal->value)
                                    ->required(fn (Get $get): bool => $get('line_type') === LineType::Internal->value)
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, ?int $state): void {
                                        if ($state === null) {
                                            return;
                                        }
                                        $product = Product::find($state);
                                        if (! $product) {
                                            return;
                                        }
                                        $set('description', $product->name);
                                        if ($product->weight) {
                                            $set('weight_kg', (float) $product->weight);
                                        }
                                    }),
                                TextInput::make('description')
                                    ->label(__(ShipmentResource::$lang.'.form.fields.line-description'))
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan(2),
                                Select::make('package_type_id')
                                    ->label(__(ShipmentResource::$lang.'.form.fields.line-package-type'))
                                    ->options(fn (): array => PackageType::query()->active()->orderBy('sort')->pluck('name', 'id')->all()),
                                TextInput::make('quantity')
                                    ->label(__(ShipmentResource::$lang.'.form.fields.line-quantity'))
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(1)
                                    ->required(),
                                TextInput::make('weight_kg')
                                    ->label(__(ShipmentResource::$lang.'.form.fields.line-weight'))
                                    ->numeric()
                                    ->minValue(0)
                                    ->suffix('kg'),
                                TextInput::make('volume_m3')
                                    ->label(__(ShipmentResource::$lang.'.form.fields.line-volume'))
                                    ->numeric()
                                    ->minValue(0)
                                    ->suffix('m³'),
                            ])
                            ->columns(3),
                        Textarea::make('instructions')
                            ->label(__(ShipmentResource::$lang.'.form.fields.instructions'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(3),

                Tab::make(__(ShipmentResource::$lang.'.form.tabs.assignment'))
                    ->icon('heroicon-o-user-group')
                    ->schema([
                        Select::make('dispatcher_id')
                            ->label(__(ShipmentResource::$lang.'.form.fields.dispatcher'))
                            ->options(fn (): array => User::query()->orderBy('name')->pluck('name', 'id')->all())
                            ->searchable(),
                        Select::make('carrier_id')
                            ->label(__(ShipmentResource::$lang.'.form.fields.carrier'))
                            ->helperText(__(ShipmentResource::$lang.'.form.fields.carrier-helper'))
                            ->relationship('carrier', 'name', fn (Builder $query) => $query->where('account_type', '!=', AccountType::ADDRESS))
                            ->searchable()
                            ->preload(),
                        TextInput::make('carrier_reference')
                            ->label(__(ShipmentResource::$lang.'.form.fields.carrier-reference'))
                            ->maxLength(64),
                        TextInput::make('waybill_no')
                            ->label(__(ShipmentResource::$lang.'.form.fields.waybill-no'))
                            ->maxLength(64)
                            ->disabled()
                            ->dehydrated(false),
                    ])
                    ->columns(2),

                Tab::make(__(ShipmentResource::$lang.'.form.tabs.confidential'))
                    ->icon('heroicon-o-lock-closed')
                    ->schema([
                        Toggle::make('is_confidential')
                            ->label(__(ShipmentResource::$lang.'.form.fields.is-confidential'))
                            ->helperText(__(ShipmentResource::$lang.'.form.fields.is-confidential-helper'))
                            ->live(),
                        Textarea::make('confidential_description')
                            ->label(__(ShipmentResource::$lang.'.form.fields.confidential-description'))
                            ->helperText(__(ShipmentResource::$lang.'.form.fields.confidential-description-helper'))
                            ->rows(3)
                            ->maxLength(1000)
                            ->columnSpanFull()
                            ->visible(fn (Get $get): bool => (bool) $get('is_confidential')),
                        Select::make('consignor_partner_id')
                            ->label(__(ShipmentResource::$lang.'.form.fields.consignor'))
                            ->helperText(__(ShipmentResource::$lang.'.form.fields.consignor-helper'))
                            ->relationship('consignor', 'name', fn (Builder $query) => $query->where('account_type', '!=', AccountType::ADDRESS))
                            ->searchable()
                            ->preload()
                            ->visible(fn (Get $get): bool => (bool) $get('is_confidential')),
                    ])
                    ->columns(2),
            ]),
        ]);
    }
}
