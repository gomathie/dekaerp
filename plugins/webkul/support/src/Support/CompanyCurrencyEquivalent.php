<?php

namespace Webkul\Support\Support;

use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;

/**
 * The small "≈ GH₵ 44,257.50 · 1 USD = 11.80 GHS" line under a foreign-
 * currency document's total (TRACE365, user 2026-11-23: purchases stay in
 * USD, with the cedi value beside them).
 *
 * The document's own stored rate wins when it has one, so a posted
 * document shows the value its books carry, not today's. A rate is "units
 * of the document currency per one unit of the company currency", as
 * Move::$invoice_currency_rate and Purchase Order::$currency_rate store it.
 * Without a stored rate, the rate of the document's date is used. No usable
 * rate: no line, rather than a 1:1 guess.
 */
class CompanyCurrencyEquivalent
{
    public static function line(float $amount, ?Currency $currency, ?Company $company, mixed $date = null, ?float $storedRate = null): ?string
    {
        $companyCurrency = $company?->currency;

        if (! $currency || ! $companyCurrency || (int) $currency->getKey() === (int) $companyCurrency->getKey()) {
            return null;
        }

        $rate = static::rate($currency, $companyCurrency, $company, $date, $storedRate);

        if (! $rate) {
            return null;
        }

        return __('support::support.company-equivalent', [
            'amount' => money($amount / $rate, $companyCurrency->name),
            'from'   => $currency->name,
            'rate'   => number_format(1 / $rate, 2),
            'to'     => $companyCurrency->name,
        ]);
    }

    /**
     * The short form for a list row, from the company-currency amount the
     * document already stores ("≈ GH₵ 44,257.50"). Nothing for a document
     * in the company's own currency.
     */
    public static function short(float $companyAmount, mixed $documentCurrencyId, ?Company $company): ?string
    {
        $companyCurrency = $company?->currency;

        if (! $companyCurrency || ! $documentCurrencyId || (int) $documentCurrencyId === (int) $companyCurrency->getKey()) {
            return null;
        }

        return '≈ '.money(abs($companyAmount), $companyCurrency->name);
    }

    protected static function rate(Currency $currency, Currency $companyCurrency, Company $company, mixed $date, ?float $storedRate): ?float
    {
        // A stored rate of 1 between two different currencies is a document
        // booked before a rate existed, not a real rate: skip it.
        if ($storedRate && $storedRate > 0 && abs($storedRate - 1.0) > 1e-9) {
            return $storedRate;
        }

        $date = $date ? (string) (is_object($date) && method_exists($date, 'toDateString') ? $date->toDateString() : $date) : now()->toDateString();

        if (! $companyCurrency->canConvertTo($currency, $company, $date)) {
            return null;
        }

        $rate = (float) $currency->getConversionRate($companyCurrency, $currency, $company, $date);

        return $rate > 0 ? $rate : null;
    }
}
