<?php

namespace Webkul\Account\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

/**
 * One change of a company's lock date. Rows are append-only: read the current
 * lock through Webkul\Account\Services\PeriodLock, never by updating a row.
 *
 * Deliberately not company-scoped: the lock is read inside model hooks, for
 * whoever is acting (a queued job, another company's staff), and a scoped read
 * there would silently find no lock and let the write through.
 */
class LockDate extends Model
{
    protected $table = 'accounts_lock_dates';

    protected $fillable = [
        'company_id',
        'lock_date',
        'previous_lock_date',
        'changed_by',
    ];

    protected function casts(): array
    {
        return [
            'lock_date'          => 'date',
            'previous_lock_date' => 'date',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
