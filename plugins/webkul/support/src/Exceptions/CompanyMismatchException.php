<?php

namespace Webkul\Support\Exceptions;

use LogicException;

/**
 * A child row was saved with a company other than its parent's.
 *
 * Moved here from `Webkul\Logistics\Exceptions` on 2026-09-29, together with
 * `InheritsParentCompany`, so that core plugins can use the rule without
 * depending on an optional one. The Logistics subclass is kept as an alias for
 * anything still catching the old class name.
 */
class CompanyMismatchException extends LogicException
{
    public static function between(string $record, string $related): self
    {
        return new self(__('support::exceptions.company-mismatch', ['record' => $record, 'related' => $related]));
    }
}
