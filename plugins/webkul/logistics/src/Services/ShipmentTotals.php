<?php

namespace Webkul\Logistics\Services;

use Webkul\Logistics\Models\Shipment;

/**
 * Cargo totals shown on the shipment and used by dispatch capacity checks.
 * Charge and cost totals belong to WP-7 and WP-8b.
 */
class ShipmentTotals
{
    public function recalculate(Shipment $shipment): Shipment
    {
        $lines = $shipment->lines()->get(['quantity', 'weight_kg', 'volume_m3']);

        $shipment->forceFill([
            'total_packages'  => (int) $lines->sum('quantity'),
            'total_weight_kg' => (float) $lines->sum('weight_kg'),
            'total_volume_m3' => (float) $lines->sum('volume_m3'),
        ]);

        // Quietly: totals are derived, so they shouldn't appear as user edits in
        // the activity log or fire another round of model events.
        $shipment->saveQuietly();

        return $shipment;
    }
}
