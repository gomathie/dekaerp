<?php

namespace Webkul\Logistics\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Logistics\Models\VehicleType;

/**
 * @extends Factory<VehicleType>
 */
class VehicleTypeFactory extends Factory
{
    protected $model = VehicleType::class;

    public function definition(): array
    {
        return [
            'name'        => fake()->words(2, true),
            'code'        => strtoupper(fake()->unique()->lexify('VT???')),
            'capacity_kg' => 1000,
            'capacity_m3' => 8,
            'is_active'   => true,
            'sort'        => 0,
            'company_id'  => null,
        ];
    }
}
