<?php

namespace Webkul\TimeOff\Filament;

use Webkul\Employee\Models\Employee;
use Webkul\TimeOff\Enums\State;
use Webkul\TimeOff\Filament\Clusters\Management\Resources\TimeOffResource;
use Webkul\TimeOff\Models\Leave;

/**
 * The Employees card on the Overview's All tab (mockup approved by the
 * user, 2026-11-13): who is away today and what is waiting for approval.
 * Seen by whoever manages time off; scopes apply through the models.
 */
class OverviewCard
{
    public static function visible(): bool
    {
        return TimeOffResource::canViewAny();
    }

    /**
     * @return array<string, mixed>
     */
    public static function data(): array
    {
        $lang = 'time-off::filament/overview-card';

        $now = now();

        $onLeave = Leave::query()
            ->where('state', State::VALIDATE_TWO->value)
            ->where('date_from', '<=', $now)
            ->where('date_to', '>=', $now)
            ->distinct()
            ->count('employee_id');

        $toApprove = Leave::query()
            ->whereIn('state', [State::CONFIRM->value, State::VALIDATE_ONE->value])
            ->count();

        $employees = Employee::query()->where('is_active', true)->count();

        $percent = $employees > 0 ? round(($employees - min($onLeave, $employees)) / $employees * 100) : 0;

        return [
            'label' => __($lang.'.label'),
            'url'   => TimeOffResource::getUrl('index'),
            'items' => [
                ['label' => __($lang.'.employees'), 'value' => number_format($employees)],
                ['label' => __($lang.'.on-leave-today'), 'value' => number_format($onLeave)],
                ['label' => __($lang.'.to-approve'), 'value' => number_format($toApprove), 'tone' => $toApprove > 0 ? 'warning' : null],
            ],
            'bar' => [
                'percent' => $percent,
                'tone'    => 'success',
                'caption' => __($lang.'.available', ['percent' => $percent]),
            ],
        ];
    }
}
