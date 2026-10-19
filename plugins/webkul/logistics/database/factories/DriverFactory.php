<?php

namespace Webkul\Logistics\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Logistics\Models\Driver;
use Webkul\Support\Models\Company;

/**
 * @extends Factory<Driver>
 */
class DriverFactory extends Factory
{
    protected $model = Driver::class;

    public function definition(): array
    {
        return [
            'name'               => fake()->name(),
            'phone'              => fake()->phoneNumber(),
            'license_number'     => strtoupper(fake()->bothify('DL-########')),
            'license_class'      => 'C',
            'license_expires_at' => now()->addYear()->toDateString(),
            'is_active'          => true,
            'company_id'         => fn () => Company::query()->value('id') ?? Company::factory(),
        ];
    }

    public function expired(): static
    {
        return $this->state(['license_expires_at' => now()->subDay()->toDateString()]);
    }
}
