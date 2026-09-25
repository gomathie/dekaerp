<?php

namespace Webkul\Logistics\Filament\Clusters\Reporting\Pages;

use BackedEnum;
use Filament\Actions\ExportAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Webkul\Logistics\Enums\ShipmentState;
use Webkul\Logistics\Filament\Exports\DeliveryPerformanceExporter;
use Webkul\Logistics\Models\Shipment;

/**
 * On time, late, or failed (WP-11).
 *
 * Judged on the promise the company made: a shipment is late when it was
 * delivered after expected_delivery_at, and on time when it was not. A shipment
 * with no expected date is neither - it was never promised for a day, and
 * scoring it would invent a commitment nobody made. Those are shown as "not
 * measured" rather than silently counted as on time, which is the flattering
 * mistake.
 */
class DeliveryPerformance extends ReportPage
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clock';

    protected static ?int $navigationSort = 2;

    protected static string $lang = 'logistics::reports.delivery-performance';

    protected static function pagePermission(): string
    {
        return 'page_logistics_delivery_performance';
    }

    /**
     * How a single shipment scored.
     */
    public static function outcome(Shipment $shipment): string
    {
        if ($shipment->state === ShipmentState::FAILED_DELIVERY) {
            return 'failed';
        }

        if (! $shipment->actual_delivery_at) {
            return $shipment->expected_delivery_at && $shipment->expected_delivery_at->isPast()
                ? 'overdue'
                : 'pending';
        }

        if (! $shipment->expected_delivery_at) {
            return 'not-measured';
        }

        return $shipment->actual_delivery_at->lessThanOrEqualTo($shipment->expected_delivery_at)
            ? 'on-time'
            : 'late';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Shipment::query()->with('customer'))
            ->columns([
                TextColumn::make('name')
                    ->label(__(static::$lang.'.columns.reference'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('customer.name')
                    ->label(__(static::$lang.'.columns.customer'))
                    ->searchable()
                    ->visibleFrom('sm'),

                TextColumn::make('expected_delivery_at')
                    ->label(__(static::$lang.'.columns.expected'))
                    ->dateTime()
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('actual_delivery_at')
                    ->label(__(static::$lang.'.columns.delivered'))
                    ->dateTime()
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('outcome')
                    ->label(__(static::$lang.'.columns.outcome'))
                    ->state(fn (Model $record): string => __(static::$lang.'.outcomes.'.static::outcome($record)))
                    ->badge()
                    ->color(fn (Model $record): string => match (static::outcome($record)) {
                        'on-time'      => 'success',
                        'late'         => 'warning',
                        'failed'       => 'danger',
                        'overdue'      => 'danger',
                        default        => 'gray',
                    }),

                // The size of the miss, not only that there was one: an hour
                // late and a week late are different conversations.
                TextColumn::make('delay')
                    ->label(__(static::$lang.'.columns.delay'))
                    ->state(function (Model $record): ?string {
                        if (! $record->actual_delivery_at || ! $record->expected_delivery_at) {
                            return null;
                        }

                        return $record->actual_delivery_at->greaterThan($record->expected_delivery_at)
                            ? $record->expected_delivery_at->diffForHumans($record->actual_delivery_at, true)
                            : null;
                    })
                    ->placeholder('—')
                    ->visibleFrom('lg'),
            ])
            ->filters([
                SelectFilter::make('customer')
                    ->label(__(static::$lang.'.filters.customer'))
                    ->relationship('customer', 'name')
                    ->searchable()
                    ->preload(),

                Filter::make('late_only')
                    ->label(__(static::$lang.'.filters.late-only'))
                    ->toggle()
                    ->query(fn (Builder $query): Builder => $query
                        ->whereNotNull('expected_delivery_at')
                        ->whereNotNull('actual_delivery_at')
                        ->whereColumn('actual_delivery_at', '>', 'expected_delivery_at')),

                Filter::make('failed_only')
                    ->label(__(static::$lang.'.filters.failed-only'))
                    ->toggle()
                    ->query(fn (Builder $query): Builder => $query->where('state', ShipmentState::FAILED_DELIVERY)),

                Filter::make('period')
                    ->schema(DateRange::components(static::$lang))
                    ->query(fn (Builder $query, array $data): Builder => DateRange::apply($query, $data, 'expected_delivery_at'))
                    ->indicateUsing(fn (array $data): ?string => DateRange::indicator($data, static::$lang)),
            ])
            ->headerActions([
                ExportAction::make()
                    ->label(__(static::$lang.'.export'))
                    ->icon('heroicon-o-arrow-up-tray')
                    ->exporter(DeliveryPerformanceExporter::class),
            ])
            ->defaultSort('expected_delivery_at', 'desc');
    }
}
