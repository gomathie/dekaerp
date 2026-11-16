<?php

namespace Webkul\Recruitment\Filament;

use Webkul\Recruitment\Filament\Clusters\Applications\Resources\ApplicantResource;
use Webkul\Recruitment\Models\Applicant;
use Webkul\Recruitment\Models\JobPosition;

/**
 * The Recruitment card on the Overview's All tab (mockup approved by the
 * user, 2026-11-13). Scopes apply through the models.
 */
class OverviewCard
{
    public static function visible(): bool
    {
        return ApplicantResource::canViewAny();
    }

    /**
     * @return array<string, mixed>
     */
    public static function data(): array
    {
        $lang = 'recruitments::filament/overview-card';

        $positions = JobPosition::query()->where('is_active', true)->count();

        $newApplicants = Applicant::query()->where('created_at', '>=', now()->subDays(7))->count();

        $hiredThisMonth = Applicant::query()
            ->whereNotNull('date_closed')
            ->where('date_closed', '>=', now()->startOfMonth())
            ->count();

        $inProgress = Applicant::query()->whereNull('date_closed')->whereNull('refuse_reason_id')->count();

        $percent = ($inProgress + $hiredThisMonth) > 0 ? round($hiredThisMonth / ($inProgress + $hiredThisMonth) * 100) : 0;

        return [
            'label' => __($lang.'.label'),
            'url'   => ApplicantResource::getUrl('index'),
            'items' => [
                ['label' => __($lang.'.open-positions'), 'value' => number_format($positions)],
                ['label' => __($lang.'.new-applicants'), 'value' => number_format($newApplicants), 'tone' => $newApplicants > 0 ? 'success' : null],
                ['label' => __($lang.'.hired-this-month'), 'value' => number_format($hiredThisMonth)],
            ],
            'bar' => [
                'percent' => $percent,
                'tone'    => 'info',
                'caption' => __($lang.'.in-progress', ['count' => number_format($inProgress)]),
            ],
        ];
    }
}
