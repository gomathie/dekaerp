<?php

namespace Webkul\Account\Services;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Models\Move;
use Webkul\Security\Models\User;
use Webkul\Security\Services\SecurityAuditLogger;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;

/**
 * Changing a company's base currency after it has accounting entries
 * (D-CR-4, user decision 2026-11-13): allowed, but only for an active Super
 * Admin, only with a reason, and always audited with the old and new
 * currency. Posted entries keep their own currency and rate.
 *
 * Every other path is still refused by CompanyObserver. The observer lets a
 * save through only while this service holds that company, so the API,
 * imports and the plain company form cannot use the exception.
 */
class BaseCurrencyChange
{
    public const AUDIT_ACTION = 'company.base-currency-changed';

    protected static ?int $authorizedCompanyId = null;

    public static function authorizes(Company $company): bool
    {
        return static::$authorizedCompanyId !== null
            && static::$authorizedCompanyId === (int) $company->getKey();
    }

    /**
     * Posted entries the warning names. Read past the global scopes: the
     * Super Admin may be working in another company, and a scoped count would
     * then say zero and make the warning look harmless.
     */
    public function postedEntryCount(Company $company): int
    {
        return Move::withoutGlobalScopes()
            ->where('company_id', $company->getKey())
            ->where('state', MoveState::POSTED)
            ->count();
    }

    public static function mayChange(?User $user): bool
    {
        return $user instanceof User && $user->is_active && $user->isSuperAdmin();
    }

    /**
     * @throws AuthorizationException when the actor is not an active Super Admin
     * @throws ValidationException when the reason or currency is unusable
     */
    public function change(Company $company, int $currencyId, string $reason): void
    {
        // The web guard by name: Sanctum's guard can be the default, and an
        // API token must never reach this exception.
        $actor = Auth::guard('web')->user();

        if (! static::mayChange($actor)) {
            throw new AuthorizationException(__('accounts::services/base-currency-change.not-allowed'));
        }

        $reason = trim($reason);

        if ($reason === '') {
            throw ValidationException::withMessages([
                'reason' => __('accounts::services/base-currency-change.reason-required'),
            ]);
        }

        $currency = Currency::query()->whereKey($currencyId)->where('active', true)->first();

        if (! $currency instanceof Currency) {
            throw ValidationException::withMessages([
                'currency_id' => __('accounts::services/base-currency-change.currency-invalid'),
            ]);
        }

        $oldCurrencyId = (int) $company->currency_id;

        if ($oldCurrencyId === (int) $currency->getKey()) {
            throw ValidationException::withMessages([
                'currency_id' => __('accounts::services/base-currency-change.same-currency'),
            ]);
        }

        $postedEntries = $this->postedEntryCount($company);

        static::$authorizedCompanyId = (int) $company->getKey();

        try {
            $company->currency_id = $currency->getKey();
            $company->save();
        } finally {
            static::$authorizedCompanyId = null;
        }

        app(SecurityAuditLogger::class)->record(
            static::AUDIT_ACTION,
            $company,
            $actor,
            (int) $company->getKey(),
            ['currency_id' => $oldCurrencyId],
            [
                'currency_id'    => (int) $currency->getKey(),
                'reason'         => $reason,
                'posted_entries' => $postedEntries,
            ],
        );
    }
}
