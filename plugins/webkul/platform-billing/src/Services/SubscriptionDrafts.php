<?php

namespace Webkul\PlatformBilling\Services;

use DateTimeImmutable;
use DateTimeZone;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Webkul\PlatformBilling\Models\Price;
use Webkul\PlatformBilling\Models\Subscription;
use Webkul\Security\Models\User;
use Webkul\Security\Services\SecurityAuditLogger;
use Webkul\Support\Models\Company;

/**
 * Records a proposed deal without activating it. D-SAAS-06 reserves billing
 * changes for Super Admin; effective entitlement rules remain undecided.
 */
class SubscriptionDrafts
{
    public function create(User $actor, Company $company, Price $price, DateTimeImmutable $anchor): Subscription
    {
        if (! $actor->isSuperAdmin()) {
            throw new AuthorizationException('Only a Super Admin may change subscriptions.');
        }

        return $this->store($actor, $company, $price, $anchor);
    }

    /**
     * The commercial switch is the one subscription a Multi-Company Admin may
     * start, and only for an assigned company (user, 2026-11-09). create()
     * stays Super Admin so this does not become general subscription access.
     */
    public function createForCommercialSwitch(User $actor, Company $company, Price $price, DateTimeImmutable $anchor): Subscription
    {
        app(CompanyAccountType::class)->assertMaySwitch($actor, $company);

        return $this->store($actor, $company, $price, $anchor);
    }

    protected function store(User $actor, Company $company, Price $price, DateTimeImmutable $anchor): Subscription
    {
        $price->loadMissing('plan');

        if (! $price->exists || ! $price->plan || ! $price->is_active || ! $price->plan->is_active
            || $price->currency !== config('platform-billing.currency')
            || ! in_array($price->interval_months, [1, 3, 6, 12], true)) {
            throw new InvalidArgumentException('An active USD plan price is required.');
        }

        return DB::transaction(function () use ($actor, $company, $price, $anchor): Subscription {
            $subscription = Subscription::query()->create([
                'company_id' => $company->getKey(),
                'plan_id' => $price->plan_id,
                'price_id' => $price->getKey(),
                'status' => 'draft',
                'plan_name' => $price->plan->name,
                'included_plugins' => $price->plan->included_plugins,
                'currency' => $price->currency,
                'interval_months' => $price->interval_months,
                'amount_minor' => $price->amount_minor,
                'seat_limit' => $price->plan->seat_limit,
                'timezone' => $anchor->getTimezone()->getName(),
                'anchor_at' => $anchor->setTimezone(new DateTimeZone('UTC')),
            ]);

            app(SecurityAuditLogger::class)->record(
                'platform_billing.subscription_draft_created',
                $subscription,
                $actor,
                (int) $company->getKey(),
                after: ['plan_id' => $price->plan_id, 'price_id' => $price->getKey()],
            );

            return $subscription;
        });
    }
}
