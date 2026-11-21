<?php

namespace Webkul\Support\Filament\Resources\SequenceResource\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Support\Enums\SequenceResetFrequency;
use Webkul\Support\Filament\Resources\SequenceResource;
use Webkul\Support\Models\Sequence;

class SequencesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['company', 'scope']))
            ->columns([
                TextColumn::make('name')
                    ->label(__('support::filament/resources/sequence.table.columns.name'))
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('code')
                    ->label(__('support::filament/resources/sequence.table.columns.code'))
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('scope')
                    ->label(__('support::filament/resources/sequence.table.columns.applies-to'))
                    ->state(fn (Sequence $record): ?string => SequenceResource::scopeLabel($record))
                    ->placeholder('—'),
                TextColumn::make('company.name')
                    ->label(__('support::filament/resources/sequence.table.columns.company'))
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('next_preview')
                    ->label(__('support::filament/resources/sequence.table.columns.next-preview'))
                    ->state(fn (Sequence $record): string => $record->formatNumber(max(1, $record->next_number)))
                    ->badge()
                    ->color('info'),
                TextColumn::make('next_number')
                    ->label(__('support::filament/resources/sequence.table.columns.next-number'))
                    ->sortable(),
                TextColumn::make('reset_frequency')
                    ->label(__('support::filament/resources/sequence.table.columns.reset-frequency'))
                    ->formatStateUsing(fn (SequenceResetFrequency $state): string => SequenceResetFrequency::options()[$state->value])
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('company_id')
                    ->label(__('support::filament/resources/sequence.table.filters.company'))
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                EditAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('support::filament/resources/sequence.table.actions.edit.notification.title'))
                            ->body(__('support::filament/resources/sequence.table.actions.edit.notification.body')),
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('support::filament/resources/sequence.table.actions.delete.notification.title'))
                            ->body(__('support::filament/resources/sequence.table.actions.delete.notification.body')),
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('support::filament/resources/sequence.table.bulk-actions.delete.notification.title'))
                                ->body(__('support::filament/resources/sequence.table.bulk-actions.delete.notification.body')),
                        ),
                ]),
            ])
            ->defaultSort('code');
    }
}
