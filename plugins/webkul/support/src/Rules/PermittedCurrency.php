<?php

namespace Webkul\Support\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Webkul\Support\Models\Currency;

/**
 * A document's currency must be one its company may use (CR-6, 2026-11-13).
 *
 * The screens already offer only the permitted list; without this rule the
 * API and imports still accepted any currency, so the list could be walked
 * around by anyone with a token. One rule, `Currency::permittedFor()`: a
 * company with no list keeps every currency.
 *
 * The currency must be permitted for every company given, which is the
 * request's active company plus any `company_id` in the body. Checking only
 * the body's company would let a caller name a company with a wider list.
 */
class PermittedCurrency implements ValidationRule
{
    /** @var list<int> */
    protected array $companyIds;

    public function __construct(mixed ...$companyIds)
    {
        $this->companyIds = collect($companyIds)
            ->filter(fn ($id): bool => is_numeric($id) && (int) $id > 0)
            ->map(fn ($id): int => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    public static function forRequest(mixed $bodyCompanyId = null): self
    {
        return new self(current_company_id(), $bodyCompanyId);
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_numeric($value)) {
            return;
        }

        foreach ($this->companyIds as $companyId) {
            $permitted = Currency::query()
                ->whereKey((int) $value)
                ->permittedFor($companyId)
                ->exists();

            if (! $permitted) {
                $fail(__('support::exceptions.currency-not-permitted'));

                return;
            }
        }
    }
}
