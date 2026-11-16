<?php

namespace Webkul\PlatformBilling\Services;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Webkul\PlatformBilling\Models\OverdueExtension;
use Webkul\PlatformBilling\Models\Price;
use Webkul\PlatformBilling\Models\Subscription;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Services\PluginEntitlementService;
use Webkul\Security\Models\Role;
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

        return $this->activateDraft($subscription, $actor, $at, false);
    }

    /**
     * Starts the subscription the commercial switch just drafted. Refuses
     * every caller outside that switch, including an assigned Multi-Company
     * Admin activating a draft on a company that was already commercial.
     * activate() stays Super Admin (D-SAAS-06, review of 45636cfcf).
     */
    public function activateForCommercialSwitch(Subscription $subscription, User $actor, ?DateTimeImmutable $at = null): Subscription
    {
        $company = Company::withoutGlobalScopes()->findOrFail($subscription->company_id);
        $accountType = app(CompanyAccountType::class);
        $accountType->assertInsideCommercialSwitch($company);
        $accountType->assertMaySwitch($actor, $company);

        if ($company->account_type !== 'commercial' || $company->test_ends_at !== null
            || Subscription::withoutGlobalScopes()->where('company_id', $company->getKey())->whereKeyNot($subscription->getKey())->exists()) {
            throw new AuthorizationException(CompanyAccountType::ONLY_DURING_SWITCH);
        }

        return $this->activateDraft($subscription, $actor, $at, true);
    }

    protected function activateDraft(Subscription $subscription, User $actor, ?DateTimeImmutable $at, bool $commercialSwitch): Subscription
    {
        return DB::transaction(function () use ($subscription, $actor, $at, $commercialSwitch): Subscription {
            $locked = $this->locked($subscription);
            $this->assertState($locked, 'draft');
            // CompanyScope follows the selected company. A Super Admin can
            // activate a subscription for a company they are not currently
            // viewing; a scoped lookup would miss it, and would also miss an
            // active subscription that company already has.
            $company = Company::withoutGlobalScopes()->lockForUpdate()->findOrFail($locked->company_id);
            if (Subscription::withoutGlobalScopes()->where('company_id', $company->id)->where('status', 'active')->exists()) {
                throw ValidationException::withMessages(['subscription' => 'This company already has an active subscription.']);
            }

            $start = $at ?? new DateTimeImmutable('now', new DateTimeZone('UTC'));
            $period = app(SubscriptionPeriod::class)->period($start, $locked->interval_months, 0);
            $locked->forceFill([
                'status'               => 'active', 'period_start_at' => $period['start'],
                'period_end_at'        => $period['end'], 'next_invoice_at' => $period['end'],
                'cancel_at_period_end' => false,
                'anchor_at'            => $start->setTimezone(new DateTimeZone('UTC')),
                'timezone'             => $start->getTimezone()->getName(),
            ])->save();
            $this->applyPlan($locked, $company, $actor, $commercialSwitch);
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
            $locked->forceFill([
                'scheduled_price_id' => $price->id,
                'scheduled_terms'    => [
                    'plan_id'          => $price->plan_id,
                    'price_id'         => $price->id,
                    'plan_name'        => $price->plan->name,
                    'included_plugins' => $price->plan->included_plugins,
                    'currency'         => $price->currency,
                    'interval_months'  => $price->interval_months,
                    'amount_minor'     => $price->amount_minor,
                    'seat_limit'       => $price->plan->seat_limit,
                ],
            ])->save();
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
            $locked->forceFill(['cancel_at_period_end' => true, 'scheduled_price_id' => null, 'scheduled_terms' => null])->save();
            $this->audit('cancellation_scheduled', $locked, $actor);

            return $locked;
        });
    }

    public function advance(Subscription $subscription, User $actor, DateTimeImmutable $at): Subscription
    {
        $this->assertSuperAdmin($actor);

        return DB::transaction(function () use ($subscription, $actor, $at): Subscription {
            return $this->applyAdvance($this->locked($subscription), $at, $actor);
        });
    }

    /**
     * The invoice job has no signed-in user. Renewal rules stay in this service
     * so the job cannot grow a second one. A scheduled plan change still has to
     * pass PluginEntitlementService, which refuses a missing actor, so that path
     * records an active Super Admin without logging them in. Logging them in
     * would make the read-only guard treat the billing run as installation staff.
     */
    public function advanceFromScheduler(Subscription $subscription, DateTimeImmutable $at): Subscription
    {
        return DB::transaction(function () use ($subscription, $at): Subscription {
            $locked = $this->locked($subscription);
            $actor = $locked->scheduled_terms ? $this->activeSuperAdmin() : null;

            return $this->applyAdvance($locked, $at, $actor);
        });
    }

    protected function applyAdvance(Subscription $locked, DateTimeImmutable $at, ?User $actor): Subscription
    {
        $this->assertState($locked, 'active');
        if ($at < $locked->period_end_at) {
            throw ValidationException::withMessages(['period' => 'The current billing period has not ended.']);
        }
        if ($locked->cancel_at_period_end) {
            $locked->forceFill(['status' => 'expired', 'next_invoice_at' => null])->save();
            $this->audit('expired', $locked, $actor);

            return $locked;
        }
        // The subscription already names its company. A queue has no session, and
        // a Super Admin's selected company is not every company, so a scoped read
        // would hide the tenant whose plan is being renewed.
        $company = Company::withoutGlobalScopes()->lockForUpdate()->findOrFail($locked->company_id);
        if ($locked->scheduled_terms) {
            $locked->forceFill([
                ...$locked->scheduled_terms,
                'scheduled_price_id' => null,
                'scheduled_terms'    => null,
                'anchor_at'          => $locked->period_end_at,
                'timezone'           => 'UTC',
            ]);
            $this->applyPlan($locked, $company, $actor);
        }
        // Advance one period only: a late scheduler must not silently skip
        // invoice periods. Derive the boundary from the original anchor.
        $periodIndex = 1;
        $anchor = $locked->anchor_at->setTimezone(new DateTimeZone($locked->timezone));
        $end = app(SubscriptionPeriod::class)->boundary($anchor, $locked->interval_months, $periodIndex);
        while ($end <= $locked->period_end_at) {
            $periodIndex++;
            $end = app(SubscriptionPeriod::class)->boundary($anchor, $locked->interval_months, $periodIndex);
        }
        $start = $locked->period_end_at;
        $locked->forceFill(['period_start_at' => $start, 'period_end_at' => $end, 'next_invoice_at' => $end])->save();
        $this->audit('renewed', $locked, $actor);

        return $locked;
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

        return $this->recordOverdueFromScheduler($subscription, $overdueAt, $actor);
    }

    /**
     * The billing sweep calls this with no user. The grace instant is the
     * invoice's day-8 timestamp; this method does not invent a second clock.
     */
    public function recordOverdueFromScheduler(Subscription $subscription, DateTimeImmutable $overdueAt, ?User $actor = null): Subscription
    {
        return DB::transaction(function () use ($subscription, $actor, $overdueAt): Subscription {
            $locked = $this->locked($subscription);
            $this->assertState($locked, 'active');
            $locked->forceFill(['overdue_at' => $overdueAt])->save();
            $this->audit('overdue_recorded', $locked, $actor);

            return $locked;
        });
    }

    public function clearOverdue(Subscription $subscription, User $actor): Subscription
    {
        $this->assertSuperAdmin($actor);

        return DB::transaction(function () use ($subscription, $actor): Subscription {
            $locked = $this->locked($subscription);
            $this->assertState($locked, 'active');
            $locked->forceFill(['overdue_at' => null])->save();
            $this->audit('overdue_cleared', $locked, $actor);

            return $locked;
        });
    }

    public function isReadOnly(Company $company, ?DateTimeImmutable $at = null): bool
    {
        $at ??= new DateTimeImmutable('now', new DateTimeZone('UTC'));

        // A trial that has reached its own end is read-only with or without a
        // subscription (D-SAAS-11). The date is test_ends_at, not a second clock.
        // The read-only guard calls this method, so Claude should review the change.
        if ($company->account_type === 'test' && $company->test_ends_at instanceof DateTimeInterface && $at >= $company->test_ends_at) {
            return true;
        }

        // An installation still running older migrations has no subscriptions
        // table yet. Without this, any earlier migration that saves a company
        // record failed the whole upgrade (2026-11-12, local database behind
        // since August). No table means no subscription, so nothing is locked.
        if (! once(fn (): bool => Schema::hasTable((new Subscription)->getTable()))) {
            return false;
        }

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

        // Each grant starts at the later of the current deadline and the
        // moment it was given, then adds its own length. A grant after the
        // lock therefore buys that length from the grant, and a grant during
        // grace still extends the original day-10 instant. The guard calls
        // this method, so Claude should review the change.
        return $subscription->overdue_at instanceof DateTimeInterface
            && $at >= $this->overdueReadOnlyAt($subscription);
    }

    /**
     * The one read-only instant. Extensions are applied in grant order.
     */
    protected function overdueReadOnlyAt(Subscription $subscription): DateTimeImmutable
    {
        $deadline = DateTimeImmutable::createFromInterface($subscription->overdue_at)->modify('+10 days');
        $extensions = OverdueExtension::withoutGlobalScopes()
            ->whereIn('invoice_id', function ($query) use ($subscription): void {
                $query->select('id')
                    ->from('platform_billing_invoices')
                    ->where('subscription_id', $subscription->getKey());
            })
            // Only extensions granted in the current overdue episode. Without
            // this, an extension on an invoice paid months ago lengthened every
            // later grace, so extensions stacked against D-SAAS-04
            // (review of 1fecd51c2, 2026-11-10).
            ->where('granted_at', '>=', $subscription->overdue_at)
            ->orderBy('granted_at')
            ->orderBy('id')
            ->get();

        foreach ($extensions as $extension) {
            $granted = DateTimeImmutable::createFromInterface($extension->granted_at);
            $start = $granted > $deadline ? $granted : $deadline;
            $deadline = $this->addExtensionLength($start, (int) $extension->length, (string) $extension->unit);
        }

        return $deadline;
    }

    protected function addExtensionLength(DateTimeImmutable $start, int $length, string $unit): DateTimeImmutable
    {
        $length = max(0, $length);

        return match ($unit) {
            OverdueExtension::UNIT_WEEK  => $start->modify('+'.$length.' weeks'),
            OverdueExtension::UNIT_MONTH => $start->modify('+'.$length.' months'),
            default                      => $start->modify('+'.$length.' days'),
        };
    }

    protected function activeSuperAdmin(): User
    {
        $names = Role::getSuperAdminRoleNames();
        $actor = User::query()
            ->where('is_active', true)
            ->whereHas('roles', function ($query) use ($names): void {
                $query->where(function ($roles) use ($names): void {
                    foreach ($names as $name) {
                        $roles->orWhereRaw('LOWER(name) = ?', [$name]);
                    }
                });
            })
            ->orderBy('id')
            ->first();

        if (! $actor instanceof User || ! $actor->isSuperAdmin()) {
            throw new AuthorizationException('A scheduled plan change needs an active Super Admin to apply module and seat terms.');
        }

        return $actor;
    }

    protected function applyPlan(Subscription $subscription, Company $company, ?User $actor, bool $commercialSwitch = false): void
    {
        $entitlements = app(PluginEntitlementService::class);
        $names = $subscription->included_plugins ?? [];
        $ids = Plugin::query()->whereIn('name', $names)->where('is_installed', true)->pluck('id')->all();
        if (count($ids) !== count(array_unique($names))) {
            throw ValidationException::withMessages(['included_plugins' => 'Every plan module must be installed.']);
        }
        // The commercial switch passes only the plan's plugins.
        // syncForCommercialSwitch unions those with the grants already on the
        // company, so the switch cannot remove one and a caller cannot hand
        // it a replacement list. syncForCompany() itself stays Super Admin.
        if ($commercialSwitch) {
            $entitlements->syncForCommercialSwitch($company, $ids, $actor);
        } else {
            $grants = array_unique([...$entitlements->grantedPluginIds((int) $company->id), ...$ids]);
            $entitlements->syncForCompany($company, $grants, $actor);
        }
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

    protected function audit(string $event, Subscription $subscription, ?User $actor): void
    {
        app(SecurityAuditLogger::class)->record('platform_billing.subscription_'.$event, $subscription, $actor, (int) $subscription->company_id);
    }
}
