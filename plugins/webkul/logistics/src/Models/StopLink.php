<?php

namespace Webkul\Logistics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Logistics\Models\Concerns\InheritsParentCompany;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Traits\BelongsToCompany;

/**
 * A one-time POD capture link for a stop (D15, WP-5b). Only the token's hash is
 * stored; the plaintext token exists only in the URL handed to the driver.
 */
class StopLink extends Model
{
    use BelongsToCompany;
    use InheritsParentCompany;

    protected static string $parentCompanyModel = Stop::class;

    protected static string $parentCompanyKey = 'stop_id';

    protected $table = 'logistics_stop_links';

    protected $fillable = [
        'token_hash',
        'expires_at',
        'used_at',
        'revoked_at',
        'stop_id',
        'created_by_id',
        'company_id',
    ];

    protected $hidden = [
        'token_hash',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at'    => 'datetime',
        'revoked_at' => 'datetime',
    ];

    public function isUsable(): bool
    {
        return $this->used_at === null
            && $this->revoked_at === null
            && $this->expires_at->isFuture();
    }

    public function stop(): BelongsTo
    {
        return $this->belongsTo(Stop::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
