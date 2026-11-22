<?php

namespace Webkul\Support\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;
use Webkul\Support\Models\CurrencyRate;

/**
 * Keeps each company's exchange rates current from a public daily source
 * (user, 2026-11-22). Without rates, a GHS price on a USD invoice used to pass
 * through unconverted, and the DRC and Zambia companies could not invoice in
 * EUR, ZAR or CDF at all (CR-6).
 *
 * A rate here means what Currency::getConversionRate() reads: units of that
 * currency per one unit of the company's own currency. So rates are written
 * per company, never shared: a shared row would mean different things to a
 * GHS company and a ZMW one.
 *
 * Rates someone entered by hand win. A day that already has a manual rate for
 * a currency is left alone, and only rows this sync wrote (no creator) are
 * ever updated.
 */
class ExchangeRateSync
{
    /**
     * Units of each currency per one unit of the provider's base currency.
     *
     * @return array<string, float>
     */
    public function fetch(): array
    {
        $provider = (string) config('exchange-rates.provider');
        $options = config("exchange-rates.providers.{$provider}");

        if (! is_array($options)) {
            throw new RuntimeException("Unknown exchange rate provider [{$provider}].");
        }

        $response = Http::timeout((int) ($options['timeout'] ?? 15))
            ->acceptJson()
            ->get((string) $options['url'])
            ->throw();

        $rates = $response->json('rates');

        // A provider error still answers 200 with result=error; trusting it
        // would write nothing useful or, worse, a partial set.
        if ($response->json('result') !== 'success' || ! is_array($rates) || $rates === []) {
            throw new RuntimeException('The exchange rate provider returned no rates.');
        }

        return collect($rates)
            ->filter(fn ($rate): bool => is_numeric($rate) && (float) $rate > 0)
            ->map(fn ($rate): float => (float) $rate)
            ->all();
    }

    /**
     * Write today's rate of every active currency for one company.
     *
     * @param  array<string, float>  $baseRates  as returned by fetch()
     * @return int rows written or updated
     */
    public function syncCompany(Company $company, array $baseRates, ?Carbon $date = null): int
    {
        $date = ($date ?? now())->toDateString();

        $companyCurrency = Currency::query()->find($company->currency_id);
        $perCompanyUnit = $companyCurrency ? ($baseRates[$companyCurrency->code] ?? null) : null;

        // A base currency the provider does not quote cannot anchor any rate.
        if (! $perCompanyUnit) {
            return 0;
        }

        $written = 0;

        $currencies = Currency::query()
            ->active()
            ->whereKeyNot($companyCurrency->getKey())
            ->get();

        DB::transaction(function () use ($currencies, $baseRates, $perCompanyUnit, $company, $date, &$written): void {
            foreach ($currencies as $currency) {
                $perBaseUnit = $baseRates[$currency->code] ?? null;

                if (! $perBaseUnit) {
                    continue;
                }

                $manual = CurrencyRate::query()
                    ->where('company_id', $company->getKey())
                    ->where('currency_id', $currency->getKey())
                    ->whereDate('name', $date)
                    ->whereNotNull('creator_id')
                    ->exists();

                if ($manual) {
                    continue;
                }

                CurrencyRate::query()->updateOrCreate(
                    [
                        'company_id'  => $company->getKey(),
                        'currency_id' => $currency->getKey(),
                        'name'        => $date,
                        'creator_id'  => null,
                    ],
                    ['rate' => round($perBaseUnit / $perCompanyUnit, 6)],
                );

                $written++;
            }
        });

        return $written;
    }
}
