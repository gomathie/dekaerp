<?php

namespace Webkul\PlatformBilling\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Webkul\PlatformBilling\Models\Subscription;
use Webkul\PlatformBilling\Services\SubscriptionLifecycle;
use Webkul\Security\Services\SeatCapacityService;
use Webkul\Support\Models\Company;

/**
 * Read-only report of what billing enforcement would do to each company.
 *
 * Replaces the planned shadow mode (SAAS-14, user decision 2026-11-10):
 * production had no subscriptions and no seat limits, so a shadow run would
 * have compared against nothing. Run this before switching billing on for
 * real companies and read the "Read-only now" and "Over limit" columns.
 *
 * It writes nothing and reads through the same services the guards use, so
 * the answer cannot drift from the enforced rule.
 *
 *     php artisan platform-billing:readiness-report
 */
class BillingReadinessReport extends Command
{
    protected $signature = 'platform-billing:readiness-report';

    protected $description = 'Report seats, modules, subscription and read-only state per company (read-only)';

    public function handle(SeatCapacityService $seats, SubscriptionLifecycle $lifecycle): int
    {
        $companies = Company::withoutGlobalScopes()->whereNull('deleted_at')->orderBy('id')->get();

        if ($companies->isEmpty()) {
            $this->components->info('There are no companies.');

            return self::SUCCESS;
        }

        $grants = DB::table('company_plugin')
            ->select('company_id', DB::raw('count(*) as total'))
            ->groupBy('company_id')
            ->pluck('total', 'company_id');

        $rows = $companies->map(function (Company $company) use ($seats, $lifecycle, $grants): array {
            $subscription = Subscription::withoutGlobalScopes()
                ->where('company_id', $company->getKey())
                ->latest('id')
                ->first();

            $limit = $seats->limitFor($company);

            return [
                $company->getKey(),
                $company->name,
                $company->account_type ?? 'commercial',
                $company->test_ends_at?->format('Y-m-d') ?? '-',
                $seats->usedSeats($company).' / '.($limit ?? 'unlimited'),
                $seats->isOverLimit($company) ? 'YES' : 'no',
                (int) ($grants[$company->getKey()] ?? 0),
                $subscription?->status ?? 'none',
                $lifecycle->isReadOnly($company) ? 'YES' : 'no',
            ];
        });

        $this->table(
            ['ID', 'Company', 'Type', 'Test ends', 'Seats', 'Over limit', 'Modules', 'Subscription', 'Read-only now'],
            $rows->all(),
        );

        $readOnly = $rows->where(8, 'YES')->count();
        $overLimit = $rows->where(5, 'YES')->count();

        $this->components->info(sprintf(
            '%d companies: %d read-only now, %d over their seat limit.',
            $rows->count(),
            $readOnly,
            $overLimit,
        ));

        return self::SUCCESS;
    }
}
