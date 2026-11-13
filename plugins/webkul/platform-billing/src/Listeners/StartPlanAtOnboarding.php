<?php

namespace Webkul\PlatformBilling\Listeners;

use DateTimeImmutable;
use DateTimeZone;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;
use Webkul\PlatformBilling\Models\Price;
use Webkul\PlatformBilling\Services\SubscriptionDrafts;
use Webkul\PlatformBilling\Services\SubscriptionLifecycle;
use Webkul\Security\Events\CompanyOnboarded;

/**
 * CR-4 (comprehensive review plan): the plan is chosen on the create-company
 * form, so the new company is commercially complete in one step instead of a
 * second trip to Subscriptions.
 *
 * Runs inside the onboarding transaction. It reuses the Super Admin path
 * (`SubscriptionDrafts::create()` + `SubscriptionLifecycle::activate()`),
 * which applies the plan's modules and seat limit; both refuse anyone else,
 * so a forged form value from a Multi-Company Admin rolls the company back
 * rather than starting a subscription. A test company never gets a plan here:
 * it becomes commercial later through the existing switch.
 */
class StartPlanAtOnboarding
{
    public const ONLY_COMMERCIAL = 'A test company cannot start a plan. Switch it to commercial later.';

    public const UNKNOWN_PRICE = 'The chosen plan price is not available.';

    public function handle(CompanyOnboarded $event): void
    {
        $priceId = $event->options['onboarding_price_id'] ?? null;

        if ($priceId === null || $priceId === '') {
            return;
        }

        if (($event->company->fresh()?->account_type ?? 'commercial') === 'test') {
            throw ValidationException::withMessages(['onboarding_price_id' => self::ONLY_COMMERCIAL]);
        }

        if (! $event->actor->isSuperAdmin()) {
            throw new AuthorizationException('Only a Super Admin may change subscriptions.');
        }

        $price = Price::query()->find($priceId);

        if (! $price instanceof Price) {
            throw ValidationException::withMessages(['onboarding_price_id' => self::UNKNOWN_PRICE]);
        }

        $now = new DateTimeImmutable('now', new DateTimeZone('UTC'));
        $draft = app(SubscriptionDrafts::class)->create($event->actor, $event->company, $price, $now);
        app(SubscriptionLifecycle::class)->activate($draft, $event->actor, $now);
    }
}
