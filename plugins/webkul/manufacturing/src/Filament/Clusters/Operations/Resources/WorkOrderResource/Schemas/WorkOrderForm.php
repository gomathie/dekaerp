<?php

namespace Webkul\Manufacturing\Filament\Clusters\Operations\Resources\WorkOrderResource\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Field\Filament\Forms\Components\ProgressStepper as FormProgressStepper;
use Webkul\Manufacturing\Enums\WorkOrderState;
use Webkul\Manufacturing\Filament\Clusters\Configurations\Resources\OperationResource;
use Webkul\Manufacturing\Filament\Clusters\Operations\Resources\WorkOrderResource;
use Webkul\Manufacturing\Models\Operation;
use Webkul\Manufacturing\Models\Order;
use Webkul\Manufacturing\Models\WorkOrder;

class WorkOrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FormProgressStepper::make('state')
                    ->hiddenLabel()
                    ->inline()
                    ->options(fn (?WorkOrder $record): array => WorkOrderResource::getVisibleWorkOrderStateOptions($record?->state?->value ?? $record?->state))
                    ->default(WorkOrderState::PENDING)
                    ->disabled()
                    ->dehydrated(),

                Section::make(__('manufacturing::filament/clusters/operations/resources/work-order.form.sections.general.title'))
                    ->columns(2)
                    ->schema([
                        Group::make()
                            ->columns(1)
                            ->schema([
                                Select::make('operation_id')
                                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.form.sections.general.fields.work-order'))
                                    ->relationship('operation', 'name', fn (Builder $query) => $query->withTrashed())
                                    ->searchable()
                                    ->preload()
                                    ->native(false)
                                    ->wrapOptionLabels(false)
                                    ->createOptionForm(fn (Schema $schema): Schema => OperationResource::form($schema->model(Operation::class)))
                                    ->createOptionAction(fn (Action $action) => $action->modalWidth(Width::SevenExtraLarge))
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, Get $get, ?string $state): void {
                                        $operation = Operation::query()->withTrashed()->find($state);

                                        $set('work_center_id', $operation?->work_center_id);
                                        $set('name', $operation?->name);

                                        WorkOrderResource::syncExpectedDuration($set, $get, $operation);
                                    })
                                    ->required()
                                    ->disabled(fn (?WorkOrder $record): bool => $record && ! in_array($record->state, [WorkOrderState::PENDING, WorkOrderState::WAITING], true)),
                                Select::make('work_center_id')
                                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.form.sections.general.fields.work-center'))
                                    ->relationship('workCenter', 'name', fn (Builder $query) => $query->withTrashed())
                                    ->searchable()
                                    ->preload()
                                    ->native(false)
                                    ->wrapOptionLabels(false)
                                    ->required()
                                    ->disabled(fn (?WorkOrder $record): bool => $record && ! in_array($record->state, [WorkOrderState::PENDING, WorkOrderState::WAITING], true)),
                                Group::make()
                                    ->columns(2)
                                    ->schema([
                                        DateTimePicker::make('started_at')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/work-order.form.sections.general.fields.start-date'))
                                            ->native(false)
                                            ->seconds(false)
                                            ->disabled(fn (?WorkOrder $record): bool => $record && in_array($record->state, [WorkOrderState::DONE, WorkOrderState::CANCEL], true)),
                                        DateTimePicker::make('finished_at')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/work-order.form.sections.general.fields.end-date'))
                                            ->native(false)
                                            ->seconds(false)
                                            ->disabled(fn (?WorkOrder $record): bool => $record && in_array($record->state, [WorkOrderState::DONE, WorkOrderState::CANCEL], true)),
                                    ]),
                                TextInput::make('expected_duration')
                                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.form.sections.general.fields.expected-duration'))
                                    ->default('00:00')
                                    ->rule('regex:/^\d+:\d{2}$/')
                                    ->placeholder('00:00')
                                    ->afterStateHydrated(function (TextInput $component, mixed $state): void {
                                        $component->state(format_float_time((float) ($state ?: 0), 'minutes'));
                                    })
                                    ->dehydrateStateUsing(fn (?string $state): float => parse_float_time($state, 'minutes'))
                                    ->required()
                                    ->suffix(__('manufacturing::filament/clusters/operations/resources/work-order.form.sections.general.fields.duration-suffix'))
                                    ->disabled(fn (?WorkOrder $record): bool => $record && in_array($record->state, [WorkOrderState::DONE, WorkOrderState::CANCEL], true)),
                            ]),
                        Group::make()
                            ->columns(1)
                            ->schema([
                                TextInput::make('display_product')
                                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.form.sections.general.fields.product'))
                                    ->afterStateHydrated(function (TextInput $component, mixed $state, ?WorkOrder $record): void {
                                        $component->state($record?->product?->name ?? $state ?? '—');
                                    })
                                    ->disabled()
                                    ->dehydrated(false),
                                TextInput::make('display_quantity')
                                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.form.sections.general.fields.quantity'))
                                    ->afterStateHydrated(function (TextInput $component, mixed $state, ?WorkOrder $record): void {
                                        $component->state(number_format((float) ($record?->quantity_remaining ?? $state ?? 0), 2));
                                    })
                                    ->disabled()
                                    ->dehydrated(false),
                                TextInput::make('display_lot_serial')
                                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.form.sections.general.fields.lot-serial'))
                                    ->afterStateHydrated(function (TextInput $component, mixed $state, ?WorkOrder $record): void {
                                        $component->state($record?->manufacturingOrder?->producingLot?->name ?? $state ?? '—');
                                    })
                                    ->disabled()
                                    ->dehydrated(false),
                                Select::make('manufacturing_order_id')
                                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.form.sections.general.fields.manufacturing-order'))
                                    ->relationship('manufacturingOrder', 'name')
                                    ->getOptionLabelFromRecordUsing(fn (Order $record): string => WorkOrderResource::getManufacturingOrderLabel($record))
                                    ->searchable()
                                    ->preload()
                                    ->native(false)
                                    ->wrapOptionLabels(false)
                                    ->live()
                                    ->required()
                                    ->disabled(fn (?WorkOrder $record): bool => (bool) $record)
                                    ->afterStateUpdated(function (Set $set, Get $get, ?string $state): void {
                                        $manufacturingOrder = Order::query()->with(['product', 'uom', 'producingLot'])->find($state);

                                        $set('product_id', $manufacturingOrder?->product_id);
                                        $set('uom_id', $manufacturingOrder?->uom_id);

                                        if ($manufacturingOrder?->product) {
                                            $set('display_product', $manufacturingOrder->product->name);
                                        }

                                        $set('display_quantity', (float) ($manufacturingOrder?->quantity_producing ?: $manufacturingOrder?->quantity ?: 0));

                                        WorkOrderResource::syncExpectedDuration(
                                            $set,
                                            $get,
                                            Operation::query()->withTrashed()->find($get('operation_id'))
                                        );
                                    }),
                            ]),
                    ]),

                Tabs::make('work-order-tabs')
                    ->tabs([
                        Tab::make(__('manufacturing::filament/clusters/operations/resources/work-order.form.tabs.time-tracking.title'))
                            ->schema([
                                WorkOrderResource::getTimeTrackingRepeater(),
                                TextEntry::make('display_total_real_duration')
                                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.form.tabs.time-tracking.footer.real-duration'))
                                    ->state(fn (?WorkOrder $record): string => format_float_time((float) ($record?->duration ?: 0), 'minutes').' (minutes)'),
                            ]),
                        Tab::make(__('manufacturing::filament/clusters/operations/resources/work-order.form.tabs.components.title'))
                            ->schema([
                                WorkOrderResource::getComponentsRepeater(),
                            ]),
                        Tab::make(__('manufacturing::filament/clusters/operations/resources/work-order.form.tabs.work-instruction.title'))
                            ->schema([
                                View::make('manufacturing::filament.clusters.operations.resources.work-order.work-instruction-preview')
                                    ->columnSpanFull(),
                            ]),
                        Tab::make(__('manufacturing::filament/clusters/operations/resources/work-order.form.tabs.blocked-by.title'))
                            ->schema([
                                Select::make('blockedByWorkOrders')
                                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.form.tabs.blocked-by.fields.work-orders'))
                                    ->relationship(
                                        'blockedByWorkOrders',
                                        'name',
                                        modifyQueryUsing: fn (Builder $query, ?WorkOrder $record) => $query
                                            ->when($record, fn (Builder $relationQuery) => $relationQuery->whereKeyNot($record->getKey()))
                                    )
                                    ->getOptionLabelFromRecordUsing(fn (WorkOrder $record): string => $record->display_name)
                                    ->searchable()
                                    ->preload()
                                    ->multiple()
                                    ->native(false)
                                    ->wrapOptionLabels(false)
                                    ->disabled(fn (?WorkOrder $record): bool => $record && ! in_array($record->state, [WorkOrderState::PENDING, WorkOrderState::WAITING], true)),
                            ]),
                    ]),

                Hidden::make('name'),
                Hidden::make('product_id'),
                Hidden::make('uom_id'),
                Hidden::make('display_quantity'),

                Section::make()
                    ->schema(WorkOrderResource::getCustomFormFields())
                    ->columns(2),
            ])
            ->columns(1);
    }
}
