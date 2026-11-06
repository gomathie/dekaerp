<?php

namespace Webkul\PlatformBilling\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    protected $table = 'platform_billing_plans';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['included_plugins' => 'array', 'is_active' => 'boolean'];
    }

    public function prices(): HasMany
    {
        return $this->hasMany(Price::class);
    }
}
