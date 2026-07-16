<?php

namespace Webkul\Support\Traits;

use Webkul\Support\Models\Scopes\CompanyScope;

/**
 * Scopes the model to the active company selected in the company switcher.
 * Alternative to listing the model in the company-scope config file — use
 * this on your own models, use the config for upstream models you don't
 * want to edit.
 */
trait BelongsToCompany
{
    public static function bootBelongsToCompany(): void
    {
        CompanyScope::applyTo(static::class);
    }
}
