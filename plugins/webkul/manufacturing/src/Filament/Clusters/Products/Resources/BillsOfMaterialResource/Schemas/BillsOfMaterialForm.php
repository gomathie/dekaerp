<?php

namespace Webkul\Manufacturing\Filament\Clusters\Products\Resources\BillsOfMaterialResource\Schemas;

use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\FusedGroup;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Webkul\Inventory\Enums\OperationType;
use Webkul\Inventory\Models\OperationType as OperationTypeModel;
use Webkul\Manufacturing\Enums\BillOfMaterialConsumption;
use Webkul\Manufacturing\Enums\BillOfMaterialReadyToProduce;
use Webkul\Manufacturing\Enums\BillOfMaterialType;
use Webkul\Manufacturing\Filament\Clusters\Products\Resources\BillsOfMaterialResource;
use Webkul\Manufacturing\Models\Product;

class BillsOfMaterialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.sections.general.title'))
                            ->schema([
                                Select::make('product_id')
                                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.sections.general.fields.product'))
                                    ->relationship('product', 'name', fn (Builder $query, Get $get) => $query
                                        ->withTrashed()
                                        ->whereNull('parent_id')
                                        ->where(owned_by_company($get('company_id'))))
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
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateHydrated(function (Set $set, ?string $state): void {
                                        $product = Product::query()->find($state);

                                        if (! $product?->parent_id) {
                                            return;
                                        }

                                        $set('product_variant_id', $product->id);
                                        $set('product_id', $product->parent_id);
                                    })
                                    ->afterStateUpdated(function (Set $set, ?string $state): void {
                                        $product = Product::query()->find($state);

                                        if (! $product) {
                                            return;
                                        }

                                        $set('product_variant_id', null);
                                        $set('uom_id', $product->uom_id);
                                        $set('company_id', $product->company_id);
                                    })
                                    ->required()
                                    ->columnSpanFull(),
                                Select::make('product_variant_id')
                                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.sections.general.fields.product-variant'))
                                    ->options(fn (Get $get): array => Product::query()
                                        ->where('parent_id', $get('product_id'))
                                        ->orderBy('name')
                                        ->pluck('name', 'id')
                                        ->all())
                                    ->native(false)
                                    ->live()
                                    ->dehydrated(false)
                                    ->afterStateUpdated(function (Set $set, ?string $state): void {
                                        $variant = Product::query()->find($state);

                                        if (! $variant) {
                                            return;
                                        }

                                        $set('uom_id', $variant->uom_id);
                                        $set('company_id', $variant->company_id);
                                    })
                                    ->columnSpanFull(),
                                FusedGroup::make([
                                    TextInput::make('quantity')
                                        ->numeric()
                                        ->minValue(0.0001)
                                        ->default(1)
                                        ->step('0.0001')
                                        ->required()
                                        ->columnSpan(2),
                                    Select::make('uom_id')
                                        ->placeholder(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.sections.general.fields.uom'))
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
                                ])
                                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.sections.general.fields.quantity'))
                                    ->columns(3),
                                TextInput::make('code')
                                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.sections.general.fields.reference'))
                                    ->maxLength(255)
                                    ->placeholder(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.sections.general.fields.reference-placeholder')),
                                Radio::make('type')
                                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.sections.general.fields.type'))
                                    ->hidden()
                                    ->options(BillOfMaterialType::class)
                                    ->default(BillOfMaterialType::NORMAL->value)
                                    ->live()
                                    ->inline(false)
                                    ->required(),
                                Select::make('company_id')
                                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.sections.general.fields.company'))
                                    ->relationship('company', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->default(current_company_id())
                                    ->live()
                                    ->afterStateUpdated(fn (Set $set, Get $get, $state) => clear_foreign_company_values($set, $get, [
                                        'product_id'        => Product::class,
                                        'operation_type_id' => OperationTypeModel::class,
                                    ], $state))
                                    ->required(),
                            ])
                            ->columns(2),

                        Tabs::make('bom-tabs')
                            ->tabs([
                                Tab::make(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.components.title'))
                                    ->schema([
                                        BillsOfMaterialResource::getComponentsRepeater(),
                                    ]),

                                Tab::make(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.operations.title'))
                                    ->visible(BillsOfMaterialResource::getOperationSettings()->enable_work_orders)
                                    ->schema([
                                        BillsOfMaterialResource::getOperationsRepeater(),
                                    ]),

                                Tab::make(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.by-products.title'))
                                    ->hidden(! BillsOfMaterialResource::getOperationSettings()->enable_byproducts)
                                    ->schema([
                                        BillsOfMaterialResource::getByProductsRepeater(),
                                    ]),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.sections.miscellaneous.title'))
                            ->schema([
                                TextEntry::make('kit_information')
                                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.sections.miscellaneous.fields.kit-information'))
                                    ->state(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.sections.miscellaneous.fields.kit-information-content'))
                                    ->columnSpanFull()
                                    ->visible(fn (Get $get): bool => BillsOfMaterialResource::matchesEnumState($get('type'), BillOfMaterialType::PHANTOM)),
                                Radio::make('ready_to_produce')
                                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.miscellaneous.fields.ready-to-produce'))
                                    ->options(BillOfMaterialReadyToProduce::class)
                                    ->default(BillOfMaterialReadyToProduce::ALL_AVAILABLE->value)
                                    ->inline(false)
                                    ->visible(fn (Get $get): bool => ! BillsOfMaterialResource::matchesEnumState($get('type'), BillOfMaterialType::PHANTOM))
                                    ->required()
                                    ->columnSpanFull(),
                                Select::make('operation_type_id')
                                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.miscellaneous.fields.routing'))
                                    ->relationship('operationType', 'name', fn (Builder $query, Get $get) => $query
                                        ->withTrashed()
                                        ->where('type', OperationType::MANUFACTURE)
                                        ->where(owned_by_company($get('company_id'))))
                                    ->searchable()
                                    ->preload()
                                    ->native(false)
                                    ->wrapOptionLabels(false)
                                    ->getOptionLabelFromRecordUsing(fn ($record): string => $record->warehouse
                                        ? $record->warehouse->name.': '.$record->name
                                        : $record->name)
                                    ->visible(fn (Get $get): bool => ! BillsOfMaterialResource::matchesEnumState($get('type'), BillOfMaterialType::PHANTOM))
                                    ->columnSpanFull(),
                                Select::make('consumption')
                                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.miscellaneous.fields.consumption'))
                                    ->options(BillOfMaterialConsumption::class)
                                    ->native(false)
                                    ->default(BillOfMaterialConsumption::WARNING->value)
                                    ->visible(fn (Get $get): bool => ! BillsOfMaterialResource::matchesEnumState($get('type'), BillOfMaterialType::PHANTOM))
                                    ->required()
                                    ->columnSpanFull(),
                                Toggle::make('allow_operation_dependencies')
                                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.miscellaneous.fields.operation-dependencies'))
                                    ->default(false)
                                    ->inline(false)
                                    ->columnSpanFull(),
                                TextInput::make('produce_delay')
                                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.sections.miscellaneous.fields.manufacturing-lead-time'))
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->suffix(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.sections.miscellaneous.fields.days-suffix'))
                                    ->required()
                                    ->columnSpanFull(),
                                TextInput::make('days_to_prepare_mo')
                                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.sections.miscellaneous.fields.days-to-prepare-manufacturing-order'))
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->suffix(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.sections.miscellaneous.fields.days-suffix'))
                                    ->required()
                                    ->columnSpanFull(),
                            ])
                            ->columns(1),
                    ])
                    ->columnSpan(['lg' => 1]),

            ])
            ->columns(3);
    }
}
