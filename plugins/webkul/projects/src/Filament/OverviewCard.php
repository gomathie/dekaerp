<?php

namespace Webkul\Project\Filament;

use Webkul\Project\Enums\TaskState;
use Webkul\Project\Filament\Resources\TaskResource;
use Webkul\Project\Models\Project;
use Webkul\Project\Models\Task;

/**
 * The Projects card on the Overview's All tab (mockup approved by the user,
 * 2026-11-13). Today's state; company and ownership scopes apply through the
 * models, so a user sees the same projects and tasks as on their lists.
 */
class OverviewCard
{
    public static function visible(): bool
    {
        return TaskResource::canViewAny();
    }

    /**
     * @return array<string, mixed>
     */
    public static function data(): array
    {
        $lang = 'projects::filament/overview-card';

        $finished = [TaskState::DONE->value, TaskState::CANCELLED->value];

        $open = Task::query()->whereNotIn('state', $finished)->count();
        $done = Task::query()->where('state', TaskState::DONE->value)->count();

        $overdue = Task::query()
            ->whereNotIn('state', $finished)
            ->whereNotNull('deadline')
            ->where('deadline', '<', now())
            ->count();

        $percent = ($open + $done) > 0 ? round($done / ($open + $done) * 100) : 0;

        return [
            'label' => __($lang.'.label'),
            'url'   => TaskResource::getUrl('index'),
            'items' => [
                ['label' => __($lang.'.active-projects'), 'value' => number_format(Project::query()->where('is_active', true)->count())],
                ['label' => __($lang.'.open-tasks'), 'value' => number_format($open)],
                ['label' => __($lang.'.overdue-tasks'), 'value' => number_format($overdue), 'tone' => $overdue > 0 ? 'danger' : null],
            ],
            'bar' => [
                'percent' => $percent,
                'tone'    => 'info',
                'caption' => __($lang.'.done', ['percent' => $percent]),
            ],
        ];
    }
}
