<?php

namespace Webkul\PlatformBilling\Services;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Webkul\PlatformBilling\Models\Plan;
use Webkul\PlatformBilling\Models\Price;
use Webkul\PlatformBilling\Support\Money;
use Webkul\PluginManager\Models\Plugin;
use Webkul\Security\Models\User;
use Webkul\Security\Services\SecurityAuditLogger;

/**
 * Creates and deactivates plans and prices.
 *
 * The catalog had no writer. The screens must not insert these rows
 * themselves, and deactivating is not a delete: subscriptions keep the terms
 * copied at sale, and the price unique key is `(plan, currency, interval)`.
 */
class PlanCatalog
{
    public const ONLY_SUPER_ADMIN = 'Only a Super Admin may change plans.';

    public const NAME_REQUIRED = 'A plan needs a name.';

    public const CODE_REQUIRED = 'A plan needs a code.';

    public const CODE_TAKEN = 'That plan code is already in use.';

    public const SEAT_LIMIT = 'A seat limit must be a whole number, or empty for no limit.';

    public const AMOUNT_REQUIRED = 'A price needs an amount.';

    public const AMOUNT_INVALID = 'Enter the price in dollars and cents, such as 10.00.';

    public const INTERVAL = 'The billing interval must be 1, 3, 6 or 12 months.';

    /**
     * Same sentence as SubscriptionLifecycle at activation. A plan that names
     * a module which is not installed cannot be sold.
     */
    public const MODULES = 'Every plan module must be installed.';

    public const PRICE_TAKEN = 'That plan already has a price for this interval.';

    public const PLAN_INACTIVE = 'An inactive plan cannot gain a price.';

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function createPlan(User $actor, array $attributes): Plan
    {
        $this->assertSuperAdmin($actor);

        $name = trim((string) ($attributes['name'] ?? ''));
        $code = trim((string) ($attributes['code'] ?? ''));

        if ($name === '' || mb_strlen($name) > 255) {
            throw ValidationException::withMessages(['name' => self::NAME_REQUIRED]);
        }

        if ($code === '' || mb_strlen($code) > 255) {
            throw ValidationException::withMessages(['code' => self::CODE_REQUIRED]);
        }

        return DB::transaction(function () use ($actor, $attributes, $name, $code): Plan {
            if (Plan::query()->where('code', $code)->exists()) {
                throw ValidationException::withMessages(['code' => self::CODE_TAKEN]);
            }

            try {
                $plan = Plan::query()->create([
                    'name'             => $name,
                    'code'             => $code,
                    'included_plugins' => $this->modules($attributes['included_plugins'] ?? []),
                    'seat_limit'       => $this->seatLimit($attributes['seat_limit'] ?? null),
                    'is_active'        => true,
                ]);
            } catch (QueryException $exception) {
                // The exists() check above loses a race. The unique index is
                // the backstop, and the screen should show the same message.
                if ($this->isUniqueViolation($exception)) {
                    throw ValidationException::withMessages(['code' => self::CODE_TAKEN]);
                }

                throw $exception;
            }

            $this->storePrice($plan, $attributes['amount'] ?? null, $attributes['interval_months'] ?? null);
            app(SecurityAuditLogger::class)->record('platform_billing.plan_created', $plan, $actor);

            return $plan->fresh() ?? $plan;
        });
    }

    public function addPrice(User $actor, Plan $plan, mixed $amount, mixed $intervalMonths): Price
    {
        $this->assertSuperAdmin($actor);

        return DB::transaction(function () use ($actor, $plan, $amount, $intervalMonths): Price {
            $locked = Plan::query()->lockForUpdate()->findOrFail($plan->getKey());

            if (! $locked->is_active) {
                throw ValidationException::withMessages(['plan' => self::PLAN_INACTIVE]);
            }

            $price = $this->storePrice($locked, $amount, $intervalMonths);
            app(SecurityAuditLogger::class)->record('platform_billing.price_created', $price, $actor);

            return $price;
        });
    }

    public function deactivatePlan(User $actor, Plan $plan): Plan
    {
        $this->assertSuperAdmin($actor);

        return DB::transaction(function () use ($actor, $plan): Plan {
            $locked = Plan::query()->lockForUpdate()->findOrFail($plan->getKey());

            if (! $locked->is_active) {
                return $locked;
            }

            $locked->forceFill(['is_active' => false])->save();
            app(SecurityAuditLogger::class)->record(
                'platform_billing.plan_deactivated',
                $locked,
                $actor,
                before: ['is_active' => true],
                after: ['is_active' => false],
            );

            return $locked;
        });
    }

    public function deactivatePrice(User $actor, Price $price): Price
    {
        $this->assertSuperAdmin($actor);

        return DB::transaction(function () use ($actor, $price): Price {
            $locked = Price::query()->lockForUpdate()->findOrFail($price->getKey());

            if (! $locked->is_active) {
                return $locked;
            }

            $locked->forceFill(['is_active' => false])->save();
            app(SecurityAuditLogger::class)->record(
                'platform_billing.price_deactivated',
                $locked,
                $actor,
                before: ['is_active' => true],
                after: ['is_active' => false],
            );

            return $locked;
        });
    }

    protected function storePrice(Plan $plan, mixed $amount, mixed $intervalMonths): Price
    {
        $interval = $this->interval($intervalMonths);
        $currency = (string) config('platform-billing.currency');
        $minor = $this->amountMinor($amount, $currency);

        if (Price::query()
            ->where('plan_id', $plan->getKey())
            ->where('currency', $currency)
            ->where('interval_months', $interval)
            ->exists()) {
            throw ValidationException::withMessages(['interval_months' => self::PRICE_TAKEN]);
        }

        try {
            return Price::query()->create([
                'plan_id'         => $plan->getKey(),
                'currency'        => $currency,
                'interval_months' => $interval,
                'amount_minor'    => $minor,
                'is_active'       => true,
            ]);
        } catch (QueryException $exception) {
            if ($this->isUniqueViolation($exception)) {
                throw ValidationException::withMessages(['interval_months' => self::PRICE_TAKEN]);
            }

            throw $exception;
        }
    }

    /**
     * @return array<int, string>
     */
    protected function modules(mixed $names): array
    {
        if (! is_array($names)) {
            throw ValidationException::withMessages(['included_plugins' => self::MODULES]);
        }

        $modules = array_values(array_unique(array_filter(array_map(
            fn (mixed $name): string => trim((string) $name),
            $names,
        ), fn (string $name): bool => $name !== '')));

        if ($modules === []) {
            return [];
        }

        $installed = Plugin::query()
            ->where('is_installed', true)
            ->whereIn('name', $modules)
            ->pluck('name')
            ->all();

        if (count($installed) !== count($modules)) {
            throw ValidationException::withMessages(['included_plugins' => self::MODULES]);
        }

        return $modules;
    }

    protected function seatLimit(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_string($value)) {
            $value = trim($value);
        }

        // Filament's integer input can arrive as an int, a digit string, or a
        // whole-number float. Anything else is not a seat limit.
        $whole = is_int($value)
            || (is_string($value) && preg_match('/^\d+$/', $value) === 1)
            || (is_float($value) && $value >= 0 && floor($value) === $value);

        if ($whole && (int) $value >= 0) {
            return (int) $value;
        }

        throw ValidationException::withMessages(['seat_limit' => self::SEAT_LIMIT]);
    }

    protected function interval(mixed $value): int
    {
        $interval = is_int($value) ? $value : (is_string($value) && preg_match('/^\d+$/', $value) === 1 ? (int) $value : 0);

        if (! in_array($interval, [1, 3, 6, 12], true)) {
            throw ValidationException::withMessages(['interval_months' => self::INTERVAL]);
        }

        return $interval;
    }

    protected function amountMinor(mixed $amount, string $currency): int
    {
        if ($amount === null || $amount === '') {
            throw ValidationException::withMessages(['amount' => self::AMOUNT_REQUIRED]);
        }

        if (is_float($amount)) {
            throw ValidationException::withMessages(['amount' => self::AMOUNT_INVALID]);
        }

        try {
            $money = Money::fromDecimal(is_int($amount) ? (string) $amount : (string) $amount, $currency);
        } catch (\InvalidArgumentException) {
            throw ValidationException::withMessages(['amount' => self::AMOUNT_INVALID]);
        }

        if ($money->minor < 0) {
            throw ValidationException::withMessages(['amount' => self::AMOUNT_INVALID]);
        }

        return $money->minor;
    }

    protected function assertSuperAdmin(User $actor): void
    {
        if (! $actor->is_active || ! $actor->isSuperAdmin()) {
            throw new AuthorizationException(self::ONLY_SUPER_ADMIN);
        }
    }

    protected function isUniqueViolation(QueryException $exception): bool
    {
        return $exception->getCode() === '23505';
    }
}
