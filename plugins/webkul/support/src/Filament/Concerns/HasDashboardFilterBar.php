<?php

namespace Webkul\Support\Filament\Concerns;

use Filament\Forms\Components\Field;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\View;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Carbon;
use Throwable;
use Webkul\Support\Filament\DashboardPeriod;

/**
 * The header row of every dashboard with filters: the period chips, "Custom
 * dates", the dates shown with the period they are compared with, and a
 * "Filters" button with the number of filters in use (user, 2026-11-25,
 * approved mockup).
 *
 * One rule, one place: a dashboard page that uses HasFiltersForm adds this
 * trait and nothing else. It replaces Filament's filters block
 * (getFiltersFormContentComponent()) with the bar, and the filters form
 * moves into the panel the Filters button opens. The filter keys, their
 * values and every widget are unchanged; the dates are written to the same
 * two keys the date picker used to fill.
 *
 * The page's filters form must not offer dates of its own: it carries
 * DashboardDateRange::keys() (hidden), and the bar sets them.
 */
trait HasDashboardFilterBar
{
    /**
     * The filter keys the page's widgets read the period from. Most
     * dashboards use start_date / end_date; Projects and Recruitment kept
     * their older startDate / endDate.
     *
     * @return array{0: string, 1: string}
     */
    protected function dashboardPeriodKeys(): array
    {
        return ['start_date', 'end_date'];
    }

    public function getFiltersFormContentComponent(): Component
    {
        return View::make('support::filament.dashboard-filter-bar')
            ->viewData(fn (): array => $this->dashboardFilterBarData())
            ->schema([EmbeddedSchema::make('filtersForm')]);
    }

    /**
     * One-click periods (DB-4, 2026-11-23). An unknown key changes nothing:
     * the method is callable from the browser.
     */
    public function selectPeriod(string $preset): void
    {
        $range = DashboardPeriod::presets()[$preset] ?? null;

        if ($range === null) {
            return;
        }

        $this->setDashboardPeriod($range[0], $range[1]);
    }

    /**
     * The "Custom dates" popover. Two Y-m-d dates, the first not after the
     * second; anything else changes nothing, because the browser can send
     * any value here. A range equal to a preset lights that preset: it is the
     * same period.
     */
    public function applyCustomPeriod(mixed $start = null, mixed $end = null): void
    {
        $start = static::parseDashboardDate($start);
        $end = static::parseDashboardDate($end);

        if ($start === null || $end === null || $start->greaterThan($end)) {
            return;
        }

        $this->setDashboardPeriod($start, $end);
    }

    /**
     * "Clear all" in the Filters panel: every filter back to its default.
     * The period and anything that is not a field of the panel (the
     * Overview's section tab) stay, since the panel does not show them.
     * Each reset goes through updatedFilters(), as a change made in the
     * panel would, so the Overview copies it to the filter's other names
     * and Filament keeps the session copy in step.
     */
    public function clearDashboardFilters(): void
    {
        foreach ($this->dashboardFilterFields() as $name => $field) {
            $default = $field->getDefaultState();

            data_set($this->filters, $name, $default);

            $this->updatedFilters($default, $name);
        }
    }

    /**
     * What the bar shows. The period comes from the shared rule, so the
     * caption says exactly what the widgets compute.
     *
     * @return array{presets: array<string, string>, active: ?string, start: string, end: string, period: string, previous: string, inUse: list<string>}
     */
    protected function dashboardFilterBarData(): array
    {
        [$start, $end] = $this->currentDashboardPeriod();
        [$previousStart, $previousEnd] = DashboardPeriod::previous($start, $end);

        return [
            'presets'  => collect(DashboardPeriod::presets())
                ->map(fn (array $range, string $key): string => __('support::filament/dashboard.period.'.$key))
                ->all(),
            'active'   => DashboardPeriod::presetOf($start, $end),
            'start'    => $start->toDateString(),
            'end'      => $end->toDateString(),
            'period'   => DashboardPeriod::label($start, $end),
            'previous' => DashboardPeriod::label($previousStart, $previousEnd),
            'inUse'    => $this->dashboardFiltersInUse(),
        ];
    }

    /**
     * The labels of the filters that narrow the figures now: a value that
     * is set and is not the field's default (Recruitment's status "All" is a
     * default, not a filter). The period is not counted; the bar shows it.
     * The Overview shows one field per alias group, so a filter known by two
     * names counts once.
     *
     * @return list<string>
     */
    protected function dashboardFiltersInUse(): array
    {
        $labels = [];

        foreach ($this->dashboardFilterFields() as $name => $field) {
            $value = data_get($this->filters, $name);

            if (blank($value) || $value == $field->getDefaultState()) {
                continue;
            }

            $label = $field->getLabel();

            $labels[] = trim(strip_tags($label instanceof Htmlable ? $label->toHtml() : (string) $label));
        }

        return $labels;
    }

    /**
     * The visible fields of the Filters panel, by filter key, without the
     * hidden period keys.
     *
     * @return array<string, Field>
     */
    protected function dashboardFilterFields(): array
    {
        $fields = [];

        foreach ($this->getFiltersForm()->getFlatFields() as $field) {
            if ($field instanceof Hidden || in_array($field->getName(), $this->dashboardPeriodKeys(), true)) {
                continue;
            }

            $fields[$field->getName()] = $field;
        }

        return $fields;
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    protected function currentDashboardPeriod(): array
    {
        [$startKey, $endKey] = $this->dashboardPeriodKeys();
        [$defaultStart, $defaultEnd] = DashboardPeriod::defaultRange();

        // Read as the widgets read it (Carbon::parse), so the caption names
        // the period they computed, whatever format the URL carried.
        $read = function (mixed $value): ?Carbon {
            try {
                return empty($value) ? null : Carbon::parse($value);
            } catch (Throwable) {
                return null;
            }
        };

        $start = $read($this->filters[$startKey] ?? null)?->startOfDay() ?? $defaultStart;
        $end = $read($this->filters[$endKey] ?? null)?->endOfDay() ?? $defaultEnd;

        return [$start, $end];
    }

    protected function setDashboardPeriod(Carbon $start, Carbon $end): void
    {
        [$startKey, $endKey] = $this->dashboardPeriodKeys();

        $this->filters = array_merge($this->filters ?? [], [
            $startKey => $start->toDateString(),
            $endKey   => $end->toDateString(),
        ]);

        // As a change in the panel would: Filament keeps the session copy.
        $this->updatedFilters($this->filters[$startKey], $startKey);
    }

    /**
     * A strict Y-m-d date, or null. Round-tripped so that "2026-02-31" is
     * refused rather than rolled into March.
     */
    protected static function parseDashboardDate(mixed $value): ?Carbon
    {
        if (! is_string($value) || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return null;
        }

        try {
            $date = Carbon::createFromFormat('!Y-m-d', $value);
        } catch (Throwable) {
            return null;
        }

        return ($date instanceof Carbon && $date->toDateString() === $value) ? $date : null;
    }
}
