<?php

namespace Webkul\Account\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Account\Models\Account;
use Webkul\Account\Models\Expense;
use Webkul\Account\Models\Journal;
use Webkul\Support\Database\Factories\Concerns\HasCompanyDefault;
use Webkul\Support\Models\Currency;

/**
 * A bare expense row, with no journal entry. Real expenses are written by
 * ExpenseRecorder, which posts the entry; use this only where a row is all
 * a test needs (a list, a policy).
 */
class ExpenseFactory extends Factory
{
    use HasCompanyDefault;

    protected $model = Expense::class;

    public function definition(): array
    {
        $amount = fake()->randomFloat(2, 10, 500);

        return [
            'number'               => 'EXP/'.now()->format('Y').'/'.fake()->unique()->numerify('#####'),
            'date'                 => now()->toDateString(),
            'currency_id'          => Currency::factory(),
            'amount'               => $amount,
            'currency_rate'        => 1,
            'amount_total'         => $amount,
            'amount_tax'           => 0,
            'amount_total_company' => $amount,
            'expense_account_id'   => Account::factory()->expense(),
            'journal_id'           => Journal::factory()->bank(),
            'reference'            => fake()->bothify('Receipt ####'),
        ];
    }
}
