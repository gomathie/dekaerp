<?php

namespace Webkul\Attendance\Filament\Clusters\Reporting\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Carbon;
use Webkul\Attendance\Filament\Clusters\Reporting;
use Webkul\Attendance\Services\AttendanceReports;
use Webkul\Attendance\Services\AttendanceService;
use Webkul\Attendance\Support\AttendanceAccess;
use Webkul\Employee\Models\Employee;

/**
 * What every attendance report shares (AT-6).
 *
 * One access rule and one date range, written once. A per-page copy is how a
 * later report ends up quietly missing the company switch.
 *
 * `canAccess()` checks the page's own Shield permission explicitly: writing
 * one on the page replaces the trait's, and `parent::canAccess()` would then
 * answer true for everyone.
 */
abstract class AttendanceReportPage extends Page
{
    protected static ?string $cluster = Reporting::class;

    protected string $view = 'attendances::filament.reports.report';

    public ?string $from = null;

    public ?string $until = null;

    /**
     * The Shield page permission, e.g. page_attendance_lateness.
     */
    abstract protected static function pagePermission(): string;

    abstract protected static function lang(): string;

    /**
     * @return array<int, string>
     */
    abstract public function columns(): array;

    /**
     * @return array<int, array<string, mixed>>
     */
    abstract public function rows(): array;

    public static function canAccess(): bool
    {
        $user = auth()->user();

        if (! $user?->can(static::pagePermission())) {
            return false;
        }

        return AttendanceAccess::enabledForCurrent();
    }

    public function mount(): void
    {
        $this->from ??= now()->startOfMonth()->toDateString();
        $this->until ??= now()->toDateString();
    }

    public static function getNavigationLabel(): string
    {
        return __(static::lang().'.title');
    }

    public function getTitle(): string
    {
        return __(static::lang().'.title');
    }

    public function getSubheading(): ?string
    {
        return __(static::lang().'.subheading');
    }

    /**
     * The chosen range, clamped so a stray date cannot scan the whole table.
     *
     * @return array{0: Carbon, 1: Carbon}
     */
    protected function range(): array
    {
        $from = $this->parseDate($this->from) ?? now()->startOfMonth();
        $until = $this->parseDate($this->until) ?? now();

        if ($until->lt($from)) {
            [$from, $until] = [$until, $from];
        }

        if ($from->diffInDays($until) > AttendanceReports::MAX_DAYS) {
            $from = $until->copy()->subDays(AttendanceReports::MAX_DAYS);
        }

        return [$from->startOfDay(), $until->endOfDay()];
    }

    protected function parseDate(?string $value): ?Carbon
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * The employees this viewer may report on, always as a list.
     *
     * The service answers null for "everyone the company scope allows" (the
     * view-any permission). The reports take a list and read attendance past
     * the global scopes, so null crashed every report for exactly the people
     * allowed to see all of them (Sentry DEKAERP-24, 2026-11-23). "Everyone"
     * is resolved here through Employee's own company scope, never by
     * dropping it, so another company's staff can never appear.
     *
     * @return array<int, int>
     */
    protected function visibleEmployeeIds(): array
    {
        $user = auth()->user();

        if ($user === null) {
            return [];
        }

        $visible = app(AttendanceService::class)->visibleEmployeeIds($user);

        if ($visible !== null) {
            return $visible;
        }

        return Employee::query()->pluck('id')->map(fn ($id): int => (int) $id)->all();
    }
}
