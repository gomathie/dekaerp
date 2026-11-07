<?php

namespace Webkul\PlatformBilling\Services;

use DateTimeImmutable;
use DateTimeZone;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Webkul\PlatformBilling\Models\Price;
use Webkul\PlatformBilling\Models\Subscription;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Services\PluginEntitlementService;
use Webkul\Security\Models\User;
use Webkul\Security\Services\SeatCapacityService;
use Webkul\Security\Services\SecurityAuditLogger;
use Webkul\Support\Models\Company;

/** D-SAAS-09: one owner for subscription transitions and their side effects. */
class SubscriptionLifecycle
{
    public function activate(Subscription $subscription, User $actor, ?DateTimeImmutable $at = null): Subscription
    {
        $this->assertSuperAdmin($actor);

        return DB::transaction(function () use ($subscription, $actor, $at): Subscription {
            $locked = $this->locked($subscription);
            $this->assertState($locked, 'draft');
            $company = Company::query()->lockForUpdate()->findOrFail($locked->company_id);
            if (Subscription::query()->where('company_id', $company->id)->where('status', 'active')->exists()) {
                throw ValidationException::withMessages(['subscription' => 'This company already has an active subscription.']);
            }

            $start = $at ?? new DateTimeImmutable('now', new DateTimeZone('UTC'));
            $period = app(SubscriptionPeriod::class)->period($start, $locked->interval_months, 0);
            $locked->forceFill([
                'status' => 'active', 'period_start_at' => $period['start'],
                'period_end_at' => $period['end'], 'next_invoice_at' => $period['end'],
                'cancel_at_period_end' => false,
            ])->save();
            $this->applyPlan($locked, $company, $actor);
            $this->audit('activated', $locked, $actor);

            return $locked;
        });
    }

    public function schedulePrice(Subscription $subscription, Price $price, User $actor): Subscription
    {
        $this->assertSuperAdmin($actor);

        return DB::transaction(function () use ($subscription, $price, $actor): Subscription {
            $locked = $this->locked($subscription);
            $this->assertState($locked, 'active');
            $price->loadMissing('plan');
            if (! $price->exists || ! $price->is_active || ! $price->plan?->is_active || $price->currency !== 'USD') {
                throw ValidationException::withMessages(['price' => 'An active USD plan price is required.']);
            }
            // The sold snapshot and current-period access stay fixed until renewal.
            $locked->forceFill(['scheduled_price_id' => $price->id])->save();
            $this->audit('price_scheduled', $locked, $actor);

            return $locked;
        });
    }

    public function cancelAtPeriodEnd(Subscription $subscription, User $actor): Subscription
    {
        $this->assertSuperAdmin($actor);

        return DB::transaction(function () use ($subscription, $actor): Subscription {
            $locked = $this->locked($subscription);
            $this->assertState($locked, 'active');
            $locked->forceFill(['cancel_at_period_end' => true, 'scheduled_price_id' => null])->save();
            $this->audit('cancellation_scheduled', $locked, $actor);

            return $locked;
        });
    }

    public function advance(Subscription $subscription, User $actor, DateTimeImmutable $at): Subscription
    {
        $this->assertSuperAdmin($actor);

        return DB::transaction(function () use ($subscription, $actor, $at): Subscription {
            $locked = $this->locked($subscription);
            $this->assertState($locked, 'active');
            if ($at < $locked->period_end_at) {
                throw ValidationException::withMessages(['period' => 'The current billing period has not ended.']);
            }
            if ($locked->cancel_at_period_end) {
                $locked->forceFill(['status' => 'expired', 'next_invoice_at' => null])->save();
                $this->audit('expired', $locked, $actor);

                return $locked;
            }
            $company = Company::query()->lockForUpdate()->findOrFail($locked->company_id);
            if ($locked->scheduled_price_id) {
                $price = Price::query()->with('plan')->findOrFail($locked->scheduled_price_id);
                $locked->forceFill([
                    'plan_id' => $price->plan_id, 'price_id' => $price->id,
                    'plan_name' => $price->plan->name, 'included_plugins' => $price->plan->included_plugins,
                    'currency' => $price->currency, 'interval_months' => $price->interval_months,
                    'amount_minor' => $price->amount_minor, 'seat_limit' => $price->plan->seat_limit,
                    'scheduled_price_id' => null, 'anchor_at' => $locked->period_end_at,
                    'timezone' => 'UTC',
                ]);
                $this->applyPlan($locked, $company, $actor);
            }
            // Walk from the original anchor when terms are unchanged; avoid month-end drift.
            $periodIndex = 1;
            $anchor = $locked->anchor_at->setTimezone(new DateTimeZone($locked->timezone));
            $end = app(SubscriptionPeriod::class)->boundary($anchor, $locked->interval_months, $periodIndex);
            while ($end <= $at) {
                $periodIndex++;
                $end = app(SubscriptionPeriod::class)->boundary($anchor, $locked->interval_months, $periodIndex);
            }
            $start = app(SubscriptionPeriod::class)->boundary($anchor, $locked->interval_months, $periodIndex - 1);
            $locked->forceFill(['period_start_at' => $start, 'period_end_at' => $end, 'next_invoice_at' => $end])->save();
            $this->audit('renewed', $locked, $actor);

            return $locked;
        });
    }

    public function reactivate(Subscription $subscription, User $actor, ?DateTimeImmutable $at = null): Subscription
    {
        $this->assertSuperAdmin($actor);
        return DB::transaction(function () use ($subscription, $actor, $at): Subscription {
            $fresh = $this->locked($subscription);
            if ($fresh->status === 'active' && $fresh->cancel_at_period_end) {
                $fresh->forceFill(['cancel_at_period_end' => false])->save();
                $this->audit('reactivated', $fresh, $actor);

                return $fresh;
            }
            $this->assertState($fresh, 'expired');
            $fresh->forceFill(['status' => 'draft'])->save();

            return $this->activate($fresh, $actor, $at);
        });
    }

    public function recordOverdue(Subscription $subscription, User $actor, DateTimeImmutable $overdueAt): Subscription
    {
        $this->assertSuperAdmin($actor);

        return DB::transaction(function () use ($subscription, $actor, $overdueAt): Subscription {
            $locked = $this->locked($subscription);
            $this->assertState($locked, 'active');
            $locked->forceFill(['overdue_at' => $overdueAt])->save();
            $this->audit('overdue_recorded', $locked, $actor);

            return $locked;
        });
    }

    public function isReadOnly(Company $company, ?DateTimeImmutable $at = null): bool
    {
        $at ??= new DateTimeImmutable('now', new DateTimeZone('UTC'));
        $subscription = Subscription::withoutGlobalScopes()->where('company_id', $company->id)
            ->whereIn('status', ['active', 'expired'])->orderByRaw("CASE WHEN status = 'active' THEN 0 ELSE 1 END")
            ->latest('id')->first();
        if (! $subscription || $subscription->status === 'draft') {
            return false;
        }
        if ($subscription->status === 'expired') {
            return true;
        }
        if ($subscription->cancel_at_period_end && $at >= $subscription->period_end_at) {
            return true;
        }

        return $subscription->overdue_at && $at >= $subscription->overdue_at->modify('+10 days');
    }

    protected function applyPlan(Subscription $subscription, Company $company, User $actor): void
    {
        $entitlements = app(PluginEntitlementService::class);
        $names = $subscription->included_plugins ?? [];
        $ids = Plugin::query()->whereIn('name', $names)->where('is_installed', true)->pluck('id')->all();
        if (count($ids) !== count(array_unique($names))) {
            throw ValidationException::withMessages(['included_plugins' => 'Every plan module must be installed.']);
        }
        // Union before sync preserves manual grants; the service adds dependencies.
        $entitlements->syncForCompany($company, array_unique([...$entitlements->grantedPluginIds((int) $company->id), ...$ids]), $actor);
        app(SeatCapacityService::class)->setLimit($company, $subscription->seat_limit, $actor);
    }

    protected function locked(Subscription $subscription): Subscription
    {
        return Subscription::withoutGlobalScopes()->lockForUpdate()->findOrFail($subscription->id);
    }

    protected function assertSuperAdmin(User $actor): void
    {
        if (! $actor->is_active || ! $actor->isSuperAdmin()) {
            throw new AuthorizationException('Only a Super Admin may change subscriptions.');
        }
    }

    protected function assertState(Subscription $subscription, string $state): void
    {
        if ($subscription->status !== $state) {
            throw ValidationException::withMessages(['subscription' => "Subscription must be {$state}."]);
        }
    }

    protected function audit(string $event, Subscription $subscription, User $actor): void
    {
        app(SecurityAuditLogger::class)->record('platform_billing.subscription_'.$event, $subscription, $actor, (int) $subscription->company_id);
    }
}
