<?php

namespace Webkul\Attendance\Policies;

use Webkul\Attendance\Models\Attendance;
use Webkul\Attendance\Services\AttendanceService;
use Webkul\Attendance\Support\AttendanceAccess;
use Webkul\Employee\Models\Department;
use Webkul\Employee\Models\Employee;
use Webkul\Security\Models\User;

/**
 * Permissions use the namespace key "attendance", not the plugin name
 * "attendances". Shield builds the key from the first namespace segment
 * after Webkul (PermissionManager), which is Attendance.
 *
 * The service repeats these checks. A super-admin Gate::before never
 * reaches this class.
 */
class AttendancePolicy
{
    public function viewAny(User $user): bool
    {
        return AttendanceAccess::enabledForCurrent()
            && ($user->can('view_any_attendance_attendance') || $user->can('view_team_attendance_attendance') || $this->managesSomeone($user));
    }

    public function view(User $user, Attendance $attendance): bool
    {
        if (! AttendanceAccess::enabledFor($attendance->company_id)) {
            return false;
        }

        $visible = app(AttendanceService::class)->visibleEmployeeIds($user);

        return $visible === null || in_array((int) $attendance->employee_id, $visible, true);
    }

    public function correct(User $user, Attendance $attendance): bool
    {
        if (! AttendanceAccess::enabledFor($attendance->company_id)) {
            return false;
        }

        if ($user->can('correct_attendance_attendance')) {
            return true;
        }

        $employee = Employee::withoutGlobalScopes()->find($attendance->employee_id);

        if ($employee === null || $employee->department_id === null) {
            return false;
        }

        return in_array((int) $employee->department_id, $this->managedDepartmentIds($user), true);
    }

    protected function managesSomeone(User $user): bool
    {
        return $this->managedDepartmentIds($user) !== [];
    }

    /**
     * @return array<int, int>
     */
    protected function managedDepartmentIds(User $user): array
    {
        $managerId = Employee::withoutGlobalScopes()->where('user_id', $user->id)->value('id');

        if ($managerId === null) {
            return [];
        }

        return Department::withoutGlobalScopes()
            ->where('manager_id', $managerId)
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->all();
    }
}
