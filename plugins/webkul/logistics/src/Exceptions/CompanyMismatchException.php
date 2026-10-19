<?php

namespace Webkul\Logistics\Exceptions;

use Webkul\Support\Exceptions\CompanyMismatchException as SupportCompanyMismatchException;

/**
 * The Logistics flavour of the shared company-mismatch error.
 *
 * Extends the Support class (moved there on 2026-09-29 with
 * `InheritsParentCompany`) so there is one exception hierarchy rather than two
 * unrelated classes with the same name: code catching the Support type also
 * catches this, whichever plugin raised it.
 *
 * `between()` is overridden only to keep the Logistics wording and its four
 * existing translations, which `Expense` still raises directly.
 */
class CompanyMismatchException extends SupportCompanyMismatchException
{
    public static function between(string $record, string $related): self
    {
        return new self(__('logistics::exceptions.company-mismatch', ['record' => $record, 'related' => $related]));
    }
}
