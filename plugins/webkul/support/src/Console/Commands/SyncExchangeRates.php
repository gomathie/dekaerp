<?php

namespace Webkul\Support\Console\Commands;

use Illuminate\Console\Command;
use Throwable;
use Webkul\Support\Models\Company;
use Webkul\Support\Services\ExchangeRateSync;

/**
 * Daily exchange rates for every company (user, 2026-11-22). Scheduled by
 * SupportServiceProvider; safe to run again the same day, which only updates
 * the rows it wrote.
 */
class SyncExchangeRates extends Command
{
    protected $signature = 'currencies:sync-rates {--company=* : Only these company ids}';

    protected $description = 'Fetch today\'s exchange rates and store them for each company';

    public function handle(ExchangeRateSync $sync): int
    {
        if (! config('exchange-rates.enabled')) {
            $this->info('Exchange rate sync is switched off (EXCHANGE_RATES_SYNC).');

            return self::SUCCESS;
        }

        try {
            $rates = $sync->fetch();
        } catch (Throwable $exception) {
            // Reported so a stalled source is seen; nothing was written, and
            // yesterday's rates stay in force.
            report($exception);
            $this->error('Could not fetch exchange rates: '.$exception->getMessage());

            return self::FAILURE;
        }

        // Every company, whoever runs this: there is no acting user in the
        // scheduler, and each row carries its company explicitly.
        $companies = Company::query()
            ->withoutGlobalScopes()
            ->whereNull('deleted_at')
            ->whereNotNull('currency_id')
            ->when($this->option('company'), fn ($query, array $ids) => $query->whereIn('id', $ids))
            ->get();

        $total = 0;

        foreach ($companies as $company) {
            $total += $sync->syncCompany($company, $rates);
        }

        $this->info("{$total} rate(s) stored for {$companies->count()} company(ies).");

        return self::SUCCESS;
    }
}
