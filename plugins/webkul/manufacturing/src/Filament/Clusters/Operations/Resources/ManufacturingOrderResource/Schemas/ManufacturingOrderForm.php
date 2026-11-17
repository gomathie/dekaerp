<?php

namespace Webkul\Manufacturing\Filament\Clusters\Operations\Resources\ManufacturingOrderResource\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Webkul\Field\Filament\Forms\Components\ProgressStepper as FormProgressStepper;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\OperationType;
use Webkul\Manufacturing\Enums\ManufacturingOrderState;
use Webkul\Manufacturing\Filament\Clusters\Operations\Resources\ManufacturingOrderResource;
use Webkul\Manufacturing\Models\BillOfMaterial;
use Webkul\Manufacturing\Models\Order;
use Webkul\Manufacturing\Models\Product;
use Webkul\Product\Enums\ProductType;

class ManufacturingOrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FormProgressStepper::make('state')
                    ->hiddenLabel()
                    ->inline()
                    ->options(ManufacturingOrderState::options())
                    ->options(function (?Order $record, Get $get): array {
                        $options = ManufacturingOrderState::options();
                        $currentState = $get('state');

                        if ($currentState && ! $currentState instanceof ManufacturingOrderState) {
                            $currentState = ManufacturingOrderState::from($currentState);
                        }

                        $currentState ??= $record?->state;

                        if (! $record) {
                            unset(
                                $options[ManufacturingOrderState::PROGRESS->value],
                                $options[ManufacturingOrderState::TO_CLOSE->value],
                                $options[ManufacturingOrderState::CANCEL->value],
                            );
                        } else {
                            foreach ([ManufacturingOrderState::PROGRESS, ManufacturingOrderState::TO_CLOSE, ManufacturingOrderState::CANCEL] as $state) {
                                if ($currentState !== $state) {
                                    unset($options[$state->value]);
                                }
                            }

                            if ($currentState) {
                                $options[$currentState->value] = $currentState->getLabel();
                            }
                        }

                        return $options;
                    })
                    ->default(ManufacturingOrderState::DRAFT)
                    ->disabled()
                    ->dehydrated(),

                Section::make(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.sections.general.title'))
                    ->columns(2)
                    ->schema(fn ($record) => [
                        Group::make()
                            ->columns(1)
                            ->schema([
                                Select::make('product_id')
                                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.sections.general.fields.product'))
                                    ->relationship(
                                        'product',
                                        'name',
                                        fn (Builder $query, Get $get) => $query
                                            ->withTrashed()
                                            ->where('type', ProductType::GOODS)
                                            ->whereNull('is_configurable')
                                            ->where(owned_by_company($get('company_id')))
                                    )
                                    ->getOptionLabelFromRecordUsing(fn (Product $record): string => $record->name)
                                    ->searchable()
                                    ->preload()
                                    ->native(false)
                                    ->wrapOptionLabels(false)
                                    ->live()
                                    ->disabled(fn (?Order $record) => $record && $record->state !== ManufacturingOrderState::DRAFT)
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
                                        $product = Product::query()->withTrashed()->find($state);

                                        if (! $product) {
                                            $set('uom_id', null);
                                            $set('bill_of_material_id', null);
                                            $set('rawMaterialMoves', []);
                                            $set('workOrders', []);

                                            return;
                                        }

                                        $set('uom_id', $product->uom_id ?: ManufacturingOrderResource::getDefaultUomId());

                                        $set('company_id', $product->company_id ?? current_company_id());

                                        $billOfMaterialId = ManufacturingOrderResource::getDefaultBillOfMaterialId($product);

                                        if (! $billOfMaterialId) {
                                            $set('bill_of_material_id', null);
                                            $set('rawMaterialMoves', []);
                                            $set('workOrders', []);

                                            return;
                                        }

                                        if ($get('bill_of_material_id') !== $billOfMaterialId) {
                                            $set('bill_of_material_id', $billOfMaterialId);
                                        }

                                        ManufacturingOrderResource::applyBillOfMaterialDefaults(
                                            $set,
                                            BillOfMaterial::query()->withTrashed()->find($billOfMaterialId),
                                            $product,
                                            (float) ($get('quantity') ?: 1),
                                            $get('uom_id'),
                                        );
                                    })
                                    ->required(),
                                ManufacturingOrderResource::getQuantityUomField(),
                                Select::make('bill_of_material_id')
                                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.sections.general.fields.bill-of-material'))
                                    ->relationship(
                                        'billOfMaterial',
                                        'code',
                                        modifyQueryUsing: function (Get $get, Builder $query): void {
                                            $product = Product::query()->withTrashed()->find($get('product_id'));

                                            if (! $product) {
                                                $query->whereRaw('1 = 0');

                                                return;
                                            }

                                            $productIds = array_filter([$product->id, $product->parent_id]);

                                            $query->withTrashed()
                                                ->whereIn('product_id', $productIds)
                                                ->where(owned_by_company($get('company_id')));
                                        }
                                    )
                                    ->getOptionLabelFromRecordUsing(fn (BillOfMaterial $record): string => ManufacturingOrderResource::getBillOfMaterialLabel($record))
                                    ->searchable()
                                    ->preload()
                                    ->native(false)
                                    ->wrapOptionLabels(false)
                                    ->live()
                                    ->disabled(fn (?Order $record) => $record && $record->state !== ManufacturingOrderState::DRAFT)
                                    ->afterStateUpdated(function (Set $set, Get $get, ?string $state): void {
                                        $product = Product::query()->withTrashed()->find($get('product_id'));

                                        ManufacturingOrderResource::applyBillOfMaterialDefaults(
                                            $set,
                                            BillOfMaterial::query()->withTrashed()->find($state),
                                            $product,
                                            (float) ($get('quantity') ?: 1),
                                            $get('uom_id'),
                                        );
                                    }),
                            ]),
                        Group::make()
                            ->columns(1)
                            ->schema([
                                DateTimePicker::make('started_at')
                                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.sections.general.fields.scheduled-date'))
                                    ->native(false)
                                    ->default(now())
                                    ->seconds(false)
                                    ->required()
                                    ->disabled(fn (?Order $record) => $record && in_array($record->state, [ManufacturingOrderState::DONE, ManufacturingOrderState::CANCEL])),
                                DateTimePicker::make('finished_at')
                                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.sections.general.fields.scheduled-end'))
                                    ->default(now())
                                    ->seconds(false)
                                    ->disabled()
                                    ->visible(fn (?Order $record) => $record && $record->state !== ManufacturingOrderState::DRAFT),
                                Select::make('assigned_user_id')
                                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.sections.general.fields.responsible'))
                                    ->relationship('assignedUser', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->native(false)
                                    ->default(Auth::id())
                                    ->disabled(fn (?Order $record) => $record && in_array($record->state, [ManufacturingOrderState::DONE, ManufacturingOrderState::CANCEL])),
                            ]),
                    ]),

                Tabs::make('manufacturing-order-tabs')
                    ->tabs([
                        Tab::make(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.components.title'))
                            ->schema([
                                ManufacturingOrderResource::getComponentsRepeater(),
                            ]),
                        Tab::make(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.work-orders.title'))
                            ->visible(ManufacturingOrderResource::getOperationSettings()->enable_work_orders)
                            ->schema([
                                ManufacturingOrderResource::getWorkOrdersRepeater(),
                            ]),
                        Tab::make(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.by-products.title'))
                            ->hidden(! ManufacturingOrderResource::getOperationSettings()->enable_byproducts)
                            ->schema([
                                TextEntry::make('by_products_process_note')
                                    ->hiddenLabel()
                                    ->state(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.by-products.process-note')),
                            ]),
                        Tab::make(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.miscellaneous.title'))
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        Select::make('operation_type_id')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.miscellaneous.fields.operation-type'))
                                            ->relationship(
                                                'operationType',
                                                'name',
                                                fn (Builder $query, Get $get) => $query
                                                    ->withTrashed()
                                                    ->where('type', 'manufacture')
                                                    ->where(owned_by_company($get('company_id')))
                                            )
                                            ->getOptionLabelFromRecordUsing(fn (OperationType $record): string => ManufacturingOrderResource::getOperationTypeLabel($record))
                                            ->searchable()
                                            ->preload()
                                            ->native(false)
                                            ->wrapOptionLabels(false)
                                            ->live()
                                            ->required()
                                            ->disabled(fn (?Order $record) => $record && $record->state !== ManufacturingOrderState::DRAFT)
                                            ->afterStateUpdated(function (Set $set, Get $get, ?string $state): void {
                                                $operationType = OperationType::query()->withTrashed()->find($state);

                                                $sourceLocationId = $operationType?->source_location_id;
                                                $set('source_location_id', $sourceLocationId);
                                                $set('destination_location_id', $operationType?->destination_location_id);

                                                $sourceLocation = $sourceLocationId ? Location::query()->withTrashed()->find($sourceLocationId) : null;
                                                $displayFrom = $sourceLocation?->full_name ?? '—';

                                                foreach (array_keys($get('rawMaterialMoves') ?? []) as $key) {
                                                    $set("rawMaterialMoves.{$key}.operation_type_id", $operationType?->id);
                                                    $set("rawMaterialMoves.{$key}.source_location_id", $sourceLocationId);
                                                    $set("rawMaterialMoves.{$key}.display_from", $displayFrom);
                                                }
                                            }),
                                        Select::make('source_location_id')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.miscellaneous.fields.source'))
                                            ->relationship('sourceLocation', 'full_name', fn (Builder $query, Get $get) => $query
                                                ->withTrashed()
                                                ->where(owned_by_company($get('company_id'))))
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->native(false)
                                            ->disabled(fn (?Order $record) => $record && $record->state !== ManufacturingOrderState::DRAFT)
                                            ->visible(ManufacturingOrderResource::getWarehouseSettings()->enable_locations)
                                            ->wrapOptionLabels(false)
                                            ->live()
                                            ->afterStateUpdated(function (Set $set, Get $get, ?string $state): void {
                                                $sourceLocation = $state ? Location::query()->withTrashed()->find($state) : null;
                                                $displayFrom = $sourceLocation?->full_name ?? '—';

                                                foreach (array_keys($get('rawMaterialMoves') ?? []) as $key) {
                                                    $set("rawMaterialMoves.{$key}.source_location_id", $state);
                                                    $set("rawMaterialMoves.{$key}.display_from", $displayFrom);
                                                }
                                            }),
                                        Select::make('destination_location_id')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.miscellaneous.fields.finished-products-location'))
                                            ->relationship('destinationLocation', 'full_name', fn (Builder $query, Get $get) => $query
                                                ->withTrashed()
                                                ->where(owned_by_company($get('company_id'))))
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->native(false)
                                            ->wrapOptionLabels(false)
                                            ->live()
                                            ->disabled(fn (?Order $record) => $record && $record->state !== ManufacturingOrderState::DRAFT)
                                            ->visible(ManufacturingOrderResource::getWarehouseSettings()->enable_locations)
                                            ->afterStateUpdated(function (Set $set, ?string $state): void {
                                                $set('destination_location_id', $state);
                                            }),
                                        Select::make('company_id')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.miscellaneous.fields.company'))
                                            ->relationship('company', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->native(false)
                                            ->disabled(fn (?Order $record) => $record && $record->state !== ManufacturingOrderState::DRAFT)
                                            ->default(current_company_id())
                                            ->live()
                                            ->afterStateUpdated(fn (Set $set, Get $get, $state) => clear_foreign_company_values($set, $get, [
                                                'product_id'              => Product::class,
                                                'bill_of_material_id'     => BillOfMaterial::class,
                                                'operation_type_id'       => OperationType::class,
                                                'source_location_id'      => Location::class,
                                                'destination_location_id' => Location::class,
                                            ], $state)),
                                        ...ManufacturingOrderResource::getCustomFormFields(),
                                    ]),
                            ]),
                    ]),

                Hidden::make('destination_location_id'),
            ])
            ->columns(1);
    }
}
