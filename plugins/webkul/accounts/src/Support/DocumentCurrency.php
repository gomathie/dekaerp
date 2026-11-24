<?php

namespace Webkul\Account\Support;

use Webkul\Account\Enums\JournalType;
use Webkul\Account\Models\Journal;
use Webkul\Account\Models\Partner;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;

/**
 * Which currency a new invoice, bill or purchase order opens in
 * (TRACE365, user 2026-11-23). One rule for every form:
 *
 *   1. the contact's own default currency, when it has one;
 *   2. else the currency set on the company's journal for that kind of
 *      document (Customer Invoices for sales, Vendor Bills for purchases),
 *      which is what Move::computeCurrencyId() already falls back to;
 *   3. else the company's own currency.
 *
 * Before, every form opened in the company currency, so a company whose
 * suppliers bill in dollars and customers pay in cedis retyped the
 * currency on nearly every document. A currency that is inactive or not
 * permitted for the company (CR-6) is skipped, never offered.
 *
 * Reads go past the global scopes and filter the company explicitly: the
 * document's company decides, not whoever is acting.
 */
class DocumentCurrency
{
    public static function for(?int $partnerId, JournalType $journalType, ?int $companyId = null): ?int
    {
        $companyId = $companyId ?: current_company_id();

        if (! $companyId) {
            return null;
        }

        $candidates = [
            static::partnerCurrency($partnerId, $companyId),
            static::journalCurrency($journalType, $companyId),
        ];

        foreach ($candidates as $currencyId) {
            if ($currencyId && static::isUsable($currencyId, $companyId)) {
                return $currencyId;
            }
        }

        return Company::withoutGlobalScopes()->whereKey($companyId)->value('currency_id');
    }

    /**
     * The contact's own currency only, when usable. Forms call this when the
     * contact changes: a contact without one leaves the currency the user
     * already chose, instead of resetting it.
     */
    public static function ofContact(?int $partnerId, ?int $companyId = null): ?int
    {
        $companyId = $companyId ?: current_company_id();

        if (! $companyId) {
            return null;
        }

        $currencyId = static::partnerCurrency($partnerId, $companyId);

        return $currencyId && static::isUsable($currencyId, $companyId) ? $currencyId : null;
    }

    protected static function partnerCurrency(?int $partnerId, int $companyId): ?int
    {
        if (! $partnerId) {
            return null;
        }

        // Only this company's contacts or shared ones: an id from another
        // tenant must not decide anything here.
        return Partner::withoutGlobalScopes()
            ->whereKey($partnerId)
            ->where(fn ($query) => $query->where('company_id', $companyId)->orWhereNull('company_id'))
            ->value('default_currency_id');
    }

    protected static function journalCurrency(JournalType $journalType, int $companyId): ?int
    {
        return Journal::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->where('type', $journalType)
            ->orderBy('sort')
            ->orderBy('id')
            ->value('currency_id');
    }

    protected static function isUsable(int $currencyId, int $companyId): bool
    {
        return Currency::query()->active()->permittedFor($companyId)->whereKey($currencyId)->exists();
    }
}
