<?php

namespace Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class DeliveryProofsRelationManager extends RelationManager
{
    protected static string $relationship = 'deliveryProofs';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return Auth::user()?->can('view', $ownerRecord) ?? false;
    }

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('logistics::delivery.relation-manager.title');
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('recipient_name')
                    ->label(__('logistics::delivery.relation-manager.columns.recipient'))
                    ->searchable(),
                TextColumn::make('received_at')
                    ->label(__('logistics::delivery.relation-manager.columns.received-at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('stop.sequence')
                    ->label(__('logistics::delivery.relation-manager.columns.stop'))
                    ->formatStateUsing(fn ($state): string => '#'.$state)
                    ->placeholder('-'),
                TextColumn::make('captured_via')
                    ->label(__('logistics::delivery.relation-manager.columns.captured-via'))
                    ->badge(),
                IconColumn::make('photo_path')
                    ->label(__('logistics::delivery.relation-manager.columns.photo'))
                    ->boolean(),
                IconColumn::make('signature_path')
                    ->label(__('logistics::delivery.relation-manager.columns.signature'))
                    ->boolean(),
            ])
            ->defaultSort('received_at', 'desc');
    }
}
