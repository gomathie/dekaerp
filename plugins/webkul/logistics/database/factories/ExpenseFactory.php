<?php

namespace Webkul\Logistics\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Logistics\Enums\ExpensePaidBy;
use Webkul\Logistics\Enums\ExpenseState;
use Webkul\Logistics\Models\Expense;
use Webkul\Logistics\Models\ExpenseCategory;
use Webkul\Support\Models\Company;

/**
 * @extends Factory<Expense>
 */
class ExpenseFactory extends Factory
{
    protected $model = Expense::class;

    public function definition(): array
    {
        return [
            'date'        => now()->toDateString(),
            'amount'      => fake()->randomFloat(2, 5, 300),
            'paid_by'     => ExpensePaidBy::COMPANY,
            'state'       => ExpenseState::DRAFT,
            'description' => fake()->sentence(),
            'category_id' => fn () => ExpenseCategory::factory(),
            'company_id'  => fn () => Company::query()->value('id') ?? Company::factory(),
        ];
    }
}
