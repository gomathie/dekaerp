<?php

namespace Webkul\Logistics\Filament\Clusters\Operations\Resources;

use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Webkul\Logistics\Filament\Clusters\Operations;
use Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\Pages\CreateShipment;
use Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\Pages\EditShipment;
use Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\Pages\ListShipments;
use Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\Pages\ManageTimeline;
use Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\Pages\ViewShipment;
use Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\RelationManagers\ChargesRelationManager;
use Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\RelationManagers\DeliveryProofsRelationManager;
use Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\RelationManagers\ExpensesRelationManager;
use Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\RelationManagers\InvoicesRelationManager;
use Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\Schemas\ShipmentForm;
use Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\Tables\ShipmentsTable;
use Webkul\Logistics\Models\Shipment;
use Webkul\Logistics\Support\LogisticsAccess;
use Webkul\Partner\Models\Partner;
use Webkul\PluginManager\Package;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Models\Order;
use Webkul\Support\Services\CompanyContext;

class ShipmentResource extends Resource
{
    protected static ?string $model = Shipment::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-truck';

    protected static ?int $navigationSort = 1;

    protected static ?string $cluster = Operations::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static string $lang = 'logistics::filament/clusters/operations/resources/shipment';

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
        return ShipmentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ShipmentsTable::configure($table);
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
        return [
            DeliveryProofsRelationManager::class,
            ChargesRelationManager::class,
            ExpensesRelationManager::class,
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
    public static function enabledCompanyOptions(): array
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
    public static function addressOptions(mixed $customerId): array
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
    public static function saleOrderOptions(mixed $companyId, mixed $customerId): array
    {
        if (! Package::isPluginInstalled('sales') || ! $customerId) {
            return [];
        }

        return Order::query()
            ->when($companyId, fn (Builder $query) => $query->where('company_id', $companyId))
            ->where('partner_id', $customerId)
            // Confirmed only, as the docblock always claimed. A quotation is not
            // yet work anyone agreed to, and picking one here would pre-fill a
            // shipment from figures the customer has not accepted.
            ->where('state', OrderState::SALE)
            // And not one that already has a shipment. The D1 listener cannot
            // create a duplicate, because it checks sale_order_id, but nothing
            // stopped a person making a second shipment for the same order from
            // this picker.
            ->whereNotIn('id', Shipment::query()->whereNotNull('sale_order_id')->select('sale_order_id'))
            ->orderByDesc('id')
            ->limit(50)
            ->pluck('name', 'id')
            ->all();
    }

    public static function fillFromSaleOrder(?string $orderId, Set $set): void
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
