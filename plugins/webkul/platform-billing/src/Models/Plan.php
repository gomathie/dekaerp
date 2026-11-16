<?php

namespace Webkul\PlatformBilling\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    protected $table = 'platform_billing_plans';

    protected $guarded = [];

    /** The database default must also be visible before a newly created model reloads. */
    protected $attributes = ['is_active' => true];

    protected function casts(): array
    {
        return ['included_plugins' => 'array', 'is_active' => 'boolean', 'seat_limit' => 'integer'];
    }

    public function prices(): HasMany
    {
        return $this->hasMany(Price::class);
    }
}
