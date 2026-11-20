<?php

namespace Webkul\Security\Filament\Resources\RoleResource\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Webkul\Security\Filament\Resources\RoleResource;

class RolesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('filament-shield::filament-shield.column.name'))
                    // Names people typed ("HR Manager") show as typed; only
                    // snake_case system names are prettified (U29, 2026-11-14).
                    ->formatStateUsing(fn ($state): string => str_contains((string) $state, '_') ? Str::headline($state) : (string) $state)
                    ->searchable(),
                TextColumn::make('guard_name')
                    ->label(__('filament-shield::filament-shield.column.guard_name')),
                TextColumn::make('permissions_count')
                    ->badge()
                    ->label(__('filament-shield::filament-shield.column.permissions'))
                    ->counts('permissions')
                    ->colors(['success']),
                TextColumn::make('updated_at')
                    ->label(__('filament-shield::filament-shield.column.updated_at'))
                    ->dateTime(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->before(function (DeleteAction $action, Model $record): void {
                        if (RoleResource::isProtectedRoleRecord($record)) {
                            Notification::make()
                                ->danger()
                                ->title(__('security::filament/resources/role.notification.system-role-delete.title'))
                                ->body(__('security::filament/resources/role.notification.system-role-delete.body'))
                                ->send();

                            $action->cancel();
                        }
                    }),
            ])
            ->toolbarActions([
                DeleteBulkAction::make()
                    ->fetchSelectedRecords(true)
                    ->authorizeIndividualRecords('delete')
                    ->action(function (DeleteBulkAction $action, Collection $records): void {
                        $deletableRecords = $records->reject(
                            fn (Model $record): bool => RoleResource::isProtectedRoleRecord($record)
                        );

                        if ($deletableRecords->isEmpty()) {
                            $action->cancel();

                            return;
                        }

                        $deletableRecords->each(fn (Model $record): ?bool => $record->delete());
                    }),
            ])
            ->defaultSort('created_at', 'asc');
    }
}
