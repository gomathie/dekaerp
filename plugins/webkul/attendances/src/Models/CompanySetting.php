<?php

namespace Webkul\Attendance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Attendance\Enums\CaptureLocation;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Scopes\CompanyScope;
use Webkul\Support\Traits\BelongsToCompany;

/**
 * Per-company attendance switch. One row per company, default off.
 */
class CompanySetting extends Model
{
    use BelongsToCompany;

    protected $table = 'attendances_company_settings';

    /**
     * Mirrors the column defaults. forCompany() returns an unsaved row when
     * the company has none, and without these the 12 hour auto check-out and
     * the off location switch would read as null and do nothing.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'is_enabled'                 => false,
        'late_tolerance_minutes'     => 0,
        'overtime_tolerance_minutes' => 0,
        'auto_checkout_after_hours'  => 12,
        'capture_location'           => 'off',
        'kiosk_enabled'              => false,
    ];

    protected $fillable = [
        'company_id',
        'is_enabled',
        'enabled_at',
        'disabled_at',
        'enabled_by_id',
        'late_tolerance_minutes',
        'overtime_tolerance_minutes',
        'auto_checkout_after_hours',
        'capture_location',
        'kiosk_enabled',
    ];

    protected function casts(): array
    {
        return [
            'is_enabled'                 => 'boolean',
            'enabled_at'                 => 'datetime',
            'disabled_at'                => 'datetime',
            'late_tolerance_minutes'     => 'integer',
            'overtime_tolerance_minutes' => 'integer',
            'auto_checkout_after_hours'  => 'integer',
            'capture_location'           => CaptureLocation::class,
            'kiosk_enabled'              => 'boolean',
        ];
    }

    public static function forCompany(int $companyId): self
    {
        return static::withoutGlobalScope(CompanyScope::class)
            ->where('company_id', $companyId)
            ->first()
            ?? new static(['company_id' => $companyId]);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function enabledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'enabled_by_id');
    }
}
