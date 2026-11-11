<?php

namespace Webkul\Attendance\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Webkul\Attendance\Database\Factories\AttendanceFactory;
use Webkul\Attendance\Enums\Source;
use Webkul\Attendance\Services\ScheduleReader;
use Webkul\Employee\Models\Employee;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Traits\BelongsToCompany;
use Webkul\Support\Traits\InheritsParentCompany;

/**
 * One stretch of presence for an employee. Times are UTC.
 */
class Attendance extends Model
{
    use BelongsToCompany;
    use HasFactory;
    use InheritsParentCompany;

    protected static string $parentCompanyModel = Employee::class;

    protected static string $parentCompanyKey = 'employee_id';

    protected $table = 'attendances_attendances';

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'is_corrected' => false,
        'needs_review' => false,
        'on_leave'     => false,
        'source_in'    => 'banner',
    ];

    protected $fillable = [
        'company_id',
        'employee_id',
        'check_in_at',
        'check_out_at',
        'worked_minutes',
        'source_in',
        'source_out',
        'latitude_in',
        'longitude_in',
        'accuracy_in',
        'latitude_out',
        'longitude_out',
        'accuracy_out',
        'ip_in',
        'ip_out',
        'note',
        'is_corrected',
        'needs_review',
        'on_leave',
        'corrected_by_id',
        'creator_id',
    ];

    protected function casts(): array
    {
        return [
            'check_in_at'    => 'datetime',
            'check_out_at'   => 'datetime',
            'worked_minutes' => 'integer',
            'source_in'      => Source::class,
            'source_out'     => Source::class,
            'latitude_in'    => 'float',
            'longitude_in'   => 'float',
            'accuracy_in'    => 'float',
            'latitude_out'   => 'float',
            'longitude_out'  => 'float',
            'accuracy_out'   => 'float',
            'is_corrected'   => 'boolean',
            'needs_review'   => 'boolean',
            'on_leave'       => 'boolean',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function correctedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'corrected_by_id');
    }

    public function corrections(): HasMany
    {
        return $this->hasMany(Correction::class);
    }

    public function isOpen(): bool
    {
        return $this->check_out_at === null;
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereNull('check_out_at');
    }

    public function displayTimezone(): string
    {
        $employee = $this->employee;

        if ($employee instanceof Employee) {
            // One rule, one place: the employee's own zone, their calendar's,
            // then the application's.
            return app(ScheduleReader::class)->timezone($employee);
        }

        return (string) config('app.timezone');
    }

    public function checkInLocal(): ?Carbon
    {
        return $this->check_in_at?->copy()->timezone($this->displayTimezone());
    }

    protected static function newFactory(): AttendanceFactory
    {
        return AttendanceFactory::new();
    }
}
