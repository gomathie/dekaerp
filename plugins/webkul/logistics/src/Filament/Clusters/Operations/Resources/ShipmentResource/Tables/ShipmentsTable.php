<?php

namespace Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Logistics\Enums\ShipmentState;
use Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource;

class ShipmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__(ShipmentResource::$lang.'.table.columns.number'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('customer.name')
                    ->label(__(ShipmentResource::$lang.'.table.columns.customer'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('state')
                    ->label(__(ShipmentResource::$lang.'.table.columns.state'))
                    ->badge()
                    ->sortable(),
                TextColumn::make('planned_pickup_at')
                    ->label(__(ShipmentResource::$lang.'.table.columns.planned-pickup-at'))
                    ->dateTime()
                    ->placeholder('—')
                    ->visibleFrom('sm')
                    ->sortable(),
                TextColumn::make('expected_delivery_at')
                    ->label(__(ShipmentResource::$lang.'.table.columns.expected-delivery-at'))
                    ->dateTime()
                    ->placeholder('—')
                    ->visibleFrom('md')
                    ->sortable(),
                TextColumn::make('serviceType.name')
                    ->label(__(ShipmentResource::$lang.'.table.columns.service-type'))
                    ->placeholder('—')
                    ->visibleFrom('lg'),
                TextColumn::make('total_packages')
                    ->label(__(ShipmentResource::$lang.'.table.columns.packages'))
                    ->numeric()
                    ->visibleFrom('lg'),
                TextColumn::make('dispatcher.name')
                    ->label(__(ShipmentResource::$lang.'.table.columns.dispatcher'))
                    ->placeholder('—')
                    ->visibleFrom('lg'),
                TextColumn::make('company.name')
                    ->label(__(ShipmentResource::$lang.'.table.columns.company'))
                    ->visibleFrom('lg'),
            ])
            ->filters([
                SelectFilter::make('state')
                    ->label(__(ShipmentResource::$lang.'.table.filters.state'))
                    ->options(ShipmentState::class)
                    ->multiple(),
                SelectFilter::make('customer')
                    ->label(__(ShipmentResource::$lang.'.table.filters.customer'))
                    ->relationship('customer', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('service_type')
                    ->label(__(ShipmentResource::$lang.'.table.filters.service-type'))
                    ->relationship('serviceType', 'name'),
                Filter::make('planned_pickup_at')
                    ->schema([
                        DateTimePicker::make('from')->label(__(ShipmentResource::$lang.'.table.filters.pickup-from'))->seconds(false),
                        DateTimePicker::make('until')->label(__(ShipmentResource::$lang.'.table.filters.pickup-until'))->seconds(false),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['from'] ?? null, fn (Builder $q, $date) => $q->where('planned_pickup_at', '>=', $date))
                        ->when($data['until'] ?? null, fn (Builder $q, $date) => $q->where('planned_pickup_at', '<=', $date))),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('id', 'desc');
    }
}
