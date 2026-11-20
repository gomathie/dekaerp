<?php

namespace Webkul\Manufacturing\Filament\Clusters\Operations\Resources;

use BackedEnum;
use Carbon\Carbon;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Field\Filament\Traits\HasCustomFields;
use Webkul\Manufacturing\Enums\WorkOrderState;
use Webkul\Manufacturing\Filament\Clusters\Operations;
use Webkul\Manufacturing\Filament\Clusters\Operations\Resources\WorkOrderResource\Pages\EditWorkOrder;
use Webkul\Manufacturing\Filament\Clusters\Operations\Resources\WorkOrderResource\Pages\ListWorkOrders;
use Webkul\Manufacturing\Filament\Clusters\Operations\Resources\WorkOrderResource\Pages\ViewWorkOrder;
use Webkul\Manufacturing\Filament\Clusters\Operations\Resources\WorkOrderResource\Schemas\WorkOrderForm;
use Webkul\Manufacturing\Filament\Clusters\Operations\Resources\WorkOrderResource\Schemas\WorkOrderInfolist;
use Webkul\Manufacturing\Filament\Clusters\Operations\Resources\WorkOrderResource\Tables\WorkOrdersTable;
use Webkul\Manufacturing\Models\Operation;
use Webkul\Manufacturing\Models\Order;
use Webkul\Manufacturing\Models\Product;
use Webkul\Manufacturing\Models\WorkCenterProductivityLog;
use Webkul\Manufacturing\Models\WorkCenterProductivityLoss;
use Webkul\Manufacturing\Models\WorkOrder;
use Webkul\Manufacturing\Settings\OperationSettings;
use Webkul\Support\Filament\Forms\Components\Repeater;
use Webkul\Support\Filament\Forms\Components\Repeater\TableColumn as RepeaterTableColumn;
use Webkul\Support\Models\UOM;

class WorkOrderResource extends Resource
{
    use HasCustomFields;

    protected static ?string $model = WorkOrder::class;

    protected static ?string $cluster = Operations::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 2;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-wrench-screwdriver';

    public static function isDiscovered(): bool
    {
        if (app()->runningInConsole()) {
            return true;
        }

        return settings(OperationSettings::class)->enable_work_orders;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereHas('manufacturingOrder');
    }

    public static function getModelLabel(): string
    {
        return __('manufacturing::models/work-order.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('manufacturing::filament/clusters/operations/resources/work-order.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('manufacturing::filament/clusters/operations/resources/work-order.navigation.group');
    }

    public static function getSubNavigationPosition(): SubNavigationPosition
    {
        return SubNavigationPosition::Start;
    }

    public static function form(Schema $schema): Schema
    {
        return WorkOrderForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WorkOrdersTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return WorkOrderInfolist::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWorkOrders::route('/'),
            'view'  => ViewWorkOrder::route('/{record}'),
            'edit'  => EditWorkOrder::route('/{record}/edit'),
        ];
    }

    public static function getTimeTrackingRepeater(): Repeater
    {
        return Repeater::make('productivityLogs')
            ->relationship('productivityLogs')
            ->hiddenLabel()
            ->defaultItems(0)
            ->addActionLabel(__('manufacturing::filament/clusters/operations/resources/work-order.form.tabs.time-tracking.add-action'))
            ->reorderable(false)
            ->compact()
            ->table([
                RepeaterTableColumn::make('assigned_user_id')
                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.form.tabs.time-tracking.columns.user')),
                RepeaterTableColumn::make('duration')
                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.form.tabs.time-tracking.columns.duration')),
                RepeaterTableColumn::make('started_at')
                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.form.tabs.time-tracking.columns.start-date')),
                RepeaterTableColumn::make('finished_at')
                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.form.tabs.time-tracking.columns.end-date')),
                RepeaterTableColumn::make('loss_id')
                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.form.tabs.time-tracking.columns.productivity')),
            ])
            ->schema([
                Hidden::make('work_center_id')
                    ->default(fn (?WorkOrder $record): ?int => $record?->work_center_id),
                Hidden::make('company_id')
                    ->default(fn (?WorkOrder $record): ?int => $record?->manufacturingOrder?->company_id),
                Select::make('assigned_user_id')
                    ->hiddenLabel()
                    ->relationship('assignedUser', 'name')
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->wrapOptionLabels(false)
                    ->default(fn (): ?int => auth()->id())
                    ->required(),
                TextInput::make('duration')
                    ->hiddenLabel()
                    ->default('00:00')
                    ->live()
                    ->rule('regex:/^\d+:\d{2}$/')
                    ->placeholder('00:00')
                    ->afterStateHydrated(function (TextInput $component, mixed $state, ?WorkCenterProductivityLog $record): void {
                        if ($record) {
                            $component->state(format_float_time((float) ($state ?: 0), 'minutes'));
                        }
                    })
                    ->afterStateUpdated(function (Set $set, Get $get, ?string $state, ?WorkCenterProductivityLog $record): void {
                        if ($record) {
                            return;
                        }

                        static::syncProductivityLogDatesFromDuration($set, $get, $state);
                    })
                    ->dehydrateStateUsing(fn (?string $state, ?WorkCenterProductivityLog $record): float => $record ? (float) ($record->duration ?: 0) : parse_float_time($state, 'minutes'))
                    ->disabled(fn (?WorkCenterProductivityLog $record): bool => (bool) $record)
                    ->required(),
                DateTimePicker::make('started_at')
                    ->hiddenLabel()
                    ->live()
                    ->native(false)
                    ->seconds(false)
                    ->default(fn (): Carbon => now())
                    ->afterStateUpdated(function (Set $set, Get $get, mixed $state, ?WorkCenterProductivityLog $record): void {
                        if ($record) {
                            return;
                        }

                        static::syncProductivityLogDurationFromDates($set, $get, $state, $get('finished_at'));
                    })
                    ->required(),
                DateTimePicker::make('finished_at')
                    ->hiddenLabel()
                    ->live()
                    ->native(false)
                    ->seconds(false)
                    ->default(fn (): Carbon => now())
                    ->afterStateUpdated(function (Set $set, Get $get, mixed $state, ?WorkCenterProductivityLog $record): void {
                        if ($record) {
                            return;
                        }

                        static::syncProductivityLogDurationFromDates($set, $get, $get('started_at'), $state);
                    }),
                Select::make('loss_id')
                    ->hiddenLabel()
                    ->relationship('loss', 'name')
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->wrapOptionLabels(false)
                    ->default(fn (): ?int => static::getDefaultProductivityLossId())
                    ->required(),
            ]);
    }

    protected static function getDefaultProductivityLossId(): ?int
    {
        return WorkCenterProductivityLoss::query()
            ->where('loss_type', 'productive')
            ->value('id');
    }

    public static function getVisibleWorkOrderStateOptions(?string $currentState): array
    {
        $options = WorkOrderState::options();

        if ($currentState === WorkOrderState::CANCEL->value) {
            unset($options[WorkOrderState::DONE->value]);

            return $options;
        }

        unset($options[WorkOrderState::CANCEL->value]);

        return $options;
    }

    protected static function syncProductivityLogDatesFromDuration(Set $set, Get $get, ?string $durationState): void
    {
        $startedAt = static::parseProductivityLogDateState($get('started_at')) ?? now();
        $duration = (float) parse_float_time($durationState ?: '00:00', 'minutes');

        $set('started_at', $startedAt->format('Y-m-d H:i:s'));
        $set('finished_at', $startedAt->copy()->addSeconds((int) round($duration * 60))->format('Y-m-d H:i:s'));
    }

    protected static function syncProductivityLogDurationFromDates(Set $set, Get $get, mixed $startedAtState, mixed $finishedAtState): void
    {
        $startedAt = static::parseProductivityLogDateState($startedAtState);
        $finishedAt = static::parseProductivityLogDateState($finishedAtState);

        if (! $startedAt && ! $finishedAt) {
            return;
        }

        if (! $startedAt) {
            $startedAt = $finishedAt;
            $set('started_at', $startedAt?->format('Y-m-d H:i:s'));
        }

        if (! $finishedAt) {
            $finishedAt = $startedAt;
            $set('finished_at', $finishedAt?->format('Y-m-d H:i:s'));
        }

        $durationInSeconds = max(0, $startedAt->diffInSeconds($finishedAt, false));
        $set('duration', format_float_time($durationInSeconds / 60, 'minutes'));
    }

    protected static function parseProductivityLogDateState(mixed $state): ?Carbon
    {
        if ($state instanceof Carbon) {
            return $state;
        }

        if (blank($state)) {
            return null;
        }

        return Carbon::parse($state);
    }

    public static function getComponentsRepeater(): Repeater
    {
        return Repeater::make('rawMaterialMoves')
            ->relationship('rawMaterialMoves')
            ->hiddenLabel()
            ->defaultItems(0)
            ->addable(false)
            ->deletable(false)
            ->reorderable(false)
            ->compact()
            ->table([
                RepeaterTableColumn::make('rendered_display_product')
                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.form.tabs.components.columns.product')),
                RepeaterTableColumn::make('rendered_to_consume')
                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.form.tabs.components.columns.to-consume')),
                RepeaterTableColumn::make('rendered_quantity')
                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.form.tabs.components.columns.quantity')),
                RepeaterTableColumn::make('rendered_uom')
                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.form.tabs.components.columns.uom')),
            ])
            ->schema([
                Hidden::make('work_order_id')
                    ->default(fn (?WorkOrder $record): ?int => $record?->id),
                Hidden::make('product_id'),
                Hidden::make('source_location_id'),
                Hidden::make('uom_id'),
                Hidden::make('product_uom_qty'),
                Hidden::make('quantity'),
                TextEntry::make('rendered_display_product')
                    ->hiddenLabel()
                    ->state(fn (Get $get): string => Product::query()->withTrashed()->find($get('product_id'))?->name ?? '—'),
                TextEntry::make('rendered_to_consume')
                    ->hiddenLabel()
                    ->state(fn (Get $get): string => number_format((float) ($get('product_uom_qty') ?: 0), 2)),
                TextEntry::make('rendered_quantity')
                    ->hiddenLabel()
                    ->state(fn (Get $get): string => number_format((float) ($get('quantity') ?: 0), 2)),
                TextEntry::make('rendered_uom')
                    ->hiddenLabel()
                    ->state(fn (Get $get): string => UOM::query()->find($get('uom_id'))?->name ?? '—'),
            ]);
    }

    public static function syncExpectedDuration(Set $set, Get $get, ?Operation $operation): void
    {
        if (! $operation) {
            return;
        }

        $manufacturingOrder = Order::query()->with(['product', 'uom'])->find($get('manufacturing_order_id'));

        if (! $manufacturingOrder?->product) {
            return;
        }

        $quantity = (float) ($manufacturingOrder->quantity_producing ?: $manufacturingOrder->quantity ?: 0);

        $set('expected_duration', format_float_time($operation->getExpectedDuration($manufacturingOrder->product, $quantity), 'minutes'));
    }

    public static function getManufacturingOrderLabel(?Order $order): string
    {
        if (! $order) {
            return '—';
        }

        return $order->reference ?: $order->name ?: 'MO-'.$order->getKey();
    }
}
