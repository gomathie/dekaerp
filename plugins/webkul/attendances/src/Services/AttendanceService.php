<?php

namespace Webkul\Attendance\Services;

use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Webkul\Attendance\Enums\CaptureLocation;
use Webkul\Attendance\Enums\Source;
use Webkul\Attendance\Exceptions\CorrectionRefusedException;
use Webkul\Attendance\Exceptions\LocationRequiredException;
use Webkul\Attendance\Exceptions\NotYourAttendanceException;
use Webkul\Attendance\Models\Attendance;
use Webkul\Attendance\Models\CompanySetting;
use Webkul\Attendance\Models\Correction;
use Webkul\Attendance\Support\AttendanceAccess;
use Webkul\Employee\Models\Department;
use Webkul\Employee\Models\Employee;
use Webkul\Security\Models\User;
use Webkul\Support\Services\CompanyContext;

/**
 * Every attendance write goes through here.
 *
 * The banner can only clock the signed-in user. Changing someone else's row
 * is a correction, with a reason, written to attendances_corrections.
 * A super-admin Gate::before skips policies, so the company switch, the
 * "only yourself" rule and the team rule are repeated here.
 */
class AttendanceService
{
    public function __construct(private ScheduleReader $schedule) {}

    /**
     * @param  array{latitude?: float|null, longitude?: float|null, accuracy?: float|null, ip?: string|null}  $evidence
     */
    public function clockIn(User $user, array $evidence = []): Attendance
    {
        $this->assertClockingThemselves($user, $evidence);

        $employee = $this->employeeForCurrentCompany($user);

        AttendanceAccess::ensureEnabled($employee->company_id);

        return DB::transaction(function () use ($user, $employee, $evidence): Attendance {
            Employee::withoutGlobalScopes()->whereKey($employee->id)->lockForUpdate()->first();

            $open = $this->openFor($employee);

            if ($open !== null) {
                return $open;
            }

            $captured = $this->capture($employee->company_id, $evidence, required: true);
            $now = now();

            try {
                return Attendance::query()->create([
                    'employee_id' => $employee->id,
                    'check_in_at' => $now,
                    'source_in' => Source::Banner,
                    'latitude_in' => $captured['latitude'],
                    'longitude_in' => $captured['longitude'],
                    'accuracy_in' => $captured['accuracy'],
                    'ip_in' => $captured['ip'],
                    'on_leave' => $this->schedule->onApprovedLeave($employee, $now->copy()->timezone($this->schedule->timezone($employee))),
                    'creator_id' => $user->id,
                ]);
            } catch (UniqueConstraintViolationException $exception) {
                $existing = $this->openFor($employee);

                if ($existing === null) {
                    throw $exception;
                }

                return $existing;
            }
        });
    }

    /**
     * @param  array{latitude?: float|null, longitude?: float|null, accuracy?: float|null, ip?: string|null}  $evidence
     */
    public function clockOut(User $user, array $evidence = []): Attendance
    {
        $this->assertClockingThemselves($user, $evidence);

        $employee = $this->employeeForCurrentCompany($user);

        AttendanceAccess::ensureEnabled($employee->company_id);

        return DB::transaction(function () use ($employee, $evidence): Attendance {
            Employee::withoutGlobalScopes()->whereKey($employee->id)->lockForUpdate()->first();

            $open = $this->openFor($employee);

            if ($open === null) {
                throw CorrectionRefusedException::notClockedIn();
            }

            $captured = $this->capture($employee->company_id, $evidence, required: true);
            $out = $this->afterCheckIn($open->check_in_at);

            $open->forceFill([
                'check_out_at' => $out,
                'worked_minutes' => $this->workedMinutes($open->check_in_at, $out),
                'source_out' => Source::Banner,
                'latitude_out' => $captured['latitude'],
                'longitude_out' => $captured['longitude'],
                'accuracy_out' => $captured['accuracy'],
                'ip_out' => $captured['ip'],
            ])->save();

            return $open->refresh();
        });
    }

    /**
     * @param  array{check_in_at?: Carbon|string|null, check_out_at?: Carbon|string|null, note?: string|null}  $changes
     */
    public function correct(User $actor, Attendance $attendance, array $changes, string $reason): Attendance
    {
        $reason = trim($reason);

        if ($reason === '') {
            throw CorrectionRefusedException::reasonRequired();
        }

        $attendance = Attendance::withoutGlobalScopes()->findOrFail($attendance->id);

        AttendanceAccess::ensureEnabled($attendance->company_id);
        $this->assertMayCorrect($actor, $attendance);

        return DB::transaction(function () use ($actor, $attendance, $changes, $reason): Attendance {
            $locked = Attendance::withoutGlobalScopes()->whereKey($attendance->id)->lockForUpdate()->firstOrFail();

            $before = $this->snapshot($locked);
            $checkIn = array_key_exists('check_in_at', $changes)
                ? Carbon::parse($changes['check_in_at'])
                : $locked->check_in_at;
            $checkOut = array_key_exists('check_out_at', $changes)
                ? ($changes['check_out_at'] === null ? null : Carbon::parse($changes['check_out_at']))
                : $locked->check_out_at;

            if ($checkOut !== null && $checkOut->lessThanOrEqualTo($checkIn)) {
                throw CorrectionRefusedException::checkOutOrder();
            }

            if ($checkOut === null && $this->openFor(Employee::withoutGlobalScopes()->findOrFail($locked->employee_id), $locked->id) !== null) {
                throw CorrectionRefusedException::notAllowed();
            }

            $locked->forceFill([
                'check_in_at' => $checkIn,
                'check_out_at' => $checkOut,
                'worked_minutes' => $checkOut === null ? null : $this->workedMinutes($checkIn, $checkOut),
                'note' => array_key_exists('note', $changes) ? $changes['note'] : $locked->note,
                'is_corrected' => true,
                'corrected_by_id' => $actor->id,
                'source_out' => $checkOut === null ? $locked->source_out : Source::Manager,
            ])->save();

            Correction::query()->create([
                'attendance_id' => $locked->id,
                'before' => $before,
                'after' => $this->snapshot($locked),
                'reason' => $reason,
                'actor_id' => $actor->id,
                'created_at' => now(),
            ]);

            return $locked->refresh();
        });
    }

    /**
     * Closes rows left open longer than the company's limit. No user is
     * logged in: the row's own company is the scope, and a company that
     * later switches attendance off still gets these closed so the hours
     * stop growing.
     */
    public function autoCheckOut(?Carbon $now = null): int
    {
        $now ??= now();
        $closed = 0;

        Attendance::withoutGlobalScopes()
            ->open()
            ->orderBy('id')
            ->each(function (Attendance $attendance) use ($now, &$closed): void {
                $hours = CompanySetting::forCompany((int) $attendance->company_id)->auto_checkout_after_hours;

                if ($hours < 1) {
                    $hours = 12;
                }

                if ($attendance->check_in_at->gt($now->copy()->subHours($hours))) {
                    return;
                }

                $out = $attendance->check_in_at->copy()->addHours($hours);

                $attendance->forceFill([
                    'check_out_at' => $out,
                    'worked_minutes' => $this->workedMinutes($attendance->check_in_at, $out),
                    'source_out' => Source::System,
                    'needs_review' => true,
                ])->save();

                $closed++;
            });

        return $closed;
    }

    /**
     * Null means the user may see every attendance the company scope allows.
     *
     * @return array<int, int>|null
     */
    public function visibleEmployeeIds(User $user): ?array
    {
        if ($user->can('view_any_attendance_attendance')) {
            return null;
        }

        $ids = [];
        $own = $this->employeeAnywhere($user);

        if ($own !== null) {
            $ids[] = $own->id;
        }

        $departmentIds = $this->managedDepartmentIds($user);

        if ($departmentIds !== []) {
            $ids = array_merge(
                $ids,
                Employee::withoutGlobalScopes()
                    ->whereIn('department_id', $departmentIds)
                    ->pluck('id')
                    ->all(),
            );
        }

        return array_values(array_unique(array_map('intval', $ids)));
    }

    public function employeeForCurrentCompany(User $user): Employee
    {
        $companyId = app(CompanyContext::class)->currentId() ?: $user->default_company_id;

        $employee = Employee::withoutGlobalScopes()
            ->where('user_id', $user->id)
            ->when($companyId, fn ($query) => $query->where('company_id', $companyId))
            ->first();

        if ($employee === null) {
            throw CorrectionRefusedException::noEmployee();
        }

        if ((int) $employee->user_id !== (int) $user->id) {
            throw new NotYourAttendanceException;
        }

        return $employee;
    }

    /**
     * The banner clocks the signed-in user. An employee id in the payload is
     * somebody else, and that is a correction, not a second way in.
     *
     * @param  array<string, mixed>  $evidence
     */
    protected function assertClockingThemselves(User $user, array $evidence): void
    {
        if (! isset($evidence['employee_id'])) {
            return;
        }

        $target = Employee::withoutGlobalScopes()->find($evidence['employee_id']);

        if ($target !== null && (int) $target->user_id !== (int) $user->id) {
            throw new NotYourAttendanceException;
        }
    }

    protected function assertMayCorrect(User $actor, Attendance $attendance): void
    {
        if ($actor->can('correct_attendance_attendance')) {
            return;
        }

        $employee = Employee::withoutGlobalScopes()->find($attendance->employee_id);

        if ($employee !== null && in_array((int) $employee->department_id, $this->managedDepartmentIds($actor), true)) {
            return;
        }

        throw CorrectionRefusedException::notAllowed();
    }

    /**
     * @return array<int, int>
     */
    protected function managedDepartmentIds(User $user): array
    {
        $manager = $this->employeeAnywhere($user);

        if ($manager === null) {
            return [];
        }

        return Department::withoutGlobalScopes()
            ->where('manager_id', $manager->id)
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->all();
    }

    protected function employeeAnywhere(User $user): ?Employee
    {
        return Employee::withoutGlobalScopes()->where('user_id', $user->id)->first();
    }

    protected function openFor(Employee $employee, ?int $exceptId = null): ?Attendance
    {
        return Attendance::withoutGlobalScopes()
            ->where('employee_id', $employee->id)
            ->whereNull('check_out_at')
            ->when($exceptId, fn ($query) => $query->whereKeyNot($exceptId))
            ->first();
    }

    /**
     * @param  array{latitude?: float|null, longitude?: float|null, accuracy?: float|null, ip?: string|null}  $evidence
     * @return array{latitude: float|null, longitude: float|null, accuracy: float|null, ip: string|null}
     */
    protected function capture(int $companyId, array $evidence, bool $required): array
    {
        $mode = CompanySetting::forCompany($companyId)->capture_location ?? CaptureLocation::Off;

        if ($mode === CaptureLocation::Off) {
            return ['latitude' => null, 'longitude' => null, 'accuracy' => null, 'ip' => null];
        }

        $latitude = $evidence['latitude'] ?? null;
        $longitude = $evidence['longitude'] ?? null;

        if ($required && $mode === CaptureLocation::Required && ($latitude === null || $longitude === null)) {
            throw LocationRequiredException::missing();
        }

        return [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'accuracy' => $evidence['accuracy'] ?? null,
            'ip' => $evidence['ip'] ?? null,
        ];
    }

    protected function afterCheckIn(Carbon $checkIn): Carbon
    {
        $out = now();

        if ($out->lessThanOrEqualTo($checkIn)) {
            return $checkIn->copy()->addSecond();
        }

        return $out;
    }

    protected function workedMinutes(Carbon $from, Carbon $to): int
    {
        return max(1, (int) round($from->diffInMinutes($to)));
    }

    /**
     * @return array<string, mixed>
     */
    protected function snapshot(Attendance $attendance): array
    {
        return [
            'check_in_at' => $attendance->check_in_at?->toIso8601String(),
            'check_out_at' => $attendance->check_out_at?->toIso8601String(),
            'note' => $attendance->note,
            'worked_minutes' => $attendance->worked_minutes,
        ];
    }
}
