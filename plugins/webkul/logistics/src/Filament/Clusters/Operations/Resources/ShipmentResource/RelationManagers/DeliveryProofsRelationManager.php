<?php

namespace Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Webkul\Logistics\Models\DeliveryProof;

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
                    ->boolean()
                    ->url(fn (DeliveryProof $record): ?string => static::fileUrl($record, 'photo_path'))
                    ->openUrlInNewTab(),
                IconColumn::make('signature_path')
                    ->label(__('logistics::delivery.relation-manager.columns.signature'))
                    ->boolean()
                    ->url(fn (DeliveryProof $record): ?string => static::fileUrl($record, 'signature_path'))
                    ->openUrlInNewTab(),
            ])
            ->defaultSort('received_at', 'desc');
    }

    protected static function fileUrl(DeliveryProof $proof, string $attribute): ?string
    {
        $path = $proof->getAttribute($attribute);

        if (blank($path)) {
            return null;
        }

        if (config('filesystems.disks.public.driver') !== 'tenant-s3') {
            return Storage::disk('public')->url($path);
        }

        $root = trim((string) config('filesystems.disks.public.root'), '/');
        $objectKey = implode('/', array_filter([
            $root,
            'companies/'.(int) $proof->company_id,
            ltrim((string) $path, '/'),
        ], fn (string $segment): bool => $segment !== ''));

        return route('secure-storage', ['path' => $objectKey]);
    }
}
