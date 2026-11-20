<?php

namespace Webkul\Logistics\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Logistics\Models\Shipment;
use Webkul\Logistics\Models\ShipmentLine;

/**
 * @extends Factory<ShipmentLine>
 */
class ShipmentLineFactory extends Factory
{
    protected $model = ShipmentLine::class;

    public function definition(): array
    {
        return [
            'description' => fake()->words(3, true),
            'quantity'    => fake()->numberBetween(1, 20),
            'weight_kg'   => fake()->randomFloat(3, 1, 500),
            'volume_m3'   => fake()->randomFloat(3, 0.01, 5),
            'shipment_id' => fn () => Shipment::factory(),
        ];
    }
}
