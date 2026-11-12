<?php

namespace Webkul\Attendance\Livewire;

use Filament\Notifications\Notification;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Throwable;
use Webkul\Attendance\Models\Attendance;
use Webkul\Attendance\Services\AttendanceService;
use Webkul\Attendance\Services\ScheduleReader;
use Webkul\Attendance\Support\AttendanceAccess;
use Webkul\Employee\Models\Employee;
use Webkul\Security\Models\User;
use Webkul\Support\Services\CompanyContext;

/**
 * The Clock in / Clock out control in the dashboard banner.
 *
 * Only the signed-in person clocks themselves: the service enforces that, and
 * the button never sends an employee id. Everything it shows is resolved for
 * the company currently being worked in, so a branch employee clocking in from
 * the parent company sees no control rather than the wrong one.
 */
class ClockButton extends Component
{
    public function clockIn(): void
    {
        $this->run(fn (AttendanceService $service, $user): Attendance => $service->clockIn($user));
    }

    public function clockOut(): void
    {
        $this->run(fn (AttendanceService $service, $user): Attendance => $service->clockOut($user));
    }

    /**
     * @param  callable(AttendanceService, User): Attendance  $action
     */
    protected function run(callable $action): void
    {
        $user = auth()->user();

        abort_unless($user, 403);

        try {
            $action(app(AttendanceService::class), $user);
        } catch (ValidationException $exception) {
            // Expected refusals: not clocked in, no employee record, missing
            // location. Each carries the sentence the person needs to read.
            Notification::make()
                ->warning()
                ->title(collect($exception->errors())->flatten()->first() ?? $exception->getMessage())
                ->send();

            return;
        } catch (Throwable $exception) {
            report($exception);

            Notification::make()
                ->danger()
                ->title(__('attendances::attendance.notifications.failed'))
                ->send();

            return;
        }

        $this->dispatch('attendance-updated');
    }

    public function render(): View
    {
        return view('attendances::livewire.clock-button', [
            'state' => $this->state(),
        ]);
    }

    /**
     * @return array{visible: bool, open: bool, since: ?string, onLeave: bool}
     */
    protected function state(): array
    {
        $employee = $this->employee();

        if ($employee === null) {
            return ['visible' => false, 'open' => false, 'since' => null, 'onLeave' => false];
        }

        $open = Attendance::withoutGlobalScopes()
            ->where('employee_id', $employee->id)
            ->whereNull('check_out_at')
            ->first();

        return [
            'visible' => true,
            'open'    => $open !== null,
            'since'   => $open?->checkInLocal()?->format('H:i'),
            'onLeave' => $open !== null
                ? (bool) $open->on_leave
                : app(ScheduleReader::class)->onApprovedLeave(
                    $employee,
                    now()->timezone(app(ScheduleReader::class)->timezone($employee)),
                ),
        ];
    }

    protected function employee(): ?Employee
    {
        $user = auth()->user();
        $companyId = app(CompanyContext::class)->currentId();

        if (! $user || ! $companyId || ! AttendanceAccess::enabledFor($companyId)) {
            return null;
        }

        return Employee::withoutGlobalScopes()
            ->where('user_id', $user->id)
            ->where('company_id', $companyId)
            ->first();
    }
}
