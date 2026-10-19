<?php

namespace Webkul\Logistics\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Logistics\Enums\VehicleOwnership;
use Webkul\Logistics\Models\Vehicle;
use Webkul\Support\Models\Company;

/**
 * @extends Factory<Vehicle>
 */
class VehicleFactory extends Factory
{
    protected $model = Vehicle::class;

    public function definition(): array
    {
        return [
            'registration_no' => strtoupper(fake()->unique()->bothify('??-####-??')),
            'name'            => fake()->words(2, true),
            'ownership'       => VehicleOwnership::OWNED,
            'capacity_kg'     => 1000,
            'capacity_m3'     => 8,
            'is_active'       => true,
            'company_id'      => fn () => Company::query()->value('id') ?? Company::factory(),
        ];
    }
}
