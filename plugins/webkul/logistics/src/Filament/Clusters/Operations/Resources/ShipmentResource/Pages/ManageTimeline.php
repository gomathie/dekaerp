<?php

namespace Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\Pages;

use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Livewire;
use Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

/**
 * The shipment's tracking timeline. Read-only: events are written by the
 * workflow, delivery and (later) telematics, never by hand.
 */
class ManageTimeline extends ManageRelatedRecords
{
    use HasRecordNavigationTabs;

    protected static string $resource = ShipmentResource::class;

    protected static string $relationship = 'events';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clock';

    protected static string $lang = 'logistics::filament/clusters/operations/resources/shipment';

    public static function getNavigationLabel(): string
    {
        return __(static::$lang.'.timeline.title');
    }

    public static function getNavigationBadge($parameters = []): ?string
    {
        return Livewire::current()->getRecord()->events()->count();
    }

    public function getTitle(): string
    {
        return __(static::$lang.'.timeline.title');
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('occurred_at')
                    ->label(__(static::$lang.'.timeline.columns.occurred-at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('type')
                    ->label(__(static::$lang.'.timeline.columns.type'))
                    ->badge(),
                TextColumn::make('source')
                    ->label(__(static::$lang.'.timeline.columns.source'))
                    ->badge()
                    ->visibleFrom('sm'),
                TextColumn::make('user.name')
                    ->label(__(static::$lang.'.timeline.columns.user'))
                    ->placeholder('—')
                    ->visibleFrom('md'),
                TextColumn::make('location_label')
                    ->label(__(static::$lang.'.timeline.columns.location'))
                    ->placeholder('—')
                    ->visibleFrom('lg'),
                TextColumn::make('notes')
                    ->label(__(static::$lang.'.timeline.columns.notes'))
                    ->placeholder('—')
                    ->wrap()
                    ->visibleFrom('lg'),
            ])
            ->defaultSort('occurred_at', 'desc')
            ->paginated([25, 50, 100])
            ->headerActions([])
            ->recordActions([])
            ->toolbarActions([]);
    }

    public function getRelationshipQuery(): Builder
    {
        return parent::getRelationshipQuery()->with('user:id,name');
    }
}
