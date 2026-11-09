<?php

namespace Webkul\Project\Filament\Widgets;

use Webkul\Project\Filament\Widgets\Concerns\GuardsProjectDashboardWidget;

use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Carbon;
use Webkul\Project\Models\Task;
use Webkul\Project\Models\TaskStage;
use Webkul\Support\Filament\Widgets\DashboardChartWidget;

class TaskByStageChart extends DashboardChartWidget
{
    use GuardsProjectDashboardWidget, InteractsWithPageFilters;

    protected static ?int $sort = 1;

    protected static function getPagePermission(): ?string
    {
        return 'widget_project_task_by_stage_chart';
    }

    public function getHeading(): string|Htmlable|null
    {
        return __('projects::filament/widgets/task-by-stage.heading.title');
    }

    protected function getData(): array
    {
        $query = Task::query();

        if (! empty($this->pageFilters['selectedProjects'])) {
            $query->whereIn('project_id', $this->pageFilters['selectedProjects']);
        }

        if (! empty($this->pageFilters['selectedAssignees'])) {
            $query->whereHas('users', function ($q) {
                $q->whereIn('users.id', $this->pageFilters['selectedAssignees']);
            });
        }

        if (! empty($this->pageFilters['selectedTags'])) {
            $query->whereHas('tags', function ($q) {
                $q->whereIn('projects_task_tag.tag_id', $this->pageFilters['selectedTags']);
            });
        }

        if (! empty($this->pageFilters['selectedPartners'])) {
            $query->whereIn('parent_id', $this->pageFilters['selectedPartners']);
        }

        $startDate = ! is_null($this->pageFilters['startDate'] ?? null) ?
            Carbon::parse($this->pageFilters['startDate']) :
            null;

        $endDate = ! is_null($this->pageFilters['endDate'] ?? null) ?
            Carbon::parse($this->pageFilters['endDate']) :
            now();

        // One grouped count instead of one count per stage. Upstream counted
        // per stage of TaskStage::all(), so the dashboard's query count grew
        // with every stage created - the same N+1 as Sentry 156271115 on the
        // state chart. toBase() keeps the global company and ownership scopes.
        // No start date means no lower bound. Upstream passed null into
        // whereBetween(), and "between NULL and ..." matches no row at all, so
        // the chart showed zero for every stage whenever no start was chosen.
        if ($startDate !== null) {
            $query->where($query->qualifyColumn('created_at'), '>=', $startDate);
        }

        $counts = $query->toBase()
            ->where($query->qualifyColumn('created_at'), '<=', $endDate)
            ->selectRaw('stage_id, count(*) as aggregate')
            ->groupBy('stage_id')
            ->pluck('aggregate', 'stage_id');

        $labels = [];
        $data = [];

        foreach (TaskStage::query()->get(['id', 'name']) as $stage) {
            $labels[] = in_array($stage->name, $labels) ? $stage->name.' '.$stage->id : $stage->name;
            $data[] = (int) ($counts[$stage->id] ?? 0);
        }

        $ramp = $this->chartRamp();

        $colors = array_map(fn (int $index): string => $ramp[$index % count($ramp)], array_keys($data));

        return [
            'datasets' => [
                [
                    'label'           => __('projects::filament/widgets/task-by-stage.datasets.label'),
                    'data'            => $data,
                    'backgroundColor' => $colors,
                    'borderRadius'    => 8,
                    'borderSkipped'   => 'bottom',
                    'maxBarThickness' => 34,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return $this->barChartOptions(8);
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
