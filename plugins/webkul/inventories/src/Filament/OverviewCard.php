<?php

namespace Webkul\Inventory\Filament;

use Illuminate\Database\Eloquent\Builder;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Enums\OperationType;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\OperationResource;
use Webkul\Inventory\Models\Operation;

/**
 * The Inventory card on the Overview's All tab (mockup approved by the
 * user, 2026-11-13): what the warehouse has to move today. Scopes apply
 * through the model.
 */
class OverviewCard
{
    public static function visible(): bool
    {
        return OperationResource::canViewAny();
    }

    /**
     * @return array<string, mixed>
     */
    public static function data(): array
    {
        $lang = 'inventories::filament/overview-card';

        $open = fn (): Builder => Operation::query()
            ->whereNotIn('state', [OperationState::DONE->value, OperationState::CANCELED->value]);

        $ofType = fn (OperationType $type): \Closure => fn (Builder $query): Builder => $query->where('type', $type->value);

        $receiptsToday = $open()
            ->whereHas('operationType', $ofType(OperationType::INCOMING))
            ->whereDate('scheduled_at', now()->toDateString())
            ->count();

        $toShip = $open()
            ->whereHas('operationType', $ofType(OperationType::OUTGOING))
            ->where('state', OperationState::ASSIGNED->value)
            ->count();

        $late = $open()->where('scheduled_at', '<', now()->startOfDay())->count();

        $all = $open()->count();
        $percent = $all > 0 ? round(($all - $late) / $all * 100) : 0;

        return [
            'label' => __($lang.'.label'),
            // The Operations section, not OperationResource::getUrl(): that
            // resource builds its URL from a record and fails without one.
            'url'   => \Webkul\Inventory\Filament\Clusters\Operations::getUrl(),
            'items' => [
                ['label' => __($lang.'.receipts-today'), 'value' => number_format($receiptsToday)],
                ['label' => __($lang.'.ready-to-ship'), 'value' => number_format($toShip)],
                ['label' => __($lang.'.late'), 'value' => number_format($late), 'tone' => $late > 0 ? 'danger' : null],
            ],
            'bar' => $all > 0 ? [
                'percent' => $percent,
                'tone'    => $percent >= 90 ? 'success' : 'warning',
                'caption' => __($lang.'.on-schedule', ['percent' => $percent]),
            ] : null,
        ];
    }
}
