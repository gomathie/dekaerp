<?php

namespace Webkul\Attendance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Attendance\Exceptions\CorrectionImmutableException;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Traits\BelongsToCompany;
use Webkul\Support\Traits\InheritsParentCompany;

/**
 * An append-only note of a manager change. Payroll and audits read it;
 * nothing rewrites it.
 */
class Correction extends Model
{
    use BelongsToCompany;
    use InheritsParentCompany;

    public $timestamps = false;

    protected static string $parentCompanyModel = Attendance::class;

    protected static string $parentCompanyKey = 'attendance_id';

    protected $table = 'attendances_corrections';

    protected $fillable = [
        'company_id',
        'attendance_id',
        'before',
        'after',
        'reason',
        'actor_id',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'before' => 'array',
            'after' => 'array',
            'created_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::updating(function (): void {
            // A later edit would hide what the manager actually changed.
            throw new CorrectionImmutableException;
        });

        static::deleting(function (): void {
            throw new CorrectionImmutableException;
        });
    }

    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class);
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
