<?php

namespace Webkul\Logistics\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Logistics\Models\ExpenseCategory;

/**
 * @extends Factory<ExpenseCategory>
 */
class ExpenseCategoryFactory extends Factory
{
    protected $model = ExpenseCategory::class;

    public function definition(): array
    {
        return [
            'name'              => fake()->words(2, true),
            'code'              => strtoupper(fake()->unique()->lexify('EX???')),
            'requires_receipt'  => false,
            'is_subcontracting' => false,
            'is_active'         => true,
            'sort'              => 0,
            'company_id'        => null,
        ];
    }

    public function subcontracting(): static
    {
        return $this->state(['is_subcontracting' => true, 'requires_receipt' => true]);
    }
}
