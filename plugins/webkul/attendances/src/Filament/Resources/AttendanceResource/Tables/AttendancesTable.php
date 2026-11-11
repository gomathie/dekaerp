<?php

namespace Webkul\Attendance\Filament\Resources\AttendanceResource\Tables;

use Filament\Actions\ExportAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Webkul\Attendance\Filament\Exports\AttendanceExporter;
use Webkul\Attendance\Filament\Resources\AttendanceResource\Actions\CorrectAttendanceAction;
use Webkul\Attendance\Models\Attendance;
use Webkul\Employee\Models\Employee;

class AttendancesTable
{
    protected static string $lang = 'attendances::filament/resources/attendance';

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee.name')
                    ->label(__(static::$lang.'.table.columns.employee'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('check_in_at')
                    ->label(__(static::$lang.'.table.columns.check-in'))
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),

                TextColumn::make('check_out_at')
                    ->label(__(static::$lang.'.table.columns.check-out'))
                    ->dateTime('Y-m-d H:i')
                    ->placeholder('—')
                    ->sortable(),

                TextColumn::make('worked_minutes')
                    ->label(__(static::$lang.'.table.columns.worked'))
                    ->state(fn (Attendance $record): string => static::duration($record->worked_minutes))
                    ->placeholder('—'),

                IconColumn::make('needs_review')
                    ->label(__(static::$lang.'.table.columns.review'))
                    ->boolean()
                    ->visibleFrom('md'),

                IconColumn::make('on_leave')
                    ->label(__(static::$lang.'.table.columns.on-leave'))
                    ->boolean()
                    ->visibleFrom('md'),

                TextColumn::make('source_in')
                    ->label(__(static::$lang.'.table.columns.source'))
                    ->badge()
                    ->visibleFrom('lg'),

                TextColumn::make('company.name')
                    ->label(__(static::$lang.'.table.columns.company'))
                    ->visibleFrom('lg'),
            ])
            ->filters([
                Filter::make('open')
                    ->label(__(static::$lang.'.table.filters.open'))
                    ->toggle()
                    // "Who is in now": the one-open index makes this exact.
                    ->query(fn (Builder $query): Builder => $query->whereNull('check_out_at')),

                Filter::make('needs_review')
                    ->label(__(static::$lang.'.table.filters.needs-review'))
                    ->toggle()
                    ->query(fn (Builder $query): Builder => $query->where('needs_review', true)),

                SelectFilter::make('employee')
                    ->label(__(static::$lang.'.table.filters.employee'))
                    ->options(fn (): array => Employee::query()->orderBy('name')->pluck('name', 'id')->all())
                    ->searchable(),

                Filter::make('day')
                    ->label(__(static::$lang.'.table.filters.day'))
                    ->schema([
                        \Filament\Forms\Components\DatePicker::make('on')
                            ->label(__(static::$lang.'.table.filters.on')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $day = $data['on'] ?? null;

                        return $day === null
                            ? $query
                            : $query->whereDate('check_in_at', Carbon::parse($day)->toDateString());
                    }),
            ])
            ->recordActions([
                CorrectAttendanceAction::make(),
            ])
            ->headerActions([
                ExportAction::make()
                    ->label(__(static::$lang.'.actions.export'))
                    ->icon('heroicon-o-arrow-up-tray')
                    // Location and IP are personal data: they are never in the
                    // file unless someone deliberately turns those columns on.
                    ->visible(fn (): bool => auth()->user()?->can('export_attendance_attendance') ?? false)
                    ->exporter(AttendanceExporter::class),
            ])
            ->defaultSort('check_in_at', 'desc');
    }

    protected static function duration(?int $minutes): ?string
    {
        if ($minutes === null) {
            return null;
        }

        return sprintf('%dh %02dm', intdiv($minutes, 60), $minutes % 60);
    }
}
