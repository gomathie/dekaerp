<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources;

use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Webkul\Account\Enums\TypeTaxUse;
use Webkul\Account\Facades\Tax as TaxFacade;
use Webkul\Account\Models\Partner;
use Webkul\Account\Models\Tax;
use Webkul\Field\Filament\Traits\HasCustomFields;
use Webkul\Inventory\Enums as InventoryEnums;
use Webkul\Inventory\Models\OperationType;
use Webkul\PluginManager\Package;
use Webkul\Product\Enums\ProductType;
use Webkul\Product\Models\Packaging;
use Webkul\Purchase\Enums\OrderState;
use Webkul\Purchase\Enums\QtyReceivedMethod;
use Webkul\Purchase\Enums\RequisitionState;
use Webkul\Purchase\Enums\RequisitionType;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\OrderResource\Schemas\OrderForm;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\OrderResource\Schemas\OrderInfolist;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\OrderResource\Tables\OrdersTable;
use Webkul\Purchase\Filament\Admin\Clusters\Products\Resources\ProductResource;
use Webkul\Purchase\Models\Order;
use Webkul\Purchase\Models\OrderLine;
use Webkul\Purchase\Models\Product;
use Webkul\Purchase\Models\Requisition;
use Webkul\Purchase\Settings\OrderSettings;
use Webkul\Purchase\Settings\ProductSettings;
use Webkul\Support\Filament\Forms\Components\Repeater;
use Webkul\Support\Filament\Forms\Components\Repeater\TableColumn;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;
use Webkul\Support\Models\UOM;

class OrderResource extends Resource
{
    use HasCustomFields;

    protected static ?string $model = Order::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static bool $isGloballySearchable = false;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'partner.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('purchases::filament/admin/clusters/orders/resources/order.global-search.vendor') => $record->partner?->name ?? '—',
            __('purchases::filament/admin/clusters/orders/resources/order.global-search.amount') => money($record->total_amount ?? 0, $record->currency?->name) ?? '—',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return OrderForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OrdersTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return OrderInfolist::configure($schema);
    }

    public static function getProductRepeater(): Repeater
    {
        return Repeater::make('products')
            ->relationship('lines')
            ->hiddenLabel()
            ->live()
            ->compact()
            ->reactive()
            ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.title'))
            ->addActionLabel(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.add-product-line'))
            ->collapsible()
            ->defaultItems(0)
            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
            ->deleteAction(function (Action $action) {
                $action->requiresConfirmation();

                $action->before(function (Action $action, $livewire) {
                    $arguments = $action->getArguments();

                    if (
                        ! empty($arguments['item'] ?? '') &&
                        ! str_starts_with($arguments['item'] ?? '', 'record-')
                    ) {
                        return;
                    }

                    if ($livewire->getRecord()?->state === OrderState::PURCHASE) {
                        Notification::make()
                            ->danger()
                            ->title(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.delete-action.error.title'))
                            ->body(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.delete-action.error.body'))
                            ->send();

                        $action->cancel();

                        return;
                    }
                });

                $action->after(function (Get $get, $livewire) {
                    $totals = self::calculateOrderTotals($get, $livewire);

                    $livewire->dispatch('itemUpdated', $totals);
                });
            })
            ->deletable(fn ($record): bool => ! in_array($record?->state, [OrderState::DONE, OrderState::CANCELED]))
            ->addable(fn ($record): bool => ! in_array($record?->state, [OrderState::DONE, OrderState::CANCELED]))
            ->table(fn ($record) => [
                TableColumn::make('product_id')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.columns.product'))
                    ->width(300)
                    ->resizable()
                    ->markAsRequired(),

                TableColumn::make('planned_at')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.columns.expected-arrival'))
                    ->markAsRequired()
                    ->resizable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TableColumn::make('product_qty')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.columns.quantity'))
                    ->resizable()
                    ->markAsRequired(),

                TableColumn::make('qty_received')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.columns.received'))
                    ->markAsRequired()
                    ->resizable()
                    ->visible(fn (): bool => Package::isPluginInstalled('inventories') && in_array($record?->state, [OrderState::PURCHASE, OrderState::DONE]))
                    ->toggleable(),

                TableColumn::make('qty_received_manual')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.columns.received'))
                    ->markAsRequired()
                    ->resizable()
                    ->visible(fn (): bool => ! Package::isPluginInstalled('inventories') && in_array($record?->state, [OrderState::PURCHASE, OrderState::DONE]))
                    ->toggleable(),

                TableColumn::make('qty_invoiced')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.columns.billed'))
                    ->resizable()
                    ->visible(fn (): bool => in_array($record?->state, [OrderState::PURCHASE, OrderState::DONE]))
                    ->toggleable(),

                TableColumn::make('uom_id')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.columns.unit'))
                    ->resizable()
                    ->markAsRequired()
                    ->visible(static::getProductSettings()->enable_uom)
                    ->toggleable(),

                TableColumn::make('product_packaging_qty')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.columns.packaging-qty'))
                    ->resizable()
                    ->visible(static::getProductSettings()->enable_packagings)
                    ->toggleable(),

                TableColumn::make('product_packaging_id')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.columns.packaging'))
                    ->resizable()
                    ->visible(static::getProductSettings()->enable_packagings)
                    ->toggleable(),

                TableColumn::make('price_unit')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.columns.unit-price'))
                    ->resizable()
                    ->markAsRequired(),

                TableColumn::make('taxes')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.columns.taxes'))
                    ->resizable()
                    ->wrapHeader(false)
                    ->toggleable(),

                TableColumn::make('discount')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.columns.discount-percentage'))
                    ->resizable()
                    ->wrapHeader(false)
                    ->toggleable(isToggledHiddenByDefault: true),

                TableColumn::make('price_subtotal')
                    ->resizable()
                    ->wrapHeader(false)
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.columns.amount')),
            ])
            ->schema(fn ($record) => [
                Select::make('product_id')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.fields.product'))
                    ->relationship(
                        'product',
                        'name',
                        fn (Builder $query, Get $get) => $query
                            ->withTrashed()
                            ->where('type', ProductType::GOODS)
                            ->whereNull('is_configurable')
                            ->where(owned_by_company($get('../../company_id'))),
                    )
                    ->getOptionLabelFromRecordUsing(function ($record): string {
                        return $record->name.($record->trashed() ? ' (Deleted)' : '');
                    })
                    ->searchable()
                    ->preload()
                    ->live()
                    ->required()
                    ->wrapOptionLabels(false)
                    ->disabled(fn (Get $get): bool => filled($get('id')) && in_array($record?->state, [OrderState::PURCHASE, OrderState::DONE, OrderState::CANCELED]))
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
                    ->afterStateUpdated(function (Set $set, Get $get) {
                        static::afterProductUpdated($set, $get);
                    }),
                DateTimePicker::make('planned_at')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.fields.expected-arrival'))
                    ->native(false)
                    ->suffixIcon('heroicon-o-calendar')
                    ->required()
                    ->default(now())
                    ->default(function (Get $get, Set $set) {
                        if (empty($get('../../planned_at'))) {
                            $set('../../planned_at', now());
                        }

                        return now();
                    })
                    ->afterStateUpdated(function (?string $state, Set $set) {
                        $set('../../planned_at', $state);
                    })
                    ->disabled(fn (): bool => in_array($record?->state, [OrderState::DONE, OrderState::CANCELED])),

                TextInput::make('product_qty')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.fields.quantity'))
                    ->required()
                    ->default(1)
                    ->numeric()
                    ->maxValue(99999999999)
                    ->live(onBlur: true)
                    ->rule(function (Get $get): \Closure {
                        return function (string $attribute, $value, \Closure $fail) use ($get): void {
                            $qtyReceived = (float) ($get('qty_received') ?? 0);

                            if ($qtyReceived > 0 && (float) $value < $qtyReceived) {
                                $fail(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.notifications.quantity-below-received.body', ['qty' => $qtyReceived]));
                            }
                        };
                    })
                    ->afterStateUpdated(function (Set $set, Get $get) {
                        static::afterProductQtyUpdated($set, $get);
                    })
                    ->disabled(fn (): bool => in_array($record?->state, [OrderState::DONE, OrderState::CANCELED])),

                TextInput::make('qty_received')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.fields.received'))
                    ->required()
                    ->default(0)
                    ->numeric()
                    ->maxValue(99999999999)
                    ->visible(fn (): bool => Package::isPluginInstalled('inventories') && in_array($record?->state, [OrderState::PURCHASE, OrderState::DONE]))
                    ->disabled(fn ($record): bool => in_array($record?->order->state, [OrderState::DONE, OrderState::CANCELED]) || $record?->qty_received_method == QtyReceivedMethod::STOCK_MOVE),
                TextInput::make('qty_received_manual')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.fields.received'))
                    ->required()
                    ->default(0)
                    ->numeric()
                    ->maxValue(99999999999)
                    ->visible(fn ($record): bool => ! Package::isPluginInstalled('inventories') && in_array($record?->order->state, [OrderState::PURCHASE, OrderState::DONE]))
                    ->disabled(fn ($record): bool => in_array($record?->order->state, [OrderState::DONE, OrderState::CANCELED]) || $record?->qty_received_method == QtyReceivedMethod::STOCK_MOVE),

                TextInput::make('qty_invoiced')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.fields.billed'))
                    ->default(0)
                    ->numeric()
                    ->maxValue(99999999999)
                    ->visible(fn (): bool => in_array($record?->state, [OrderState::PURCHASE, OrderState::DONE]))
                    ->disabled(),

                Select::make('uom_id')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.fields.unit'))
                    ->relationship(
                        'uom',
                        'name',
                        function (Builder $query, Get $get) {
                            $product = Product::withTrashed()->find($get('product_id'));
                            $categoryId = $product?->uom?->category_id;

                            return $query->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))->orderBy('id');
                        },
                    )
                    ->required()
                    ->live()
                    ->native(false)
                    ->wrapOptionLabels(false)
                    ->selectablePlaceholder(false)
                    ->afterStateUpdated(function (Set $set, Get $get) {
                        static::afterUOMUpdated($set, $get);
                    })
                    ->visible(static::getProductSettings()->enable_uom)
                    ->disabled(fn (Get $get): bool => filled($get('id')) && in_array($record?->state, [OrderState::PURCHASE, OrderState::DONE, OrderState::CANCELED])),

                TextInput::make('product_packaging_qty')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.fields.packaging-qty'))
                    ->live(onBlur: true)
                    ->numeric()
                    ->maxValue(99999999999)
                    ->afterStateUpdated(function (Set $set, Get $get) {
                        static::afterProductPackagingQtyUpdated($set, $get);
                    })
                    ->visible(static::getProductSettings()->enable_packagings)
                    ->disabled(fn (): bool => in_array($record?->state, [OrderState::DONE, OrderState::CANCELED])),

                Select::make('product_packaging_id')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.fields.packaging'))
                    ->relationship(
                        'productPackaging',
                        'name',
                        modifyQueryUsing: fn (Builder $query, Get $get) => $query->where('product_id', $get('product_id')),
                    )
                    ->searchable()
                    ->preload()
                    ->live()
                    ->wrapOptionLabels(false)
                    ->afterStateUpdated(function (Set $set, Get $get) {
                        static::afterProductPackagingUpdated($set, $get);
                    })
                    ->visible(static::getProductSettings()->enable_packagings)
                    ->disabled(fn (): bool => in_array($record?->state, [OrderState::DONE, OrderState::CANCELED])),

                TextInput::make('price_unit')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.fields.unit-price'))
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->maxValue(99999999999)
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Set $set, Get $get) {
                        self::calculateLineTotals($set, $get);
                    })
                    ->disabled(fn (): bool => in_array($record?->state, [OrderState::DONE, OrderState::CANCELED])),

                Select::make('taxes')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.fields.taxes'))
                    ->relationship(
                        'taxes',
                        'name',
                        modifyQueryUsing: fn (Builder $query, Get $get) => $query
                            ->where('type_tax_use', TypeTaxUse::PURCHASE)
                            ->where(owned_by_company($get('../../company_id'))),
                    )
                    ->searchable()
                    ->multiple()
                    ->preload()
                    ->wrapOptionLabels(false)
                    ->afterStateUpdated(function (Get $get, Set $set, $state) {
                        self::calculateLineTotals($set, $get);
                    })
                    ->live()
                    ->disabled(fn (): bool => in_array($record?->state, [OrderState::DONE, OrderState::CANCELED])),

                TextInput::make('discount')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.fields.discount-percentage'))
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->maxValue(100)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Set $set, Get $get) {
                        self::calculateLineTotals($set, $get);
                    })
                    ->disabled(fn (): bool => in_array($record?->state, [OrderState::DONE, OrderState::CANCELED])),

                TextInput::make('price_subtotal')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.fields.amount'))
                    ->default(0)
                    ->readOnly()
                    ->disabled(fn (): bool => in_array($record?->state, [OrderState::DONE, OrderState::CANCELED])),

                Hidden::make('product_uom_qty')
                    ->default(0),

                Hidden::make('price_tax')
                    ->default(0),

                Hidden::make('price_total')
                    ->default(0),

                Hidden::make('from_requisition')
                    ->default(false),
            ])
            ->mutateRelationshipDataBeforeCreateUsing(function (array $data, $record) {
                $product = Product::find($data['product_id']);

                $qtyReceivedMethod = QtyReceivedMethod::MANUAL;

                if (Package::isPluginInstalled('inventories')) {
                    $qtyReceivedMethod = QtyReceivedMethod::STOCK_MOVE;
                }

                $data = array_merge($data, [
                    'name'                => $product->name,
                    'state'               => $record->state->value,
                    'qty_received_method' => $qtyReceivedMethod,
                    'uom_id'              => $data['uom_id'] ?? $product->uom_id,
                    'currency_id'         => $record->currency_id,
                    'partner_id'          => $record->partner_id,
                    'creator_id'          => Auth::id(),
                    'company_id'          => $record->company_id,
                ]);

                return $data;
            })->extraItemActions([
                Action::make('openProduct')
                    ->tooltip(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.actions.open-product.tooltip'))
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->url(function (array $arguments, Get $get): ?string {
                        $productId = $get("products.{$arguments['item']}.product_id");

                        if (! $productId) {
                            return null;
                        }

                        return ProductResource::getUrl('edit', ['record' => $productId]);
                    }, shouldOpenInNewTab: true)
                    ->hidden(
                        fn (array $arguments, Get $get): bool => empty($get("products.{$arguments['item']}.product_id"))
                    ),
            ]);
    }

    private static function afterProductUpdated(Set $set, Get $get): void
    {
        if (! $get('product_id')) {
            return;
        }

        $product = Product::find($get('product_id'));

        $set('uom_id', $product->uom_id);

        $uomQuantity = static::calculateUnitQuantity($product->uom_id, $get('product_qty'), $product->uom_id);

        $set('product_uom_qty', round($uomQuantity, 2));

        $priceUnit = static::calculateUnitPrice($get);

        $set('price_unit', round($priceUnit, 2));

        $set('taxes', Tax::forProduct($product, TypeTaxUse::PURCHASE, $get('../../company_id')));

        $packaging = static::getBestPackaging($get('product_id'), round($uomQuantity, 2));

        $set('product_packaging_id', $packaging['packaging_id'] ?? null);

        $set('product_packaging_qty', $packaging['packaging_qty'] ?? null);

        self::calculateLineTotals($set, $get);
    }

    private static function afterProductQtyUpdated(Set $set, Get $get): void
    {
        if (! $get('product_id')) {
            return;
        }

        $product = Product::find($get('product_id'));

        $uomQuantity = static::calculateUnitQuantity($get('uom_id'), $get('product_qty'), $product->uom_id);

        $set('product_uom_qty', round($uomQuantity, 2));

        $packaging = static::getBestPackaging($get('product_id'), $uomQuantity);

        $set('product_packaging_id', $packaging['packaging_id'] ?? null);

        $set('product_packaging_qty', $packaging['packaging_qty'] ?? null);

        if (! $get('from_requisition')) {
            $priceUnit = static::calculateUnitPrice($get);

            $set('price_unit', round($priceUnit, 2));
        }

        self::calculateLineTotals($set, $get);

        self::checkBlanketOrderQtyLimit($get);
    }

    private static function afterUOMUpdated(Set $set, Get $get): void
    {
        if (! $get('product_id')) {
            return;
        }

        $product = Product::find($get('product_id'));

        $uomQuantity = static::calculateUnitQuantity($get('uom_id'), $get('product_qty'), $product->uom_id);

        $set('product_uom_qty', round($uomQuantity, 2));

        $packaging = static::getBestPackaging($get('product_id'), $uomQuantity);

        $set('product_packaging_id', $packaging['packaging_id'] ?? null);

        $set('product_packaging_qty', $packaging['packaging_qty'] ?? null);

        $priceUnit = static::calculateUnitPrice($get);

        $set('price_unit', round($priceUnit, 2));

        self::calculateLineTotals($set, $get);
    }

    private static function afterProductPackagingQtyUpdated(Set $set, Get $get): void
    {
        if (! $get('product_id')) {
            return;
        }

        if ($get('product_packaging_id')) {
            $packaging = Packaging::find($get('product_packaging_id'));

            $packagingQty = floatval($get('product_packaging_qty') ?? 0);

            $productUOMQty = $packagingQty * $packaging->qty;

            $set('product_uom_qty', round($productUOMQty, 2));

            $uom = UOM::find($get('uom_id'));

            $productQty = $uom ? $productUOMQty * $uom->factor : $productUOMQty;

            $set('product_qty', round($productQty, 2));
        }

        self::calculateLineTotals($set, $get);
    }

    private static function afterProductPackagingUpdated(Set $set, Get $get): void
    {
        if (! $get('product_id')) {
            return;
        }

        if ($get('product_packaging_id')) {
            $packaging = Packaging::find($get('product_packaging_id'));

            $productUOMQty = $get('product_uom_qty') ?: 1;

            if ($packaging) {
                $packagingQty = $productUOMQty / $packaging->qty;

                $set('product_packaging_qty', $packagingQty);
            }
        } else {
            $set('product_packaging_qty', null);
        }

        self::calculateLineTotals($set, $get);
    }

    private static function calculateUnitQuantity($fromUomId, $quantity, $toUomId = null): float
    {
        if (! $fromUomId || ! filled($quantity)) {
            return (float) ($quantity ?? 0);
        }

        $fromUom = UOM::find($fromUomId);

        if (! $fromUom) {
            return (float) ($quantity ?? 0);
        }

        $toUom = $toUomId ? UOM::find($toUomId) : $fromUom;

        if (! $toUom) {
            return (float) ($quantity ?? 0);
        }

        return $fromUom->computeQuantity((float) ($quantity ?? 0), $toUom, false);
    }

    private static function convertPrice($amount, ?int $fromCurrencyId, ?int $toCurrencyId, ?int $companyId): float
    {
        if (! $amount || ! $fromCurrencyId || ! $toCurrencyId || $fromCurrencyId === $toCurrencyId) {
            return (float) $amount;
        }

        $fromCurrency = Currency::find($fromCurrencyId);

        $toCurrency = Currency::find($toCurrencyId);

        if (! $fromCurrency || ! $toCurrency) {
            return (float) $amount;
        }

        return $fromCurrency->convert($amount, $toCurrency, Company::find($companyId), round: false);
    }

    private static function resolveVendorPrice($product, $seller, ?int $currencyId, ?int $companyId): float
    {
        if ($seller) {
            return static::convertPrice($seller->price, $seller->currency_id, $currencyId, $companyId);
        }

        return static::convertPrice(
            $product->cost ?: $product->price,
            Company::find($companyId)?->currency_id,
            $currencyId,
            $companyId
        );
    }

    private static function calculateUnitPrice($get)
    {
        $product = Product::find($get('product_id'));

        $vendorPrices = $product->sellers->sortByDesc('sort');

        if ($get('../../partner_id')) {
            $vendorPrices = $vendorPrices->where('partner_id', $get('../../partner_id'));
        }

        $vendorPrice = static::resolveVendorPrice(
            $product,
            $vendorPrices->where('min_qty', '<=', $get('product_qty') ?? 1)->first(),
            $get('../../currency_id'),
            $get('../../company_id') ?? current_company_id()
        );

        if (! $get('uom_id') || ! $product->uom) {
            return $vendorPrice;
        }

        $uomQty = UOM::find($get('uom_id'))->computeQuantity(1, $product->uom, false);

        return (float) ($vendorPrice * $uomQty);
    }

    private static function getBestPackaging($productId, $quantity)
    {
        $packagings = Packaging::where('product_id', $productId)
            ->orderByDesc('qty')
            ->get();

        foreach ($packagings as $packaging) {
            if ($quantity && $quantity % $packaging->qty == 0) {
                return [
                    'packaging_id'  => $packaging->id,
                    'packaging_qty' => round($quantity / $packaging->qty, 2),
                ];
            }
        }

        return null;
    }

    private static function calculateLineTotals(Set $set, Get $get, ?string $prefix = ''): void
    {
        if (! $get($prefix.'product_id')) {
            $set($prefix.'price_unit', 0);

            $set($prefix.'discount', 0);

            $set($prefix.'price_tax', 0);

            $set($prefix.'price_subtotal', 0);

            $set($prefix.'price_total', 0);

            return;
        }

        $priceUnit = floatval($get($prefix.'price_unit'));

        $quantity = floatval($get($prefix.'product_qty') ?? 1);

        $discountValue = floatval($get($prefix.'discount') ?? 0);

        $discountedUnit = $discountValue > 0 ? $priceUnit * (1 - ($discountValue / 100)) : $priceUnit;

        $taxIds = $get($prefix.'taxes') ?? [];

        $taxes = Tax::whereIn('id', $taxIds)->get();

        if ($taxes->isEmpty()) {
            $subTotal = round($discountedUnit * $quantity, 4);

            $set($prefix.'price_subtotal', $subTotal);

            $set($prefix.'price_tax', 0);

            $set($prefix.'price_total', $subTotal);
        } else {
            $taxResult = TaxFacade::computeAll($taxes, $discountedUnit, null, $quantity);

            $set($prefix.'price_subtotal', round($taxResult['total_excluded'], 4));

            $set($prefix.'price_tax', round($taxResult['total_included'] - $taxResult['total_excluded'], 4));

            $set($prefix.'price_total', round($taxResult['total_included'], 4));
        }
    }

    public static function calculateOrderTotals(Get $get, $livewire): array
    {
        $defaultTotals = [
            'subtotal'         => 0,
            'totalTax'         => 0,
            'grandTotal'       => 0,
            'currency_id'      => $get('currency_id'),
        ];

        $products = $get('products') ?? [];

        if (empty($products)) {
            $livewire->dispatch('itemUpdated', $defaultTotals);

            return $defaultTotals;
        }

        $subtotal = 0;
        $totalTax = 0;
        $grandTotal = 0;

        foreach ($products as $product) {
            if (empty($product['product_id'])) {
                continue;
            }

            $subtotal += floatval($product['price_subtotal'] ?? 0);
            $totalTax += floatval($product['price_tax'] ?? 0);
            $grandTotal += floatval($product['price_total'] ?? 0);
        }

        $totals = [
            'subtotal'         => round($subtotal, 2),
            'totalTax'         => round($totalTax, 2),
            'grandTotal'       => round($grandTotal, 2),
            'currency_id'      => $get('currency_id'),
        ];

        $livewire->dispatch('itemUpdated', $totals);

        return $totals;
    }

    public static function handleVendorChange($state, Set $set, Get $get): void
    {
        if (! $state) {
            $set('requisition_id', null);

            return;
        }

        $vendor = Partner::find($state);

        $set('payment_term_id', $vendor->property_supplier_payment_term_id);

        $set('requisition_id', null);

        if (static::getOrderSettings()->enable_purchase_agreements) {
            $activeAgreement = static::getActiveAgreementForVendor($state, $get('company_id') ?? current_company_id());

            if ($activeAgreement) {
                $set('requisition_id', $activeAgreement->id);

                $products = static::mapRequisitionLinesToProducts($activeAgreement);

                $set('products', $products);

                $set('currency_id', $activeAgreement?->currency_id);

                foreach (array_keys($products) as $key) {
                    self::calculateLineTotals($set, $get, "products.$key.");
                }

                return;
            }
        }

        static::updateProductPricesForVendor($state, $set, $get);
    }

    public static function handleRequisitionChange($state, Set $set, Get $get): void
    {
        if (! $state) {
            return;
        }

        $requisition = Requisition::find($state);

        if (! $requisition) {
            return;
        }

        $products = static::mapRequisitionLinesToProducts($requisition);

        $set('products', $products);

        $set('currency_id', $requisition?->currency_id);

        foreach (array_keys($products) as $key) {
            self::calculateLineTotals($set, $get, "products.$key.");
        }
    }

    private static function getActiveAgreementForVendor(int $partnerId, ?int $companyId = null): ?Requisition
    {
        return Requisition::where('partner_id', $partnerId)
            ->where('company_id', $companyId ?? current_company_id())
            ->where('state', RequisitionState::CONFIRMED)
            ->where(function ($query) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', now());
            })
            ->where(function ($query) {
                $query->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->latest()
            ->first();
    }

    private static function mapRequisitionLinesToProducts(Requisition $requisition): array
    {
        $products = [];

        foreach ($requisition->lines as $line) {
            $product = $line->product;
            $uom = $line->uom;

            $products[] = [
                'product_id'        => $product?->id,
                'uom_id'            => $uom?->id,
                'product_qty'       => $line->qty,
                'price_unit'        => $line->price_unit,
                'planned_at'        => now(),
                'taxes'             => $product ? Tax::forProduct($product, TypeTaxUse::PURCHASE, $requisition->company_id) : [],
                'discount'          => 0,
                'from_requisition'  => true,
            ];
        }

        return $products;
    }

    public static function updateProductPricesForVendor($partnerId, Set $set, Get $get): void
    {
        $products = $get('products');

        if (! is_array($products)) {
            return;
        }

        foreach ($products as $key => $product) {
            if (! isset($product['product_id'])) {
                continue;
            }

            $productModel = Product::find($product['product_id']);

            if (! $productModel) {
                continue;
            }

            $seller = $productModel->sellers
                ->where('partner_id', $partnerId)
                ->where('min_qty', '<=', $product['product_qty'] ?? 1)
                ->sortByDesc('sort')
                ->first();

            $vendorPrice = static::resolveVendorPrice(
                $productModel,
                $seller,
                $get('currency_id'),
                $get('company_id') ?? current_company_id()
            );

            $set("products.$key.price_unit", round($vendorPrice, 2));

            self::calculateLineTotals($set, $get, "products.$key.");
        }
    }

    private static function checkBlanketOrderQtyLimit(Get $get, ?string $prefix = ''): void
    {
        $requisitionId = $get('../../requisition_id');

        if (! $requisitionId) {
            return;
        }

        $requisition = Requisition::find($requisitionId);

        if (! $requisition || $requisition->type !== RequisitionType::BLANKET_ORDER) {
            return;
        }

        $productId = $get($prefix.'product_id');
        $productQty = floatval($get($prefix.'product_qty') ?? 0);

        if (! $productId || $productQty <= 0) {
            return;
        }

        $requisitionLine = $requisition->lines->where('product_id', $productId)->first();

        if (! $requisitionLine) {
            return;
        }

        $orderedQty = (float) OrderLine::query()
            ->where('product_id', $productId)
            ->whereHas('order', fn ($query) => $query
                ->where('requisition_id', $requisitionId)
                ->whereIn('state', [OrderState::PURCHASE->value, OrderState::DONE->value])
            )
            ->sum('product_qty');

        $availableQty = $requisitionLine->qty - $orderedQty;

        if ($productQty > $availableQty) {
            Notification::make()
                ->warning()
                ->title(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.notifications.blanket-order-qty-limit.title'))
                ->body(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.notifications.blanket-order-qty-limit.body', [
                    'product_qty'     => $productQty,
                    'available_qty'   => $availableQty,
                ]))
                ->send();
        }
    }

    public static function getInventoryOperationTypeId(?int $companyId): ?int
    {
        if (! $companyId || ! static::canUseInventoryWarehouses()) {
            return null;
        }

        $operationType = OperationType::where('type', InventoryEnums\OperationType::INCOMING)
            ->whereHas('warehouse', function ($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })
            ->first();

        if (! $operationType) {
            $operationType = OperationType::where('type', InventoryEnums\OperationType::INCOMING)
                ->whereDoesntHave('warehouse')
                ->first();
        }

        return $operationType?->id;
    }

    public static function canUseInventoryWarehouses(): bool
    {
        return Package::isPluginInstalled('inventories');
    }

    public static function getOrderSettings(): OrderSettings
    {
        return settings(OrderSettings::class);
    }

    public static function getProductSettings(): ProductSettings
    {
        return settings(ProductSettings::class);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->orderByDesc('id');
    }
}
