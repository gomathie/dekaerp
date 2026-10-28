<?php

namespace Webkul\Support\Exceptions;

use RuntimeException;

/**
 * A tenant row was about to be written with no company, by someone acting.
 *
 * `company_id = null` is not "no tenant" in this schema - `CompanyScope` reads
 * it as a *shared* row, visible to every company. A tenant record created while
 * an authenticated user is acting but no company could be resolved is therefore
 * a cross-tenant leak, not a harmless default, and it fails closed here instead
 * of being written and discovered later.
 *
 * Deliberately not thrown when nobody is acting (seeders, installers, console
 * bootstrapping): those genuinely create rows outside a request, and they set
 * the company explicitly where it matters.
 */
class MissingCompanyContextException extends RuntimeException
{
    public static function forModel(string $model): self
    {
        return new self(__('A :model cannot be saved without a company: the acting user has no active company.', [
            'model' => class_basename($model),
        ]));
    }
}
