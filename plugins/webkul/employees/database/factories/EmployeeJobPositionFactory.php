<?php

namespace Webkul\Employee\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Employee\Models\Department;
use Webkul\Employee\Models\EmployeeJobPosition;
use Webkul\Security\Models\User;
use Webkul\Support\Database\Factories\Concerns\HasCompanyDefault;

class EmployeeJobPositionFactory extends Factory
{
    use HasCompanyDefault;

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EmployeeJobPosition::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sort'               => fake()->randomNumber(),
            'name'               => fake()->word,
            'description'        => fake()->text,
            'requirements'       => fake()->text,
            'expected_employees' => fake()->randomNumber(),
            'no_of_employee'     => fake()->randomNumber(),
            // The column is `is_active`. Neither `status` nor `open_date`, which
            // this factory used to set, exists on employees_job_positions - in
            // the employees schema or in the columns the recruitments plugin adds
            // - so creating a job position failed outright.
            'is_active'          => true,
            'no_of_recruitment'  => fake()->randomNumber(),
            'department_id'      => Department::factory(),
            'creator_id'         => User::query()->value('id') ?? User::factory(),
        ];
    }
}
