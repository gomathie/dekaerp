<?php

namespace Webkul\Logistics\Filament;

use Webkul\Logistics\Enums\ShipmentState;
use Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource;
use Webkul\Logistics\Models\Shipment;

/**
 * The Logistics card on the Overview's All tab (mockup approved by the
 * user, 2026-11-13). Visibility is ShipmentResource::canViewAny(), which
 * carries the Logistics module switch and permission. "On time" counts
 * only deliveries with a promised date, so a missing date is not called
 * late or on time (the WP-11 rule).
 */
class OverviewCard
{
    public static function visible(): bool
    {
        return ShipmentResource::canViewAny();
    }

    /**
     * @return array<string, mixed>
     */
    public static function data(): array
    {
        $lang = 'logistics::filament/overview-card';

        $moving = Shipment::query()->whereIn('state', [
            ShipmentState::PICKED_UP->value,
            ShipmentState::IN_TRANSIT->value,
            ShipmentState::OUT_FOR_DELIVERY->value,
        ])->count();

        $failed = Shipment::query()->where('state', ShipmentState::FAILED_DELIVERY->value)->count();

        $deliveredThisMonth = Shipment::query()
            ->where('state', ShipmentState::DELIVERED->value)
            ->where('actual_delivery_at', '>=', now()->startOfMonth())
            ->whereNotNull('expected_delivery_at');

        $delivered = (clone $deliveredThisMonth)->count();
        $late = (clone $deliveredThisMonth)->whereColumn('actual_delivery_at', '>', 'expected_delivery_at')->count();

        $percent = $delivered > 0 ? round(($delivered - $late) / $delivered * 100) : 0;

        return [
            'label' => __($lang.'.label'),
            'url'   => ShipmentResource::getUrl('index'),
            'items' => [
                ['label' => __($lang.'.in-transit'), 'value' => number_format($moving)],
                ['label' => __($lang.'.late'), 'value' => number_format($late), 'tone' => $late > 0 ? 'danger' : null],
                ['label' => __($lang.'.failed'), 'value' => number_format($failed), 'tone' => $failed > 0 ? 'warning' : null],
            ],
            'bar' => $delivered > 0 ? [
                'percent' => $percent,
                'tone'    => $percent >= 90 ? 'success' : 'warning',
                'caption' => __($lang.'.on-time', ['percent' => $percent]),
            ] : null,
        ];
    }
}
