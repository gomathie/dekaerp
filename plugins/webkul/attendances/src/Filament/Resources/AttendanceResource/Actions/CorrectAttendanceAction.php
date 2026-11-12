<?php

namespace Webkul\Attendance\Filament\Resources\AttendanceResource\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Webkul\Attendance\Models\Attendance;
use Webkul\Attendance\Services\AttendanceService;

/**
 * A manager correction, with a reason (AT-D4).
 *
 * The reason is not `required()` on the field: Laravel treats a blank or
 * whitespace-only value as empty, and the action would never run, so the
 * service's own refusal never reached the person. The service decides.
 */
class CorrectAttendanceAction extends Action
{
    protected string $lang = 'attendances::filament/resources/attendance.actions.correct';

    public static function getDefaultName(): ?string
    {
        return 'correctAttendance';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__($this->lang.'.label'))
            ->icon('heroicon-o-pencil-square')
            ->modalHeading(__($this->lang.'.heading'))
            ->modalDescription(__($this->lang.'.description'))
            ->visible(fn (Attendance $record): bool => Auth::user()?->can('correct', $record) ?? false)
            ->schema([
                DateTimePicker::make('check_in_at')
                    ->label(__($this->lang.'.fields.check-in'))
                    ->seconds(false)
                    ->required(),

                DateTimePicker::make('check_out_at')
                    ->label(__($this->lang.'.fields.check-out'))
                    ->seconds(false),

                Textarea::make('note')
                    ->label(__($this->lang.'.fields.note'))
                    ->rows(2),

                Textarea::make('reason')
                    ->label(__($this->lang.'.fields.reason'))
                    ->rows(2)
                    ->helperText(__($this->lang.'.fields.reason-help')),
            ])
            ->fillForm(fn (Attendance $record): array => [
                'check_in_at'  => $record->check_in_at,
                'check_out_at' => $record->check_out_at,
                'note'         => $record->note,
            ])
            ->action(function (Attendance $record, array $data): void {
                try {
                    app(AttendanceService::class)->correct(
                        Auth::user(),
                        $record,
                        [
                            'check_in_at'  => $data['check_in_at'] ?? null,
                            'check_out_at' => $data['check_out_at'] ?? null,
                            'note'         => $data['note'] ?? null,
                        ],
                        (string) ($data['reason'] ?? ''),
                    );
                } catch (ValidationException $exception) {
                    Notification::make()
                        ->warning()
                        ->title(collect($exception->errors())->flatten()->first() ?? $exception->getMessage())
                        ->persistent()
                        ->send();

                    return;
                }

                Notification::make()
                    ->success()
                    ->title(__($this->lang.'.notification'))
                    ->send();
            });
    }
}
