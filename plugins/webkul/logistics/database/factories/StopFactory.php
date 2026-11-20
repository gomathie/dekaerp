<?php

namespace Webkul\Logistics\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Logistics\Enums\StopState;
use Webkul\Logistics\Enums\StopType;
use Webkul\Logistics\Models\Shipment;
use Webkul\Logistics\Models\Stop;

/**
 * @extends Factory<Stop>
 */
class StopFactory extends Factory
{
    protected $model = Stop::class;

    public function definition(): array
    {
        return [
            'sequence'           => 1,
            'type'               => StopType::DELIVERY,
            'state'              => StopState::PENDING,
            'contact_name'       => fake()->name(),
            'contact_phone'      => fake()->phoneNumber(),
            'planned_arrival_at' => now()->addDays(2),
            'shipment_id'        => fn () => Shipment::factory(),
        ];
    }

    public function pickup(): static
    {
        return $this->state(['type' => StopType::PICKUP, 'sequence' => 0]);
    }
}
