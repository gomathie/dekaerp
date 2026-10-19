<?php

namespace Webkul\Logistics\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Logistics\Enums\TransportMode;
use Webkul\Logistics\Models\ServiceType;

/**
 * @extends Factory<ServiceType>
 */
class ServiceTypeFactory extends Factory
{
    protected $model = ServiceType::class;

    public function definition(): array
    {
        return [
            'name'              => fake()->words(2, true),
            'code'              => strtoupper(fake()->unique()->lexify('SVC???')),
            'transport_mode'    => TransportMode::ROAD,
            'product_reference' => 'LOG-FREIGHT',
            'is_active'         => true,
            'sort'              => 0,
            'company_id'        => null,
        ];
    }
}
