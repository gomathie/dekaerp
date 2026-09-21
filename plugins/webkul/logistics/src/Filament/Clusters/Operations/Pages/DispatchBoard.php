<?php

namespace Webkul\Logistics\Filament\Clusters\Operations\Pages;

use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Logistics\Enums\ShipmentState;
use Webkul\Logistics\Models\Shipment;
use Webkul\Logistics\Support\LogisticsAccess;

/**
 * One screen for the dispatcher: what still needs a trip, what is moving, and
 * what is late.
 *
 * Deliberately a paginated table per tab rather than a drag-and-drop board.
 * A company with thousands of open shipments cannot render them all, and the
 * plan rules drag and drop out for this package.
 *
 * Rows are company-scoped by CompanyScope on the model; nothing here removes
 * that scope.
 */
class DispatchBoard extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-group';

    protected static ?int $navigationSort = 3;

    protected static ?string $cluster = \Webkul\Logistics\Filament\Clusters\Operations::class;

    protected static string $lang = 'logistics::filament/clusters/operations/pages/dispatch-board';

    protected string $view = 'logistics::filament.pages.dispatch-board';

    public ?string $activeTab = 'unassigned';

    public static function getNavigationLabel(): string
    {
        return __(static::$lang.'.title');
    }

    public function getTitle(): string
    {
        return __(static::$lang.'.title');
    }

    /**
     * Both permissions, not either.
     *
     * Shield generates `page_logistics_dispatch_board` automatically, and that
     * is what grants the screen. But the board renders shipment data, so a user
     * who may open the page still must be allowed to see shipments - otherwise
     * the page permission alone would leak customer names and destinations to
     * someone denied the shipment list. The per-company switch hides it like
     * every other Logistics screen.
     */
    public static function canAccess(): bool
    {
        $user = auth()->user();

        return ($user?->can('page_logistics_dispatch_board') ?? false)
            && ($user?->can('view_any_logistics_shipment') ?? false)
            && LogisticsAccess::enabledForCurrent();
    }

    public function getTabs(): array
    {
        return [
            'unassigned'      => __(static::$lang.'.tabs.unassigned'),
            'awaiting_pickup' => __(static::$lang.'.tabs.awaiting-pickup'),
            'active'          => __(static::$lang.'.tabs.active'),
            'due_today'       => __(static::$lang.'.tabs.due-today'),
            'overdue'         => __(static::$lang.'.tabs.overdue'),
            'failed'          => __(static::$lang.'.tabs.failed'),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => $this->tabQuery())
            ->columns([
                TextColumn::make('name')
                    ->label(__(static::$lang.'.columns.number'))
                    ->searchable(),

                TextColumn::make('state')
                    ->label(__(static::$lang.'.columns.state'))
                    ->badge(),

                TextColumn::make('customer.name')
                    ->label(__(static::$lang.'.columns.customer'))
                    ->placeholder('—')
                    ->visibleFrom('sm'),

                TextColumn::make('destination_label')
                    ->label(__(static::$lang.'.columns.destination'))
                    ->placeholder('—')
                    ->visibleFrom('md'),

                TextColumn::make('expected_delivery_at')
                    ->label(__(static::$lang.'.columns.expected-delivery-at'))
                    ->dateTime()
                    ->sortable()
                    ->placeholder('—')
                    ->visibleFrom('lg'),
            ])
            ->defaultPaginationPageOption(25)
            ->defaultSort('expected_delivery_at');
    }

    /**
     * Eager loads the relations the columns render, so the board costs a fixed
     * number of queries rather than one per row.
     */
    protected function tabQuery(): Builder
    {
        $query = Shipment::query()->with(['customer:id,name']);

        return match ($this->activeTab) {
            'awaiting_pickup' => $query->where('state', ShipmentState::AWAITING_PICKUP),

            'active' => $query->whereIn('state', [
                ShipmentState::PICKED_UP,
                ShipmentState::IN_TRANSIT,
                ShipmentState::OUT_FOR_DELIVERY,
            ]),

            'due_today' => $query
                ->whereDate('expected_delivery_at', today())
                ->whereNotIn('state', [ShipmentState::DELIVERED, ShipmentState::CANCELLED, ShipmentState::RETURNED]),

            'overdue' => $query
                ->whereNotNull('expected_delivery_at')
                ->where('expected_delivery_at', '<', now())
                ->whereNotIn('state', [ShipmentState::DELIVERED, ShipmentState::CANCELLED, ShipmentState::RETURNED]),

            'failed' => $query->where('state', ShipmentState::FAILED_DELIVERY),

            // Unassigned: confirmed, and not yet on any trip.
            default => $query
                ->where('state', ShipmentState::CONFIRMED)
                ->whereDoesntHave('trips'),
        };
    }

    public function updatedActiveTab(): void
    {
        $this->resetTable();
    }
}
