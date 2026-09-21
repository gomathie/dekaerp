<?php

namespace Webkul\Logistics\Filament\Clusters\Operations\Resources\TripResource\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Webkul\Logistics\Enums\ShipmentState;
use Webkul\Logistics\Models\Shipment;
use Webkul\Logistics\Models\Trip;
use Webkul\Logistics\Services\DispatchService;

class ShipmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'shipments';

    protected static string $lang = 'logistics::filament/clusters/operations/resources/trip';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __(static::$lang.'.relations.shipments.title');
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__(static::$lang.'.relations.shipments.columns.number'))
                    ->searchable(),

                TextColumn::make('state')
                    ->label(__(static::$lang.'.relations.shipments.columns.state'))
                    ->badge(),

                TextColumn::make('customer.name')
                    ->label(__(static::$lang.'.relations.shipments.columns.customer'))
                    ->placeholder('—')
                    ->visibleFrom('sm'),

                TextColumn::make('total_weight_kg')
                    ->label(__(static::$lang.'.relations.shipments.columns.weight'))
                    ->numeric()
                    ->visibleFrom('md'),

                TextColumn::make('total_volume_m3')
                    ->label(__(static::$lang.'.relations.shipments.columns.volume'))
                    ->numeric()
                    ->visibleFrom('lg'),
            ])
            ->headerActions([
                // Attaching goes through DispatchService rather than the plain
                // relation: it schedules the stops and moves each shipment to
                // AwaitingPickup through ShipmentWorkflow. A bare attach would
                // leave the shipment confirmed and the trip without stops.
                AttachAction::make()
                    ->label(__(static::$lang.'.relations.shipments.actions.attach.label'))
                    ->visible(fn (): bool => Auth::user()?->can('update', $this->getOwnerRecord()) ?? false)
                    ->schema(fn (): array => [
                        Select::make('recordId')
                            ->label(__(static::$lang.'.relations.shipments.actions.attach.field'))
                            ->options(fn (): array => $this->attachableShipments())
                            ->searchable()
                            ->required(),
                    ])
                    ->action(function (array $data): void {
                        /** @var Trip $trip */
                        $trip = $this->getOwnerRecord();

                        $shipment = Shipment::query()->findOrFail($data['recordId']);

                        $warnings = app(DispatchService::class)->assign($trip, [$shipment]);

                        Notification::make()
                            ->success()
                            ->title(__(static::$lang.'.relations.shipments.actions.attach.notification'))
                            ->body($warnings === [] ? null : implode(' ', $warnings))
                            ->send();
                    }),
            ])
            ->recordActions([
                DetachAction::make()
                    ->visible(fn (): bool => Auth::user()?->can('update', $this->getOwnerRecord()) ?? false),
            ]);
    }

    /**
     * Confirmed shipments of this trip's company only.
     *
     * The company filter is the security boundary; the state filter matches
     * DispatchService, which refuses anything not confirmed.
     *
     * @return array<int, string>
     */
    protected function attachableShipments(): array
    {
        /** @var Trip $trip */
        $trip = $this->getOwnerRecord();

        return Shipment::query()
            ->where('company_id', $trip->company_id)
            ->where('state', ShipmentState::CONFIRMED)
            ->whereDoesntHave('trips', fn (Builder $query) => $query->whereKey($trip->getKey()))
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }
}
