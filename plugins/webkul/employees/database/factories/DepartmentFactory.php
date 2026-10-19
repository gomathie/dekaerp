<?php

namespace Webkul\Employee\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Employee\Models\Department;
use Webkul\Employee\Models\Employee;
use Webkul\Support\Database\Factories\Concerns\HasCompanyDefault;

class DepartmentFactory extends Factory
{
    use HasCompanyDefault;

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Department::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name,
            // Not Employee::factory(): EmployeeFactory defaults department_id to
            // this factory, so a manager here makes an employee, which makes a
            // department, which makes a manager, until PHP's stack runs out -
            // Employee::factory()->create() could never complete. The column is
            // nullable; use the ->for()/->state() you need when a department
            // must have a manager.
            'manager_id' => null,
            'color'      => fake()->hexColor,
        ];
    }
}
