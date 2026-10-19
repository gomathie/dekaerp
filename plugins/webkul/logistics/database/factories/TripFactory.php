<?php

namespace Webkul\Logistics\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Logistics\Enums\TripState;
use Webkul\Logistics\Models\Trip;
use Webkul\Support\Models\Company;

/**
 * @extends Factory<Trip>
 */
class TripFactory extends Factory
{
    protected $model = Trip::class;

    public function definition(): array
    {
        return [
            'state'            => TripState::PLANNED,
            'planned_start_at' => now()->addDay(),
            'planned_end_at'   => now()->addDay()->addHours(8),
            'company_id'       => fn () => Company::query()->value('id') ?? Company::factory(),
        ];
    }
}
