<?php

namespace Webkul\Logistics\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Logistics\Enums\ShipmentPriority;
use Webkul\Logistics\Enums\ShipmentState;
use Webkul\Logistics\Enums\TransportMode;
use Webkul\Logistics\Models\Shipment;
use Webkul\Partner\Models\Partner;
use Webkul\Support\Models\Company;

/**
 * @extends Factory<Shipment>
 */
class ShipmentFactory extends Factory
{
    protected $model = Shipment::class;

    public function definition(): array
    {
        return [
            'customer_reference'   => strtoupper(fake()->bothify('PO-#####')),
            'transport_mode'       => TransportMode::ROAD,
            'priority'             => ShipmentPriority::NORMAL,
            'state'                => ShipmentState::DRAFT,
            'origin_label'         => fake()->city(),
            'destination_label'    => fake()->city(),
            'planned_pickup_at'    => now()->addDay(),
            'expected_delivery_at' => now()->addDays(2),
            'customer_id'          => fn () => Partner::factory(),
            'company_id'           => fn () => Company::query()->value('id') ?? Company::factory(),
        ];
    }

    public function forCompany(Company|int $company): static
    {
        return $this->state(['company_id' => $company instanceof Company ? $company->id : $company]);
    }
}
