<?php

namespace Webkul\Logistics\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Logistics\Models\PackageType;

/**
 * @extends Factory<PackageType>
 */
class PackageTypeFactory extends Factory
{
    protected $model = PackageType::class;

    public function definition(): array
    {
        return [
            'name'       => fake()->word(),
            'code'       => strtoupper(fake()->unique()->lexify('PK???')),
            'is_active'  => true,
            'sort'       => 0,
            'company_id' => null,
        ];
    }
}
