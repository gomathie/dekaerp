<?php

namespace Webkul\Employee\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Employee\Enums\WorkLocation as WorkLocationType;
use Webkul\Employee\Models\WorkLocation;
use Webkul\Security\Models\User;
use Webkul\Support\Database\Factories\Concerns\HasCompanyDefault;

class WorkLocationFactory extends Factory
{
    use HasCompanyDefault;

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = WorkLocation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // No user_id column on employees_work_locations, and the flag is
            // `is_active`, not `active`. Both were silently wrong until a test
            // tried to create one.
            'creator_id'      => User::query()->value('id') ?? User::factory(),
            'name'            => fake()->name,
            // The model casts this column to the WorkLocation enum, so a random
            // word cannot be read back: hydrating the record throws
            // "not a valid backing value". One of the three real cases instead.
            'location_type'   => fake()->randomElement(WorkLocationType::cases()),
            'location_number' => fake()->numberBetween(1, 100),
            'is_active'       => true,
        ];
    }
}
