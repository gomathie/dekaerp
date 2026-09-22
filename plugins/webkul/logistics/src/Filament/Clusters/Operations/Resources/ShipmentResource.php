<?php

namespace Webkul\Logistics\Filament\Clusters\Operations\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Webkul\Logistics\Enums\ShipmentPriority;
use Webkul\Logistics\Enums\ShipmentState;
use Webkul\Logistics\Enums\StopType;
use Webkul\Logistics\Enums\TransportMode;
use Webkul\Logistics\Filament\Clusters\Operations;
use Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\Pages\CreateShipment;
use Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\Pages\EditShipment;
use Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\Pages\ListShipments;
use Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\Pages\ManageTimeline;
use Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\Pages\ViewShipment;
use Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\RelationManagers\ChargesRelationManager;
use Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\RelationManagers\InvoicesRelationManager;
use Webkul\Logistics\Models\PackageType;
use Webkul\Logistics\Models\ServiceType;
use Webkul\Logistics\Models\Shipment;
use Webkul\Logistics\Support\LogisticsAccess;
use Webkul\Partner\Enums\AccountType;
use Webkul\Partner\Models\Partner;
use Webkul\PluginManager\Package;
use Webkul\Sale\Models\Order;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Currency;
use Webkul\Support\Services\CompanyContext;

class ShipmentResource extends Resource
{
    protected static ?string $model = Shipment::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-truck';

    protected static ?int $navigationSort = 1;

    protected static ?string $cluster = Operations::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string $lang = 'logistics::filament/clusters/operations/resources/shipment';

    public static function getModelLabel(): string
    {
        return __('logistics::models/shipment.title');
    }

    public static function getNavigationLabel(): string
    {
        return __(static::$lang.'.navigation.title');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'customer.name', 'customer_reference', 'waybill_no'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __(static::$lang.'.global-search.customer')  => $record->customer?->name ?? '—',
            __(static::$lang.'.global-search.state')     => $record->state?->getLabel() ?? '—',
            __(static::$lang.'.global-search.reference') => $record->customer_reference ?? '—',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make()->columnSpanFull()->tabs([

                Tab::make(__(static::$lang.'.form.tabs.general'))
                    ->icon('heroicon-o-clipboard-document-list')
                    ->schema([
                        TextInput::make('name')
                            ->label(__(static::$lang.'.form.fields.number'))
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder(__(static::$lang.'.form.fields.number-placeholder')),
                        Select::make('company_id')
                            ->label(__(static::$lang.'.form.fields.company'))
                            ->options(fn (): array => static::enabledCompanyOptions())
                            ->default(fn (): ?int => app(CompanyContext::class)->currentId())
                            ->required()
                            ->live()
                            ->disabledOn('edit'),
                        Select::make('customer_id')
                            ->label(__(static::$lang.'.form.fields.customer'))
                            ->relationship('customer', 'name', fn (Builder $query) => $query->where('account_type', '!=', AccountType::ADDRESS))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live(),
                        TextInput::make('customer_reference')
                            ->label(__(static::$lang.'.form.fields.customer-reference'))
                            ->maxLength(64),
                        Select::make('sale_order_id')
                            ->label(__(static::$lang.'.form.fields.sale-order'))
                            ->helperText(__(static::$lang.'.form.fields.sale-order-helper'))
                            ->options(fn (Get $get): array => static::saleOrderOptions($get('company_id'), $get('customer_id')))
                            ->visible(fn (): bool => Package::isPluginInstalled('sales'))
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(function (?string $state, Set $set): void {
                                static::fillFromSaleOrder($state, $set);
                            }),
                        Select::make('service_type_id')
                            ->label(__(static::$lang.'.form.fields.service-type'))
                            ->options(fn (): array => ServiceType::query()->active()->orderBy('sort')->pluck('name', 'id')->all())
                            ->searchable(),
                        Select::make('transport_mode')
                            ->label(__(static::$lang.'.form.fields.transport-mode'))
                            ->options(TransportMode::class)
                            ->default(TransportMode::ROAD)
                            ->required()
                            ->native(false),
                        Select::make('priority')
                            ->label(__(static::$lang.'.form.fields.priority'))
                            ->options(ShipmentPriority::class)
                            ->default(ShipmentPriority::NORMAL)
                            ->required()
                            ->native(false),
                        Select::make('currency_id')
                            ->label(__(static::$lang.'.form.fields.currency'))
                            ->options(fn (): array => Currency::query()->orderBy('name')->pluck('name', 'id')->all())
                            ->searchable(),
                    ])
                    ->columns(2),

                Tab::make(__(static::$lang.'.form.tabs.route'))
                    ->icon('heroicon-o-map-pin')
                    ->schema([
                        TextInput::make('origin_label')
                            ->label(__(static::$lang.'.form.fields.origin'))
                            ->maxLength(255),
                        TextInput::make('destination_label')
                            ->label(__(static::$lang.'.form.fields.destination'))
                            ->maxLength(255),
                        Select::make('pickup_address_id')
                            ->label(__(static::$lang.'.form.fields.pickup-address'))
                            ->options(fn (Get $get): array => static::addressOptions($get('customer_id')))
                            ->searchable(),
                        Select::make('delivery_address_id')
                            ->label(__(static::$lang.'.form.fields.delivery-address'))
                            ->options(fn (Get $get): array => static::addressOptions($get('customer_id')))
                            ->searchable(),
                        DateTimePicker::make('planned_pickup_at')
                            ->label(__(static::$lang.'.form.fields.planned-pickup-at'))
                            ->seconds(false),
                        DateTimePicker::make('expected_delivery_at')
                            ->label(__(static::$lang.'.form.fields.expected-delivery-at'))
                            ->seconds(false)
                            ->after('planned_pickup_at'),
                        Repeater::make('stops')
                            ->label(__(static::$lang.'.form.fields.stops'))
                            ->relationship()
                            ->columnSpanFull()
                            ->orderColumn('sequence')
                            ->defaultItems(0)
                            ->collapsed()
                            ->itemLabel(fn (array $state): ?string => $state['contact_name'] ?? null)
                            ->schema([
                                Select::make('type')
                                    ->label(__(static::$lang.'.form.fields.stop-type'))
                                    ->options(StopType::class)
                                    ->default(StopType::DELIVERY)
                                    ->required()
                                    ->native(false),
                                Select::make('address_id')
                                    ->label(__(static::$lang.'.form.fields.stop-address'))
                                    ->options(fn (Get $get): array => static::addressOptions($get('../../customer_id')))
                                    ->searchable(),
                                TextInput::make('contact_name')
                                    ->label(__(static::$lang.'.form.fields.stop-contact'))
                                    ->maxLength(255),
                                TextInput::make('contact_phone')
                                    ->label(__(static::$lang.'.form.fields.stop-phone'))
                                    ->tel()
                                    ->maxLength(64),
                                DateTimePicker::make('planned_arrival_at')
                                    ->label(__(static::$lang.'.form.fields.stop-planned-arrival'))
                                    ->seconds(false),
                                Textarea::make('instructions')
                                    ->label(__(static::$lang.'.form.fields.stop-instructions'))
                                    ->rows(2)
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),
                    ])
                    ->columns(2),

                Tab::make(__(static::$lang.'.form.tabs.cargo'))
                    ->icon('heroicon-o-cube')
                    ->schema([
                        TextInput::make('declared_value')
                            ->label(__(static::$lang.'.form.fields.declared-value'))
                            ->numeric()
                            ->minValue(0),
                        Toggle::make('is_fragile')
                            ->label(__(static::$lang.'.form.fields.is-fragile')),
                        Toggle::make('is_hazardous')
                            ->label(__(static::$lang.'.form.fields.is-hazardous'))
                            ->helperText(__(static::$lang.'.form.fields.is-hazardous-helper')),
                        Repeater::make('lines')
                            ->label(__(static::$lang.'.form.fields.cargo-lines'))
                            ->relationship()
                            ->columnSpanFull()
                            ->orderColumn('sort')
                            ->defaultItems(0)
                            ->itemLabel(fn (array $state): ?string => $state['description'] ?? null)
                            ->schema([
                                TextInput::make('description')
                                    ->label(__(static::$lang.'.form.fields.line-description'))
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan(2),
                                Select::make('package_type_id')
                                    ->label(__(static::$lang.'.form.fields.line-package-type'))
                                    ->options(fn (): array => PackageType::query()->active()->orderBy('sort')->pluck('name', 'id')->all()),
                                TextInput::make('quantity')
                                    ->label(__(static::$lang.'.form.fields.line-quantity'))
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(1)
                                    ->required(),
                                TextInput::make('weight_kg')
                                    ->label(__(static::$lang.'.form.fields.line-weight'))
                                    ->numeric()
                                    ->minValue(0)
                                    ->suffix('kg'),
                                TextInput::make('volume_m3')
                                    ->label(__(static::$lang.'.form.fields.line-volume'))
                                    ->numeric()
                                    ->minValue(0)
                                    ->suffix('m³'),
                            ])
                            ->columns(3),
                        Textarea::make('instructions')
                            ->label(__(static::$lang.'.form.fields.instructions'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(3),

                Tab::make(__(static::$lang.'.form.tabs.assignment'))
                    ->icon('heroicon-o-user-group')
                    ->schema([
                        Select::make('dispatcher_id')
                            ->label(__(static::$lang.'.form.fields.dispatcher'))
                            ->options(fn (): array => User::query()->orderBy('name')->pluck('name', 'id')->all())
                            ->searchable(),
                        Select::make('carrier_id')
                            ->label(__(static::$lang.'.form.fields.carrier'))
                            ->helperText(__(static::$lang.'.form.fields.carrier-helper'))
                            ->relationship('carrier', 'name', fn (Builder $query) => $query->where('account_type', '!=', AccountType::ADDRESS))
                            ->searchable()
                            ->preload(),
                        TextInput::make('carrier_reference')
                            ->label(__(static::$lang.'.form.fields.carrier-reference'))
                            ->maxLength(64),
                        TextInput::make('waybill_no')
                            ->label(__(static::$lang.'.form.fields.waybill-no'))
                            ->maxLength(64)
                            ->disabled()
                            ->dehydrated(false),
                    ])
                    ->columns(2),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__(static::$lang.'.table.columns.number'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('customer.name')
                    ->label(__(static::$lang.'.table.columns.customer'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('state')
                    ->label(__(static::$lang.'.table.columns.state'))
                    ->badge()
                    ->sortable(),
                TextColumn::make('planned_pickup_at')
                    ->label(__(static::$lang.'.table.columns.planned-pickup-at'))
                    ->dateTime()
                    ->placeholder('—')
                    ->visibleFrom('sm')
                    ->sortable(),
                TextColumn::make('expected_delivery_at')
                    ->label(__(static::$lang.'.table.columns.expected-delivery-at'))
                    ->dateTime()
                    ->placeholder('—')
                    ->visibleFrom('md')
                    ->sortable(),
                TextColumn::make('serviceType.name')
                    ->label(__(static::$lang.'.table.columns.service-type'))
                    ->placeholder('—')
                    ->visibleFrom('lg'),
                TextColumn::make('total_packages')
                    ->label(__(static::$lang.'.table.columns.packages'))
                    ->numeric()
                    ->visibleFrom('lg'),
                TextColumn::make('dispatcher.name')
                    ->label(__(static::$lang.'.table.columns.dispatcher'))
                    ->placeholder('—')
                    ->visibleFrom('lg'),
                TextColumn::make('company.name')
                    ->label(__(static::$lang.'.table.columns.company'))
                    ->visibleFrom('lg'),
            ])
            ->filters([
                SelectFilter::make('state')
                    ->label(__(static::$lang.'.table.filters.state'))
                    ->options(ShipmentState::class)
                    ->multiple(),
                SelectFilter::make('customer')
                    ->label(__(static::$lang.'.table.filters.customer'))
                    ->relationship('customer', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('service_type')
                    ->label(__(static::$lang.'.table.filters.service-type'))
                    ->relationship('serviceType', 'name'),
                Filter::make('planned_pickup_at')
                    ->schema([
                        DateTimePicker::make('from')->label(__(static::$lang.'.table.filters.pickup-from'))->seconds(false),
                        DateTimePicker::make('until')->label(__(static::$lang.'.table.filters.pickup-until'))->seconds(false),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['from'] ?? null, fn (Builder $q, $date) => $q->where('planned_pickup_at', '>=', $date))
                        ->when($data['until'] ?? null, fn (Builder $q, $date) => $q->where('planned_pickup_at', '<=', $date))),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('id', 'desc');
    }

    /**
     * Eager loads everything the table columns read, so the list stays at a
     * constant number of queries however many rows it shows.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class])
            ->with(['customer:id,name', 'serviceType:id,name', 'dispatcher:id,name', 'company:id,name']);
    }

    public static function getRelations(): array
    {
        // WP-5's DeliveryProofsRelationManager joins this list once that
        // package is finished and files its request.
        return [
            ChargesRelationManager::class,
            InvoicesRelationManager::class,
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewShipment::class,
            EditShipment::class,
            ManageTimeline::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'    => ListShipments::route('/'),
            'create'   => CreateShipment::route('/create'),
            'view'     => ViewShipment::route('/{record}'),
            'edit'     => EditShipment::route('/{record}/edit'),
            'timeline' => ManageTimeline::route('/{record}/timeline'),
        ];
    }

    /**
     * @return array<int, string>
     */
    protected static function enabledCompanyOptions(): array
    {
        return app(CompanyContext::class)->allowedCompanies()
            ->filter(fn ($company): bool => LogisticsAccess::enabledFor($company->id))
            ->pluck('name', 'id')
            ->all();
    }

    /**
     * Addresses of the chosen customer (its address contacts, plus itself).
     *
     * @return array<int, string>
     */
    protected static function addressOptions(mixed $customerId): array
    {
        if (! $customerId) {
            return [];
        }

        return Partner::query()
            ->where(fn (Builder $query) => $query->whereKey($customerId)->orWhere('parent_id', $customerId))
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }

    /**
     * Confirmed sales orders of this customer and company, for the "From sales
     * order" picker. Only used when the Sales plugin is installed.
     *
     * @return array<int, string>
     */
    protected static function saleOrderOptions(mixed $companyId, mixed $customerId): array
    {
        if (! Package::isPluginInstalled('sales') || ! $customerId) {
            return [];
        }

        return Order::query()
            ->when($companyId, fn (Builder $query) => $query->where('company_id', $companyId))
            ->where('partner_id', $customerId)
            ->orderByDesc('id')
            ->limit(50)
            ->pluck('name', 'id')
            ->all();
    }

    protected static function fillFromSaleOrder(?string $orderId, Set $set): void
    {
        if (! $orderId || ! Package::isPluginInstalled('sales')) {
            return;
        }

        $order = Order::query()->find($orderId);

        if (! $order) {
            return;
        }

        $set('currency_id', $order->currency_id);
        $set('customer_reference', $order->client_order_ref);
    }
}
