<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources;

use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Webkul\Field\Filament\Traits\HasCustomFields;
use Webkul\Product\Enums\ProductType;
use Webkul\Product\Models\Product;
use Webkul\Purchase\Enums\OrderState;
use Webkul\Purchase\Enums\RequisitionState;
use Webkul\Purchase\Enums\RequisitionType;
use Webkul\Purchase\Filament\Admin\Clusters\Orders;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseAgreementResource\Pages\CreatePurchaseAgreement;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseAgreementResource\Pages\EditPurchaseAgreement;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseAgreementResource\Pages\ListPurchaseAgreements;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseAgreementResource\Pages\ManageRfqs;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseAgreementResource\Pages\ViewPurchaseAgreement;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseAgreementResource\Schemas\PurchaseAgreementForm;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseAgreementResource\Schemas\PurchaseAgreementInfolist;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseAgreementResource\Tables\PurchaseAgreementsTable;
use Webkul\Purchase\Models\Requisition;
use Webkul\Purchase\Settings\OrderSettings;
use Webkul\Purchase\Settings\ProductSettings;
use Webkul\Support\Filament\Forms\Components\Repeater;
use Webkul\Support\Filament\Forms\Components\Repeater\TableColumn;
use Webkul\Support\Models\Company;

class PurchaseAgreementResource extends Resource
{
    use HasCustomFields;

    protected static ?string $model = Requisition::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-check';

    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $cluster = Orders::class;

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'partner.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('purchases::filament/admin/clusters/orders/resources/purchase-agreement.global-search.vendor') => $record->partner?->name ?? '—',
            __('purchases::filament/admin/clusters/orders/resources/purchase-agreement.global-search.type')   => $record->type?->getLabel() ?? '—',
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('purchases::filament/admin/clusters/orders/resources/purchase-agreement.navigation.title');
    }

    public static function getModelLabel(): string
    {
        return __('purchases::filament/admin/clusters/orders/resources/purchase-agreement.navigation.title');
    }

    public static function isDiscovered(): bool
    {
        if (app()->runningInConsole()) {
            return true;
        }

        return static::getOrderSettings()->enable_purchase_agreements;
    }

    public static function form(Schema $schema): Schema
    {
        return PurchaseAgreementForm::configure($schema);
    }

    public static function getProductsRepeater(): Repeater
    {
        $columns = 4;

        if (static::getProductSettings()->enable_uom) {
            $columns++;
        }

        return Repeater::make('lines')
            ->hiddenLabel()
            ->relationship()
            ->compact()
            ->table(fn (Get $get) => [
                TableColumn::make('product_id')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.tabs.products.columns.product'))
                    ->width(300)
                    ->resizable()
                    ->markAsRequired(),
                TableColumn::make('qty')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.tabs.products.columns.quantity'))
                    ->resizable()
                    ->markAsRequired(),
                TableColumn::make('ordered_qty')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.tabs.products.columns.ordered'))
                    ->visible($get('type') === RequisitionType::BLANKET_ORDER)

                    ->width(250),
                TableColumn::make('uom_id')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.tabs.products.columns.uom'))
                    ->resizable()
                    ->visible(static::getProductSettings()->enable_uom)
                    ->markAsRequired(),
                TableColumn::make('price_unit')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.tabs.products.columns.unit-price'))
                    ->resizable()
                    ->markAsRequired(),
            ])
            ->schema([
                Select::make('product_id')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.tabs.products.fields.product'))
                    ->relationship(
                        'product',
                        'name',
                        fn (Builder $query, Get $get) => $query
                            ->where('type', ProductType::GOODS)
                            ->withTrashed()
                            ->whereNull('is_configurable')
                            ->where(owned_by_company($get('../../company_id'))),
                    )
                    ->required()
                    ->searchable()
                    ->preload()
                    ->distinct()
                    ->wrapOptionLabels(false)
                    ->getOptionLabelFromRecordUsing(function ($record): string {
                        return $record->name.($record->trashed() ? ' (Deleted)' : '');
                    })
                    ->disableOptionWhen(function ($value, $state, $component, $label) {
                        if (str_contains($label, ' (Deleted)')) {
                            return true;
                        }

                        $repeater = $component->getParentRepeater();
                        if (! $repeater) {
                            return false;
                        }

                        return collect($repeater->getState())
                            ->pluck(
                                (string) str($component->getStatePath())
                                    ->after("{$repeater->getStatePath()}.")
                                    ->after('.'),
                            )
                            ->flatten()
                            ->diff(Arr::wrap($state))
                            ->filter(fn (mixed $siblingItemState): bool => filled($siblingItemState))
                            ->contains($value);
                    })
                    ->live()
                    ->afterStateUpdated(function (Set $set, Get $get) {
                        if ($product = Product::find($get('product_id'))) {
                            $set('uom_id', $product->uom_id);
                        }
                    })
                    ->disabled(fn ($record): bool => in_array($record?->requisition?->state, [RequisitionState::CLOSED, RequisitionState::CANCELED])),
                TextInput::make('qty')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.tabs.products.fields.quantity'))
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(99999999999)
                    ->default(0)
                    ->required()
                    ->disabled(fn ($record): bool => in_array($record?->requisition?->state, [RequisitionState::CLOSED, RequisitionState::CANCELED])),
                TextInput::make('ordered_qty')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.tabs.products.fields.ordered'))
                    ->numeric()
                    ->default(0)
                    ->dehydrated(false)
                    ->disabled(),
                Select::make('uom_id')
                    ->label(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.fields.unit'))
                    ->relationship(
                        'uom',
                        'name',
                        fn ($query) => $query->where('category_id', 1),
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->wrapOptionLabels(false)
                    ->visible(static::getProductSettings()->enable_uom)
                    ->disabled(fn ($record): bool => in_array($record?->requisition?->state, [RequisitionState::CLOSED, RequisitionState::CANCELED])),
                TextInput::make('price_unit')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.tabs.products.fields.unit-price'))
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(99999999999)
                    ->default(0)
                    ->required()
                    ->disabled(fn ($record): bool => in_array($record?->requisition?->state, [RequisitionState::CLOSED, RequisitionState::CANCELED])),
            ])
            ->columns($columns);
    }

    public static function canBeConfirmed(Requisition $record): bool
    {
        return $record->lines()->exists();
    }

    public static function canBeClosed(Requisition $record): bool
    {
        return ! $record->orders()
            ->whereNotIn('state', [
                OrderState::DONE->value,
                OrderState::CANCELED->value,
            ])
            ->exists();
    }

    public static function table(Table $table): Table
    {
        return PurchaseAgreementsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PurchaseAgreementInfolist::configure($schema);
    }

    public static function getOrderSettings(): OrderSettings
    {
        return settings(OrderSettings::class);
    }

    public static function getProductSettings(): ProductSettings
    {
        return settings(ProductSettings::class);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewPurchaseAgreement::class,
            EditPurchaseAgreement::class,
            ManageRfqs::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListPurchaseAgreements::route('/'),
            'create' => CreatePurchaseAgreement::route('/create'),
            'edit'   => EditPurchaseAgreement::route('/{record}/edit'),
            'view'   => ViewPurchaseAgreement::route('/{record}/view'),
            'rfqs'   => ManageRfqs::route('/{record}/rfqs'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }

    public static function handleCompanyChange(Get $get, Set $set): void
    {
        $companyCurrencyId = Company::whereCurrencyId($get('company_id'))->value('currency_id');

        if (filled($companyCurrencyId)) {
            $set('currency_id', $companyCurrencyId);
        }
    }
}
