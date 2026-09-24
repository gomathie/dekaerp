<?php

namespace Webkul\Employee\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Employee\Models\DepartureReason;

class DepartureReasonFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DepartureReason::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // The column is `sort`, not `sequence`: inserting the old name fails
            // with "column sequence does not exist". reason_code is an integer
            // column, so a word cannot go in it either.
            'sort'        => fake()->numberBetween(1, 100),
            'reason_code' => fake()->numberBetween(1, 9999),
            'name'        => fake()->word,
        ];
    }
}
