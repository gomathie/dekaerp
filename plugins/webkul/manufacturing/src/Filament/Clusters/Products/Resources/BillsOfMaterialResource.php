<?php

namespace Webkul\Manufacturing\Filament\Clusters\Products\Resources;

use BackedEnum;
use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Webkul\Manufacturing\Enums\OperationTimeMode;
use Webkul\Manufacturing\Enums\OperationWorksheetType;
use Webkul\Manufacturing\Filament\Clusters\Configurations\Resources\OperationResource;
use Webkul\Manufacturing\Filament\Clusters\Products;
use Webkul\Manufacturing\Filament\Clusters\Products\Resources\BillsOfMaterialResource\Pages\BillOfMaterialOverview;
use Webkul\Manufacturing\Filament\Clusters\Products\Resources\BillsOfMaterialResource\Pages\CreateBillOfMaterial;
use Webkul\Manufacturing\Filament\Clusters\Products\Resources\BillsOfMaterialResource\Pages\EditBillOfMaterial;
use Webkul\Manufacturing\Filament\Clusters\Products\Resources\BillsOfMaterialResource\Pages\ListBillsOfMaterial;
use Webkul\Manufacturing\Filament\Clusters\Products\Resources\BillsOfMaterialResource\Pages\ViewBillOfMaterial;
use Webkul\Manufacturing\Filament\Clusters\Products\Resources\BillsOfMaterialResource\Schemas\BillsOfMaterialForm;
use Webkul\Manufacturing\Filament\Clusters\Products\Resources\BillsOfMaterialResource\Schemas\BillsOfMaterialInfolist;
use Webkul\Manufacturing\Filament\Clusters\Products\Resources\BillsOfMaterialResource\Tables\BillsOfMaterialsTable;
use Webkul\Manufacturing\Models\BillOfMaterial;
use Webkul\Manufacturing\Models\Operation;
use Webkul\Manufacturing\Models\Product;
use Webkul\Manufacturing\Models\WorkCenter;
use Webkul\Manufacturing\Settings\OperationSettings;
use Webkul\Product\Models\ProductAttributeValue;
use Webkul\Support\Filament\Forms\Components\Repeater;
use Webkul\Support\Filament\Forms\Components\Repeater\TableColumn as RepeaterTableColumn;
use Webkul\Support\Models\Company;

class BillsOfMaterialResource extends Resource
{
    protected static ?string $model = BillOfMaterial::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?int $navigationSort = 2;

    protected static ?string $cluster = Products::class;

    protected static ?string $recordTitleAttribute = 'code';

    public static function getModelLabel(): string
    {
        return __('manufacturing::models/bill-of-material.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('manufacturing::filament/clusters/products/resources/bill-of-material.navigation.title');
    }

    public static function getSubNavigationPosition(): SubNavigationPosition
    {
        return SubNavigationPosition::Start;
    }

    public static function form(Schema $schema): Schema
    {
        return BillsOfMaterialForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BillsOfMaterialsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return BillsOfMaterialInfolist::configure($schema);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'    => ListBillsOfMaterial::route('/'),
            'create'   => CreateBillOfMaterial::route('/create'),
            'overview' => BillOfMaterialOverview::route('/{record}/overview'),
            'view'     => ViewBillOfMaterial::route('/{record}'),
            'edit'     => EditBillOfMaterial::route('/{record}/edit'),
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewBillOfMaterial::class,
            EditBillOfMaterial::class,
            BillOfMaterialOverview::class,
        ]);
    }

    protected static function getSelectedBillOfMaterialProductId(Get $get): mixed
    {
        return $get('../../product_variant_id') ?: $get('../../product_id');
    }

    protected static function isSelectedBillOfMaterialProduct(Get $get, mixed $productId): bool
    {
        $billOfMaterialProductId = static::getSelectedBillOfMaterialProductId($get);

        return filled($productId)
            && filled($billOfMaterialProductId)
            && (string) $productId === (string) $billOfMaterialProductId;
    }

    protected static function notBillOfMaterialProductRule(Get $get): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail) use ($get): void {
            if (static::isSelectedBillOfMaterialProduct($get, $value)) {
                $fail(__(
                    'manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.components.validation.component-different-from-product',
                ));
            }
        };
    }

    public static function normalizeProductVariantData(array $data): array
    {
        if (! empty($data['product_variant_id'])) {
            $data['product_id'] = $data['product_variant_id'];
        }

        unset($data['product_variant_id']);

        return $data;
    }

    public static function getComponentsRepeater(): Repeater
    {
        return Repeater::make('lines')
            ->relationship('lines')
            ->hiddenLabel()
            ->defaultItems(0)
            ->compact()
            ->addActionLabel(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.components.add-action'))
            ->table([
                RepeaterTableColumn::make('product_id')
                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.components.columns.component'))
                    ->markAsRequired()
                    ->resizable(),
                RepeaterTableColumn::make('quantity')
                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.components.columns.quantity'))
                    ->markAsRequired()
                    ->resizable(),
                RepeaterTableColumn::make('uom_id')
                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.components.columns.uom'))
                    ->markAsRequired()
                    ->resizable(),
                RepeaterTableColumn::make('attributeValues')
                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.components.columns.apply-on-variants'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->resizable(),
                RepeaterTableColumn::make('operation_id')
                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.components.columns.consumed-in-operation'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->resizable(),
                RepeaterTableColumn::make('is_manual_consumption')
                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.components.columns.highlight-consumption'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->resizable(),
            ])
            ->schema([
                Hidden::make('company_id'),
                Select::make('product_id')
                    ->relationship('product', 'name', fn (Builder $query, Get $get) => $query
                        ->withTrashed()
                        ->where(function (Builder $productQuery): void {
                            $productQuery
                                ->where('is_configurable', false)
                                ->orWhereNull('is_configurable');
                        })
                        ->where(owned_by_company($get('../../company_id'))))
                    ->searchable()
                    ->preload()
                    ->required()
                    ->createOptionForm(fn (Schema $schema): Schema => ProductResource::form($schema))
                    ->createOptionAction(fn (Action $action) => $action->modalWidth(Width::SevenExtraLarge))
                    ->live()
                    ->getOptionLabelFromRecordUsing(function ($record): string {
                        return $record->name.($record->trashed() ? ' (Deleted)' : '');
                    })
                    ->wrapOptionLabels(false)
                    ->disableOptionWhen(function ($label, $value, $state, $component, Get $get) {
                        $isDeleted = str_contains($label, ' (Deleted)');

                        $isDuplicate = false;

                        if ($component?->getParentRepeater()) {
                            $repeater = $component->getParentRepeater();

                            $isDuplicate = collect($repeater->getState())
                                ->pluck(
                                    (string) str($component->getStatePath())
                                        ->after("{$repeater->getStatePath()}.")
                                        ->after('.'),
                                )
                                ->flatten()
                                ->diff(Arr::wrap($state))
                                ->filter(fn (mixed $siblingItemState): bool => filled($siblingItemState))
                                ->contains($value);
                        }

                        return $isDeleted
                            || $isDuplicate
                            || static::isSelectedBillOfMaterialProduct($get, $value);
                    })
                    ->rules([
                        fn (Get $get): Closure => static::notBillOfMaterialProductRule($get),
                    ])
                    ->afterStateUpdated(function (Set $set, Get $get, ?string $state): void {
                        $product = Product::query()->find($state);

                        if (! $product) {
                            return;
                        }

                        $set('uom_id', $product->uom_id);
                        $set('company_id', $get('../../company_id'));
                    }),
                TextInput::make('quantity')
                    ->numeric()
                    ->minValue(0.0001)
                    ->default(1)
                    ->step('0.0001')
                    ->required(),
                Select::make('uom_id')
                    ->relationship(
                        'uom',
                        'name',
                        function (Builder $query, Get $get): Builder {
                            $product = Product::query()->withTrashed()->find($get('product_id'));
                            $categoryId = $product?->uom?->category_id;

                            return $query
                                ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
                                ->orderBy('name');
                        },
                    )
                    ->default(fn (Get $get): ?int => Product::query()->withTrashed()->find($get('product_id'))?->uom_id)
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('attributeValues')
                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.components.columns.apply-on-variants'))
                    ->relationship(
                        name: 'attributeValues',
                        titleAttribute: 'id',
                        modifyQueryUsing: function (Builder $query, Get $get) {
                            $bomProductId = $get('../../product_id');

                            return $query->where('product_id', $bomProductId);
                        },
                    )
                    ->getOptionLabelFromRecordUsing(fn ($record): string => $record->attribute?->name && $record->attributeOption?->name ? "{$record->attribute->name}: {$record->attributeOption->name}" : ($record->attributeOption?->name ?? (string) $record->id))
                    ->searchable()
                    ->preload()
                    ->multiple(),
                Select::make('operation_id')
                    ->relationship('operation', 'name')
                    ->native(false)
                    ->searchable()
                    ->preload()
                    ->wrapOptionLabels(false)
                    ->createOptionForm(fn (Schema $schema): Schema => OperationResource::form($schema))
                    ->createOptionAction(fn (Action $action) => $action->modalWidth(Width::SevenExtraLarge)),
                Checkbox::make('is_manual_consumption')
                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.components.columns.highlight-consumption'))
                    ->default(false),
            ]);
    }

    public static function getOperationsRepeater(): Repeater
    {
        return Repeater::make('operations')
            ->relationship('operations')
            ->hiddenLabel()
            ->defaultItems(0)
            ->compact()
            ->addActionLabel(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.operations.add-action'))
            ->addAction(function (Action $action): Action {
                return $action
                    ->schema(fn (Schema $schema): Schema => OperationResource::form($schema->model(Operation::class)))
                    ->modalWidth(Width::SevenExtraLarge)
                    ->fillForm(function (Get $get): array {
                        return static::getDefaultOperationRepeaterData([
                            'bill_of_material_id' => $get('../../id'),
                            'company_id'          => $get('../../company_id'),
                        ]);
                    })
                    ->action(function (array $data, Repeater $component): void {
                        $state = $component->getState() ?? [];
                        $state[(string) Str::uuid()] = static::normalizeOperationRepeaterData($data);

                        $component->state($state);
                    });
            })
            ->footerActions([
                Action::make('copyExistingOperation')
                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.operations.actions.copy-existing'))
                    ->link()
                    ->schema([
                        Select::make('operation_id')
                            ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.operations.actions.copy-existing-fields.operation'))
                            ->options(fn (): array => Operation::query()
                                ->with(['billOfMaterial.product', 'workCenter'])
                                ->orderBy('name')
                                ->get()
                                ->mapWithKeys(function (Operation $operation): array {
                                    $billOfMaterialLabel = static::getBillOfMaterialLabel($operation->billOfMaterial);
                                    $workCenterName = $operation->workCenter?->name;

                                    $labelParts = array_filter([
                                        $operation->name,
                                        $workCenterName,
                                        $billOfMaterialLabel !== '—' ? $billOfMaterialLabel : null,
                                    ]);

                                    return [$operation->id => implode(' / ', $labelParts)];
                                })
                                ->all())
                            ->native(false)
                            ->searchable()
                            ->preload()
                            ->wrapOptionLabels(false)
                            ->required(),
                    ])
                    ->modalWidth(Width::ThreeExtraLarge)
                    ->action(function (array $data, Repeater $component, Get $get): void {
                        $operation = Operation::query()
                            ->with(['attributeValues'])
                            ->find($data['operation_id']);

                        if (! $operation) {
                            return;
                        }

                        $state = $component->getState() ?? [];
                        $state[(string) Str::uuid()] = static::normalizeOperationRepeaterData([
                            'bill_of_material_id'        => $get('../../id'),
                            'company_id'                 => $get('../../company_id') ?? $operation->company_id,
                            'name'                       => $operation->name,
                            'work_center_id'             => $operation->work_center_id,
                            'time_mode'                  => $operation->time_mode?->value ?? $operation->time_mode,
                            'time_mode_batch'            => $operation->time_mode_batch,
                            'manual_cycle_time'          => (string) $operation->manual_cycle_time,
                            'worksheet_type'             => $operation->worksheet_type?->value ?? $operation->worksheet_type,
                            'worksheet'                  => $operation->worksheet,
                            'worksheet_google_slide_url' => $operation->worksheet_google_slide_url,
                            'note'                       => $operation->note,
                            'attributeValues'            => $operation->attributeValues->pluck('id')->all(),
                        ]);

                        $component->state($state);
                    }),
            ])
            ->table([
                RepeaterTableColumn::make('display_name')
                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.operations.columns.operation'))
                    ->markAsRequired()
                    ->resizable(),
                RepeaterTableColumn::make('display_work_center_id')
                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.operations.columns.work-center'))
                    ->markAsRequired()
                    ->resizable(),
                RepeaterTableColumn::make('display_time_mode')
                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.operations.columns.time-mode'))
                    ->markAsRequired()
                    ->resizable(),
                RepeaterTableColumn::make('display_time_mode_batch')
                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.operations.columns.time-mode-batch'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->resizable(),
                RepeaterTableColumn::make('display_company')
                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.operations.columns.company'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->resizable(),
                RepeaterTableColumn::make('display_attribute_values')
                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.operations.columns.apply-on-variants'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->resizable(),
                RepeaterTableColumn::make('display_manual_cycle_time')
                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.operations.columns.duration'))
                    ->markAsRequired()
                    ->resizable(),
            ])
            ->schema([
                Hidden::make('bill_of_material_id'),
                Hidden::make('company_id'),
                Hidden::make('name'),
                Hidden::make('work_center_id'),
                Hidden::make('time_mode'),
                Hidden::make('time_mode_batch'),
                Hidden::make('manual_cycle_time'),
                Hidden::make('worksheet_type'),
                Hidden::make('worksheet'),
                Hidden::make('worksheet_google_slide_url'),
                Hidden::make('note'),
                TextEntry::make('display_name')
                    ->hiddenLabel()
                    ->state(fn (Get $get): string => (string) ($get('name') ?? '—')),
                TextEntry::make('display_work_center_id')
                    ->hiddenLabel()
                    ->state(function (Get $get): string {
                        return WorkCenter::query()->find($get('work_center_id'))?->name ?? '—';
                    }),
                TextEntry::make('display_time_mode')
                    ->hiddenLabel()
                    ->state(function (Get $get): string {
                        $state = $get('time_mode');

                        if ($state instanceof OperationTimeMode) {
                            return $state->getLabel();
                        }

                        return OperationTimeMode::tryFrom((string) $state)?->getLabel() ?? '—';
                    }),
                TextEntry::make('display_time_mode_batch')
                    ->hiddenLabel()
                    ->state(fn (Get $get): string => filled($get('time_mode_batch')) ? (string) $get('time_mode_batch') : '—'),
                TextEntry::make('display_company')
                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.operations.columns.company'))
                    ->state(function (Get $get): string {
                        $companyId = $get('../../company_id');

                        return Company::query()->find($companyId)?->name ?? '—';
                    }),
                Select::make('attributeValues')
                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.operations.columns.apply-on-variants'))
                    ->native(false)
                    ->wrapOptionLabels(false)
                    ->hidden()
                    ->relationship(
                        'attributeValues',
                        'id',
                        modifyQueryUsing: function (Get $get, Builder $query): void {
                            $productId = $get('../../product_id');

                            if (! $productId) {
                                $query->whereRaw('1 = 0');

                                return;
                            }

                            $query->where('product_id', $productId);
                        }
                    )
                    ->getOptionLabelFromRecordUsing(fn ($record): string => $record->attribute?->name && $record->attributeOption?->name ? "{$record->attribute->name}: {$record->attributeOption->name}" : ($record->attributeOption?->name ?? (string) $record->id))
                    ->searchable()
                    ->preload()
                    ->multiple(),
                TextEntry::make('display_attribute_values')
                    ->hiddenLabel()
                    ->state(function (Get $get): string {
                        $attributeValueIds = $get('attributeValues') ?? [];

                        if (! is_array($attributeValueIds) || $attributeValueIds === []) {
                            return '—';
                        }

                        return ProductAttributeValue::query()
                            ->with(['attribute', 'attributeOption'])
                            ->whereIn('id', $attributeValueIds)
                            ->get()
                            ->map(fn ($record): string => $record->attribute?->name && $record->attributeOption?->name ? "{$record->attribute->name}: {$record->attributeOption->name}" : ($record->attributeOption?->name ?? (string) $record->id))
                            ->implode(', ');
                    }),
                TextEntry::make('display_manual_cycle_time')
                    ->hiddenLabel()
                    ->state(function (Get $get): string {
                        return format_float_time($get('manual_cycle_time') ?? 60, 'minutes');
                    }),
            ])
            ->mutateRelationshipDataBeforeCreateUsing(fn (array $data): array => static::normalizeOperationRepeaterData($data))
            ->mutateRelationshipDataBeforeSaveUsing(fn (array $data): array => static::normalizeOperationRepeaterData($data))
            ->extraItemActions([
                Action::make('editOperation')
                    ->icon('heroicon-o-pencil-square')
                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.operations.actions.edit'))
                    ->tooltip(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.operations.actions.edit'))
                    ->extraAttributes(['data-row-click-action' => 'true'])
                    ->schema(fn (Schema $schema): Schema => OperationResource::form($schema->model(Operation::class)))
                    ->modalWidth(Width::SevenExtraLarge)
                    ->fillForm(function (array $arguments, Repeater $component, Get $get): array {
                        return array_replace(
                            static::getDefaultOperationRepeaterData([
                                'bill_of_material_id' => $get('../../id'),
                                'company_id'          => $get('../../company_id'),
                            ]),
                            static::normalizeOperationRepeaterData(
                                $component->getRawItemState($arguments['item'])
                            ),
                        );
                    })
                    ->action(function (array $arguments, array $data, Repeater $component): void {
                        $state = $component->getState() ?? [];
                        $state[$arguments['item']] = static::normalizeOperationRepeaterData($data);

                        $component->state($state);
                    }),
            ]);
    }

    public static function getByProductsRepeater(): Repeater
    {
        return Repeater::make('byproducts')
            ->relationship('byproducts')
            ->hiddenLabel()
            ->defaultItems(0)
            ->compact()
            ->addActionLabel(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.by-products.add-action'))
            ->table([
                RepeaterTableColumn::make('product_id')
                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.by-products.columns.product'))
                    ->markAsRequired()
                    ->resizable(),
                RepeaterTableColumn::make('quantity')
                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.by-products.columns.quantity'))
                    ->markAsRequired()
                    ->resizable(),
                RepeaterTableColumn::make('uom_id')
                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.by-products.columns.uom'))
                    ->markAsRequired()
                    ->resizable(),
                RepeaterTableColumn::make('operation_id')
                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.by-products.columns.operation'))
                    ->resizable(),
            ])
            ->schema([
                Hidden::make('company_id'),
                Select::make('product_id')
                    ->relationship('product', 'name', fn (Builder $query, Get $get) => $query
                        ->withTrashed()
                        ->where(owned_by_company($get('../../company_id'))))
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()
                    ->getOptionLabelFromRecordUsing(function ($record): string {
                        return $record->name.($record->trashed() ? ' (Deleted)' : '');
                    })
                    ->wrapOptionLabels(false)
                    ->disableOptionWhen(function ($label, $value, $state, $component) {
                        $isDeleted = str_contains($label, ' (Deleted)');

                        $isDuplicate = false;

                        if ($component?->getParentRepeater()) {
                            $repeater = $component->getParentRepeater();

                            $isDuplicate = collect($repeater->getState())
                                ->pluck(
                                    (string) str($component->getStatePath())
                                        ->after("{$repeater->getStatePath()}.")
                                        ->after('.'),
                                )
                                ->flatten()
                                ->diff(Arr::wrap($state))
                                ->filter(fn (mixed $siblingItemState): bool => filled($siblingItemState))
                                ->contains($value);
                        }

                        return $isDeleted || $isDuplicate;
                    })
                    ->afterStateUpdated(function (Set $set, Get $get, ?string $state): void {
                        $product = Product::query()->find($state);

                        if (! $product) {
                            return;
                        }

                        $set('uom_id', $product->uom_id);
                        $set('company_id', $get('../../company_id'));
                    }),
                TextInput::make('quantity')
                    ->numeric()
                    ->minValue(0.0001)
                    ->default(1)
                    ->step('0.0001')
                    ->required(),
                Select::make('uom_id')
                    ->relationship(
                        'uom',
                        'name',
                        function (Builder $query, Get $get): Builder {
                            $product = Product::query()->withTrashed()->find($get('product_id'));
                            $categoryId = $product?->uom?->category_id;

                            return $query
                                ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
                                ->orderBy('name');
                        },
                    )
                    ->default(fn (Get $get): ?int => Product::query()->withTrashed()->find($get('product_id'))?->uom_id)
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('operation_id')
                    ->relationship('operation', 'name')
                    ->native(false)
                    ->searchable()
                    ->preload()
                    ->wrapOptionLabels(false)
                    ->createOptionForm(fn (Schema $schema): Schema => OperationResource::form($schema))
                    ->createOptionAction(fn (Action $action) => $action->modalWidth(Width::SevenExtraLarge)),
            ]);
    }

    protected static function getBillOfMaterialLabel(?BillOfMaterial $billOfMaterial): string
    {
        if (! $billOfMaterial) {
            return '—';
        }

        return filled($billOfMaterial->code)
            ? (string) $billOfMaterial->code
            : ($billOfMaterial->product?->name ?? '—');
    }

    protected static function getDefaultOperationRepeaterData(array $overrides = []): array
    {
        return static::normalizeOperationRepeaterData([
            'bill_of_material_id'        => null,
            'time_mode'                  => OperationTimeMode::MANUAL->value,
            'time_mode_batch'            => 10,
            'manual_cycle_time'          => '60:00',
            'worksheet_type'             => OperationWorksheetType::TEXT->value,
            'worksheet'                  => null,
            'worksheet_google_slide_url' => null,
            'note'                       => null,
            'company_id'                 => current_company_id(),
            'attributeValues'            => [],
            ...$overrides,
        ]);
    }

    protected static function normalizeOperationRepeaterData(array $data): array
    {
        $data['company_id'] ??= current_company_id();
        $data['attributeValues'] = array_values($data['attributeValues'] ?? []);
        $data['time_mode'] ??= OperationTimeMode::MANUAL->value;
        $data['time_mode_batch'] ??= 10;
        $data['manual_cycle_time'] ??= '60';

        return $data;
    }

    public static function matchesEnumState(mixed $state, BackedEnum $enum): bool
    {
        if ($state instanceof BackedEnum) {
            return $state->value === $enum->value;
        }

        return $state === $enum->value;
    }

    public static function getOperationSettings(): OperationSettings
    {
        return settings(OperationSettings::class);
    }
}
