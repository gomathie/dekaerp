<?php

namespace Webkul\Attendance\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Attendance\Enums\Source;
use Webkul\Attendance\Models\Attendance;
use Webkul\Employee\Models\Employee;
use Webkul\Support\Database\Factories\Concerns\HasCompanyDefault;

/**
 * @extends Factory<Attendance>
 */
class AttendanceFactory extends Factory
{
    use HasCompanyDefault;

    protected $model = Attendance::class;

    public function definition(): array
    {
        return [
            'employee_id'  => Employee::factory(),
            'check_in_at'  => now()->subHour(),
            'check_out_at' => null,
            'source_in'    => Source::Banner,
            'needs_review' => false,
            'on_leave'     => false,
            'is_corrected' => false,
        ];
    }

    public function closed(): static
    {
        return $this->state(fn (): array => [
            'check_in_at'    => now()->subHours(8),
            'check_out_at'   => now(),
            'worked_minutes' => 480,
            'source_out'     => Source::Banner,
        ]);
    }
}
