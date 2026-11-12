<?php

namespace Webkul\Attendance\Filament\Resources;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;
use Webkul\Attendance\Filament\Resources\AttendanceResource\Pages\ListAttendances;
use Webkul\Attendance\Filament\Resources\AttendanceResource\Tables\AttendancesTable;
use Webkul\Attendance\Models\Attendance;
use Webkul\Attendance\Services\AttendanceService;
use Webkul\Support\Enums\NavigationGroup;

/**
 * Attendance records for managers (AT-4).
 *
 * Read-only: a record is written by clocking in, never typed in. What a
 * manager changes is a correction, which goes through the service so it is
 * audited.
 */
class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static string|BackedEnum|null $navigationIcon = 'icon-attendance';

    protected static ?int $navigationSort = 1;

    protected static string $lang = 'attendances::filament/resources/attendance';

    public static function getModelLabel(): string
    {
        return __(static::$lang.'.model.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __(static::$lang.'.model.plural');
    }

    public static function getNavigationLabel(): string
    {
        return __(static::$lang.'.navigation.title');
    }

    public static function getNavigationGroup(): string|UnitEnum
    {
        return NavigationGroup::Employee;
    }

    /**
     * An attendance row is created by clocking in. A correction edits the row
     * through the audited service, so there is nothing to create here.
     */
    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return AttendancesTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['employee:id,name', 'company:id,name']);

        $user = auth()->user();

        if ($user === null) {
            return $query->whereRaw('1 = 0');
        }

        // A team manager sees their team, an "own rows" user sees themselves.
        // Null means the user may see every row the company scope allows.
        $visible = app(AttendanceService::class)->visibleEmployeeIds($user);

        if ($visible !== null) {
            $query->whereIn('employee_id', $visible);
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAttendances::route('/'),
        ];
    }
}
