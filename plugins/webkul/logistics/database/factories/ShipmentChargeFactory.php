<?php

namespace Webkul\Logistics\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Logistics\Models\Shipment;
use Webkul\Logistics\Models\ShipmentCharge;

/**
 * @extends Factory<ShipmentCharge>
 */
class ShipmentChargeFactory extends Factory
{
    protected $model = ShipmentCharge::class;

    public function definition(): array
    {
        $quantity = fake()->numberBetween(1, 5);
        $price = fake()->randomFloat(2, 10, 500);

        return [
            'description' => 'Freight',
            'quantity'    => $quantity,
            'price_unit'  => $price,
            'discount'    => 0,
            'subtotal'    => $quantity * $price,
            'total'       => $quantity * $price,
            'is_billable' => true,
            'shipment_id' => fn () => Shipment::factory(),
        ];
    }
}
