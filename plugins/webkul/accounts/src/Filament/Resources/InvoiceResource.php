<?php

namespace Webkul\Account\Filament\Resources;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Radio;
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
use Illuminate\Support\Str;
use Webkul\Account\Enums\DisplayType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\TypeTaxUse;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Facades\Tax as TaxFacade;
use Webkul\Account\Filament\Resources\InvoiceResource\Pages\CreateInvoice;
use Webkul\Account\Filament\Resources\InvoiceResource\Pages\EditInvoice;
use Webkul\Account\Filament\Resources\InvoiceResource\Pages\ListInvoices;
use Webkul\Account\Filament\Resources\InvoiceResource\Pages\ViewInvoice;
use Webkul\Account\Filament\Resources\InvoiceResource\Schemas\InvoiceForm;
use Webkul\Account\Filament\Resources\InvoiceResource\Schemas\InvoiceInfolist;
use Webkul\Account\Filament\Resources\InvoiceResource\Tables\InvoicesTable;
use Webkul\Account\Livewire\InvoiceSummary;
use Webkul\Account\Models\CashRounding;
use Webkul\Account\Models\Invoice;
use Webkul\Account\Models\MoveLine;
use Webkul\Account\Models\Product;
use Webkul\Account\Models\Tax;
use Webkul\Field\Filament\Traits\HasCustomFields;
use Webkul\Product\Enums\ProductType;
use Webkul\Product\Filament\Resources\ProductResource as BaseProductResource;
use Webkul\Product\Models\Category as ProductCategory;
use Webkul\Product\Models\Product as BaseProduct;
use Webkul\Product\Settings\ProductSettings;
use Webkul\Support\Filament\Forms\Components\Repeater;
use Webkul\Support\Filament\Forms\Components\Repeater\TableColumn;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;
use Webkul\Support\Models\UOM;

class InvoiceResource extends Resource
{
    use HasCustomFields;

    protected static ?string $model = Invoice::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-receipt-percent';

    protected static bool $shouldRegisterNavigation = false;

    protected static bool $isGloballySearchable = false;

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('accounts::filament/resources/invoice.global-search.customer') => $record?->partner?->name ?? '—',
            __('accounts::filament/resources/invoice.global-search.date')     => $record?->invoice_date ?? '—',
            __('accounts::filament/resources/invoice.global-search.due-date') => $record?->invoice_date_due ?? '—',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return InvoiceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InvoicesTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return InvoiceInfolist::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListInvoices::route('/'),
            'create' => CreateInvoice::route('/create'),
            'view'   => ViewInvoice::route('/{record}'),
            'edit'   => EditInvoice::route('/{record}/edit'),
        ];
    }

    public static function getProductRepeater(): Repeater
    {
        return Repeater::make('products')
            ->relationship('invoiceLines')
            ->hiddenLabel()
            ->compact()
            ->live()
            ->label(__('accounts::filament/resources/invoice.form.tabs.invoice-lines.repeater.products.title'))
            ->addActionLabel(__('accounts::filament/resources/invoice.form.tabs.invoice-lines.repeater.products.add-product'))
            // Clients asked whether editing a line price changes the product.
            // It never did; this says so where the price is typed.
            ->helperText(__('accounts::filament/resources/invoice.form.tabs.invoice-lines.repeater.products.price-hint'))
            ->collapsible()
            ->defaultItems(0)
            ->deleteAction(function (Action $action) {
                $action->requiresConfirmation();

                $action->after(function (Get $get, $livewire) {
                    $totals = self::calculateMoveTotals($get, $livewire);

                    $livewire->dispatch('itemUpdated', $totals);
                });
            })
            ->addable(fn ($record): bool => ! in_array($record?->state, [MoveState::POSTED, MoveState::CANCEL]))
            ->deletable(fn ($record): bool => ! in_array($record?->state, [MoveState::POSTED, MoveState::CANCEL]))
            ->table([
                TableColumn::make('product_id')
                    ->label(__('accounts::filament/resources/invoice.form.tabs.invoice-lines.repeater.products.columns.product'))
                    ->width(300)
                    ->resizable()
                    ->markAsRequired()
                    ->toggleable(),
                TableColumn::make('quantity')
                    ->label(__('accounts::filament/resources/invoice.form.tabs.invoice-lines.repeater.products.columns.quantity'))
                    ->resizable()
                    ->markAsRequired()
                    ->toggleable(),
                TableColumn::make('uom_id')
                    ->label(__('accounts::filament/resources/invoice.form.tabs.invoice-lines.repeater.products.columns.unit'))
                    ->resizable()
                    ->visible(fn () => settings(ProductSettings::class)->enable_uom)
                    ->toggleable(),
                TableColumn::make('price_unit')
                    ->label(__('accounts::filament/resources/invoice.form.tabs.invoice-lines.repeater.products.columns.unit-price'))
                    ->resizable()
                    ->markAsRequired(),
                TableColumn::make('discount')
                    ->label(__('accounts::filament/resources/invoice.form.tabs.invoice-lines.repeater.products.columns.discount-percentage'))
                    ->resizable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TableColumn::make('taxes')
                    ->label(__('accounts::filament/resources/invoice.form.tabs.invoice-lines.repeater.products.columns.taxes'))
                    ->resizable()
                    ->toggleable(),
                TableColumn::make('price_subtotal')
                    ->label(__('accounts::filament/resources/invoice.form.tabs.invoice-lines.repeater.products.columns.sub-total'))
                    ->resizable()
                    ->toggleable(),
            ])
            ->schema([
                Select::make('product_id')
                    ->label(__('accounts::filament/resources/invoice.form.tabs.invoice-lines.repeater.products.fields.product'))
                    ->relationship(
                        'product',
                        'name',
                        fn (Builder $query, Get $get) => $query
                            ->withTrashed()
                            ->where('is_configurable', null)
                            ->where(owned_by_company($get('../../company_id'))),
                    )
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
                    ->searchable()
                    ->preload()
                    ->live()
                    ->dehydrated()
                    ->disabled(fn ($record) => in_array($record?->parent_state, [MoveState::POSTED, MoveState::CANCEL]))
                    ->afterStateUpdated(fn (Set $set, Get $get) => static::afterProductUpdated($set, $get))
                    /*
                     * "+" adds a product or service from the invoice line
                     * (user, 2026-11-20, after Zoho Books): name, kind and
                     * price only; unit and category take the product form's
                     * own defaults. Needs the product create permission.
                     */
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label(__('accounts::filament/resources/invoice.form.tabs.invoice-lines.repeater.products.fields.product'))
                            ->required()
                            ->maxLength(255),
                        Radio::make('type')
                            ->hiddenLabel()
                            ->options(ProductType::class)
                            ->default(ProductType::SERVICE->value)
                            ->inline()
                            ->required(),
                        TextInput::make('price')
                            ->label(__('accounts::filament/resources/invoice.form.tabs.invoice-lines.repeater.products.fields.unit-price'))
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->required(),
                    ])
                    ->createOptionUsing(function (array $data): int {
                        $uomId = BaseProductResource::getDefaultUomIdByProductType($data['type']);

                        return Product::create([
                            'name'        => $data['name'],
                            'type'        => $data['type'],
                            'price'       => $data['price'],
                            'uom_id'      => $uomId,
                            'uom_po_id'   => $uomId,
                            'category_id' => ProductCategory::query()->orderBy('id')->value('id'),
                            'company_id'  => current_company_id(),
                        ])->getKey();
                    })
                    ->createOptionAction(fn (Action $action) => $action
                        ->visible(fn (): bool => (bool) Auth::user()?->can('create', BaseProduct::class)))
                    ->required(),
                TextInput::make('quantity')
                    ->label(__('accounts::filament/resources/invoice.form.tabs.invoice-lines.repeater.products.fields.quantity'))
                    ->required()
                    ->default(1)
                    ->numeric()
                    ->maxValue(99999999999)
                    ->live(onBlur: true)
                    ->dehydrated()
                    ->disabled(fn ($record) => in_array($record?->parent_state, [MoveState::POSTED, MoveState::CANCEL]))
                    ->afterStateUpdated(fn (Set $set, Get $get) => static::afterProductQtyUpdated($set, $get)),
                Select::make('uom_id')
                    ->label(__('accounts::filament/resources/invoice.form.tabs.invoice-lines.repeater.products.fields.unit'))
                    ->relationship(
                        'uom',
                        'name',
                        function (Builder $query, Get $get) {
                            $product = Product::find($get('product_id'));
                            $categoryId = $product?->uom?->category_id;

                            return $query->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))->orderBy('id');
                        },
                    )
                    ->wrapOptionLabels(false)
                    ->required()
                    ->live()
                    ->native(false)
                    ->selectablePlaceholder(false)
                    ->dehydrated()
                    ->disabled(fn ($record) => in_array($record?->parent_state, [MoveState::POSTED, MoveState::CANCEL]))
                    ->afterStateUpdated(fn (Set $set, Get $get) => static::afterUOMUpdated($set, $get))
                    ->visible(fn (ProductSettings $settings) => $settings->enable_uom),
                TextInput::make('price_unit')
                    ->label(__('accounts::filament/resources/invoice.form.tabs.invoice-lines.repeater.products.fields.unit-price'))
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->maxValue(99999999999)
                    ->required()
                    ->live(onBlur: true)
                    ->dehydrated()
                    ->disabled(fn ($record) => in_array($record?->parent_state, [MoveState::POSTED, MoveState::CANCEL]))
                    ->afterStateUpdated(fn (Set $set, Get $get) => self::calculateLineTotals($set, $get)),
                TextInput::make('discount')
                    ->label(__('accounts::filament/resources/invoice.form.tabs.invoice-lines.repeater.products.fields.discount-percentage'))
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->maxValue(99999999999)
                    ->live(onBlur: true)
                    ->dehydrated()
                    ->disabled(fn ($record) => in_array($record?->parent_state, [MoveState::POSTED, MoveState::CANCEL]))
                    ->afterStateUpdated(fn (Set $set, Get $get) => self::calculateLineTotals($set, $get)),
                Select::make('taxes')
                    ->label(__('accounts::filament/resources/invoice.form.tabs.invoice-lines.repeater.products.fields.taxes'))
                    ->relationship(
                        'taxes',
                        'name',
                        modifyQueryUsing: fn (Builder $query, Get $get) => $query
                            ->where('type_tax_use', TypeTaxUse::SALE)
                            ->where(owned_by_company($get('../../company_id'))),
                    )
                    ->wrapOptionLabels(false)
                    ->searchable()
                    ->multiple()
                    ->preload()
                    ->dehydrated()
                    ->disabled(fn ($record) => in_array($record?->parent_state, [MoveState::POSTED, MoveState::CANCEL]))
                    ->afterStateHydrated(fn (Get $get, Set $set) => self::calculateLineTotals($set, $get))
                    ->afterStateUpdated(fn (Get $get, Set $set) => self::calculateLineTotals($set, $get))
                    ->live(),
                TextInput::make('price_subtotal')
                    ->label(__('accounts::filament/resources/invoice.form.tabs.invoice-lines.repeater.products.fields.sub-total'))
                    ->numeric()
                    ->default(0)
                    ->readOnly()
                    ->reactive()
                    ->dehydrated(),
                Hidden::make('product_uom_qty')
                    ->default(0)
                    ->dehydrated(),
                Hidden::make('price_tax')
                    ->default(0)
                    ->dehydrated(),
                Hidden::make('price_total')
                    ->default(0)
                    ->dehydrated(),
            ])
            ->mutateRelationshipDataBeforeCreateUsing(fn (array $data, $record) => static::mutateProductRelationship($data, $record))
            ->mutateRelationshipDataBeforeSaveUsing(fn (array $data, $record) => static::mutateProductRelationship($data, $record));
    }

    public static function getSummaryComponent()
    {
        return InvoiceSummary::class;
    }

    public static function mutateProductRelationship(array $data, $record): array
    {
        $data['currency_id'] = $record->currency_id;

        return $data;
    }

    private static function afterProductUpdated(Set $set, Get $get): void
    {
        if (! $get('product_id')) {
            return;
        }

        $product = Product::find($get('product_id'));

        $set('uom_id', $product->uom_id);

        $priceUnit = static::calculateUnitPrice($product->uom_id, $product);

        if ($get('../../currency_id')) {
            $currency = Currency::find($get('../../currency_id'));

            $company = Company::find($get('../../company_id')) ?? current_company();

            // No rate means no conversion and a warning, never a silent 1:1
            // (a GHS price used to land unchanged on a USD invoice).
            if ($company->currency->canConvertTo($currency, $company)) {
                $priceUnit = $company->currency->convert(
                    $priceUnit,
                    $currency,
                    $company
                );
            } else {
                static::notifyMissingRate($company->currency, $currency);
            }
        }

        $set('price_unit', round($priceUnit, 2));

        $set('taxes', Tax::forProduct($product, TypeTaxUse::SALE, $get('../../company_id')));

        $uomQuantity = static::calculateUnitQuantity($get('uom_id'), $get('quantity'));

        $set('product_uom_qty', round($uomQuantity, 2));

        self::calculateLineTotals($set, $get);
    }

    private static function afterProductQtyUpdated(Set $set, Get $get): void
    {
        if (! $get('product_id')) {
            return;
        }

        $uomQuantity = static::calculateUnitQuantity($get('uom_id'), $get('quantity'));

        $set('product_uom_qty', round($uomQuantity, 2));

        self::calculateLineTotals($set, $get);
    }

    private static function afterUOMUpdated(Set $set, Get $get): void
    {
        if (! $get('product_id')) {
            return;
        }

        $uomQuantity = static::calculateUnitQuantity($get('uom_id'), $get('quantity'));

        $set('product_uom_qty', round($uomQuantity, 2));

        $product = Product::find($get('product_id'));

        $priceUnit = static::calculateUnitPrice($get('uom_id'), $product);

        $set('price_unit', round($priceUnit, 2));

        self::calculateLineTotals($set, $get);
    }

    private static function calculateUnitQuantity($uomId, $quantity)
    {
        if (! $uomId || ! filled($quantity)) {
            return (float) ($quantity ?? 0);
        }

        $fromUom = UOM::find($uomId);

        if (! $fromUom) {
            return (float) ($quantity ?? 0);
        }

        $referenceUom = UOM::where('category_id', $fromUom->category_id)->orderBy('factor')->first();

        if (! $referenceUom) {
            return (float) ($quantity ?? 0);
        }

        return $fromUom->computeQuantity((float) ($quantity ?? 0), $referenceUom, false);
    }

    private static function calculateUnitPrice($uomId, $product)
    {
        $price = $product->price ?? $product->cost;

        if (! $uomId || ! $product->uom) {
            return $price;
        }

        $uomQty = UOM::find($uomId)->computeQuantity(1, $product->uom, false);

        return (float) ($price * $uomQty);
    }

    private static function calculateLineTotals(Set $set, Get $get): void
    {
        if (! $get('product_id')) {
            $set('price_unit', 0);
            $set('discount', 0);
            $set('price_tax', 0);
            $set('price_subtotal', 0);
            $set('price_total', 0);

            return;
        }

        $currencyId = $get('../../currency_id');
        $companyId = $get('../../company_id');
        $productId = $get('product_id');

        if (! $currencyId || ! $companyId || ! $productId) {
            return;
        }

        $currency = Currency::find($currencyId);
        $company = Company::find($companyId);
        $product = Product::find($productId);

        if (! $currency || ! $company || ! $product) {
            return;
        }

        $totals = static::lineTotals([
            'quantity'   => $get('quantity'),
            'price_unit' => $get('price_unit'),
            'discount'   => $get('discount'),
            'taxes'      => $get('taxes'),
        ], $product, $currency, $company, $get('../../move_type'));

        $set('price_subtotal', $totals['price_subtotal']);
        $set('price_tax', $totals['price_tax']);
        $set('price_total', $totals['price_total']);
    }

    /**
     * One line's subtotal, tax and total. The single place this is computed,
     * used when a line field changes and when the invoice currency changes.
     *
     * @param  array{quantity?: mixed, price_unit?: mixed, discount?: mixed, taxes?: mixed}  $line
     * @return array{price_subtotal: float, price_tax: float, price_total: float}
     */
    private static function lineTotals(array $line, Product $product, Currency $currency, Company $company, mixed $moveType): array
    {
        $mockLine = new MoveLine([
            'quantity'     => $line['quantity'] ?? 1,
            'price_unit'   => $line['price_unit'] ?? 0,
            'discount'     => $line['discount'] ?? 0,
            'display_type' => DisplayType::PRODUCT,
        ]);

        $mockMove = new Invoice([
            'move_type'   => $moveType,
            'currency_id' => $currency->id,
            'company_id'  => $company->id,
        ]);

        $mockLine->setRelation('taxes', Tax::whereIn('id', $line['taxes'] ?? [])->get());
        $mockLine->setRelation('currency', $currency);
        $mockLine->setRelation('company', $company);
        $mockLine->setRelation('product', $product);
        $mockLine->setRelation('move', $mockMove);

        $mockMove->setRelation('currency', $currency);
        $mockMove->setRelation('company', $company);

        $baseLine = TaxFacade::withTaxDetails(AccountFacade::productBaseLine($mockLine), $company);

        $subtotal = $baseLine['tax_details']['raw_total_excluded_currency'];
        $total = $baseLine['tax_details']['raw_total_included_currency'];

        return [
            'price_subtotal' => round($subtotal, 4),
            'price_tax'      => round($total - $subtotal, 4),
            'price_total'    => round($total, 4),
        ];
    }

    /**
     * The invoice currency changed: convert every line's price from the old
     * currency to the new one, recompute each line, and refresh the summary.
     * Without a rate the prices are left as they are and the user is told
     * which rate is missing, rather than converting at 1:1.
     */
    public static function afterCurrencyUpdated(Set $set, Get $get, mixed $state, mixed $old, $livewire): void
    {
        $from = $old ? Currency::find($old) : null;
        $to = $state ? Currency::find($state) : null;
        $company = Company::find($get('company_id')) ?? current_company();

        if (! $to || ! $company) {
            return;
        }

        $products = $get('products') ?? [];

        if ($from && $from->id !== $to->id && ! empty($products)) {
            if ($from->canConvertTo($to, $company)) {
                foreach ($products as $key => $line) {
                    $product = filled($line['product_id'] ?? null) ? Product::find($line['product_id']) : null;

                    if (! $product) {
                        continue;
                    }

                    $line['price_unit'] = round($from->convert((float) ($line['price_unit'] ?? 0), $to, $company), 2);

                    $products[$key] = array_merge($line, static::lineTotals($line, $product, $to, $company, $get('move_type')));
                }

                $set('products', $products);
            } else {
                static::notifyMissingRate($from, $to);
            }
        }

        self::calculateMoveTotals($get, $livewire);
    }

    private static function notifyMissingRate(Currency $from, Currency $to): void
    {
        Notification::make()
            ->warning()
            ->title(__('accounts::filament/resources/invoice.form.notifications.missing-rate.title'))
            ->body(__('accounts::filament/resources/invoice.form.notifications.missing-rate.body', [
                'from' => $from->name,
                'to'   => $to->name,
            ]))
            ->persistent()
            ->send();
    }

    public static function calculateMoveTotals(Get $get, $livewire): array
    {
        $defaultTotals = [
            'subtotal'   => 0,
            'totalTax'   => 0,
            'grandTotal' => 0,
            'rounding'   => 0,
        ];

        $currencyId = $get('currency_id');
        $companyId = $get('company_id');
        $products = $get('products') ?? [];

        if (! $currencyId || ! $companyId || empty($products)) {
            $livewire->dispatch('itemUpdated', $defaultTotals);

            return $defaultTotals;
        }

        $currency = Currency::find($currencyId);
        $company = Company::find($companyId);

        if (! $currency || ! $company) {
            $livewire->dispatch('itemUpdated', $defaultTotals);

            return $defaultTotals;
        }

        $cashRoundingId = $get('invoice_cash_rounding_id');

        $mockMove = new Invoice([
            'move_type'                => $get('move_type'),
            'currency_id'              => $currency->id,
            'company_id'               => $company->id,
            'invoice_cash_rounding_id' => $cashRoundingId,
        ]);

        $mockMove->setRelation('currency', $currency);
        $mockMove->setRelation('company', $company);

        if ($cashRoundingId) {
            $cashRounding = CashRounding::find($cashRoundingId);

            if ($cashRounding) {
                $mockMove->setRelation('invoiceCashRounding', $cashRounding);
            }
        }

        $mockLines = collect($products)
            ->filter(fn ($productData) => ! empty($productData['product_id']))
            ->map(function ($productData) use ($currency, $company, $mockMove) {
                $product = Product::find($productData['product_id']);

                if (! $product) {
                    return null;
                }

                $mockLine = new MoveLine([
                    'quantity'     => $productData['quantity'] ?? 1,
                    'price_unit'   => $productData['price_unit'] ?? 0,
                    'discount'     => $productData['discount'] ?? 0,
                    'display_type' => DisplayType::PRODUCT,
                ]);

                $mockLine->setRelation('taxes', Tax::whereIn('id', $productData['taxes'] ?? [])->get());
                $mockLine->setRelation('currency', $currency);
                $mockLine->setRelation('company', $company);
                $mockLine->setRelation('product', $product);
                $mockLine->setRelation('move', $mockMove);

                return $mockLine;
            })
            ->filter();

        if ($mockLines->isEmpty()) {
            $livewire->dispatch('itemUpdated', $defaultTotals);

            return $defaultTotals;
        }

        $mockMove->setRelation('lines', $mockLines);

        [$baseLines] = AccountFacade::roundedBaseAndTaxLines($mockMove, false);

        $subtotal = 0;
        $grandTotal = 0;
        $rounding = 0;

        foreach ($baseLines as $baseLine) {
            $specialType = $baseLine['special_type'] ?? null;

            if ($specialType === 'cash_rounding') {
                $rounding = $baseLine['tax_details']['raw_total_excluded_currency'];
            } else {
                $subtotal += $baseLine['tax_details']['raw_total_excluded_currency'] ?? 0;
                $grandTotal += $baseLine['tax_details']['raw_total_included_currency'] ?? 0;
            }
        }

        if ($rounding == 0 && $cashRoundingId) {
            $cashRounding = CashRounding::find($cashRoundingId);

            if ($cashRounding) {
                $rounding = $cashRounding->computeDifference($currency, $grandTotal);
            }
        }

        $defaultTotals = [
            'subtotal'   => round($subtotal, 2),
            'totalTax'   => round($grandTotal - $subtotal, 2),
            'grandTotal' => round($grandTotal, 2),
            'rounding'   => round($rounding, 2),
            // The summary is a child component mounted once; without the
            // currency here it kept showing the first currency's symbol.
            'currencyId' => $currency->id,
        ];

        $livewire->dispatch('itemUpdated', $defaultTotals);

        return $defaultTotals;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->when(Str::contains(static::class, 'InvoiceResource'), function (Builder $query) {
                $query->where('move_type', MoveType::OUT_INVOICE);
            });
    }

    public static function disableTypeField($component): void
    {
        if (method_exists($component, 'getChildComponents')) {
            foreach ($component->getChildComponents() as $child) {
                static::disableTypeField($child);
            }
        }

        if (method_exists($component, 'getName') && $component->getName() === 'type') {
            $component->disabled()->dehydrated();
        }
    }
}
