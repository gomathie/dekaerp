<?php

namespace Webkul\PlatformBilling\Services;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Webkul\PlatformBilling\Models\Price;
use Webkul\Security\Models\User;
use Webkul\Security\Services\MultiCompanyAdminService;
use Webkul\Security\Services\SecurityAuditLogger;
use Webkul\Support\Models\Company;

/**
 * Trial length and the one-way switch to commercial (D-SAAS-11).
 *
 * Adding days is installation staff. Switching starts a subscription, which
 * D-SAAS-06 keeps with Super Admin. There is no path back to test.
 */
class CompanyAccountType
{
    public const ONLY_STAFF = 'Only a Super Admin, or a Multi-Company Admin for this company, may change the trial.';

    public const ONLY_SUPER_ADMIN = 'Only a Super Admin may switch a company to commercial.';

    public const ONLY_TEST = 'Only a test company can be given more days.';

    public const ONLY_TEST_SWITCH = 'Only a test company can be switched to commercial.';

    public const DAYS_REQUIRED = 'A test company needs a number of days.';

    public function addDays(User $actor, Company $company, int $days, DateTimeImmutable $at): Company
    {
        $this->assertMayExtend($actor, $company);

        if ($days < 1) {
            throw ValidationException::withMessages(['test_days' => self::DAYS_REQUIRED]);
        }

        return DB::transaction(function () use ($actor, $company, $days, $at): Company {
            $locked = Company::withoutGlobalScopes()->lockForUpdate()->findOrFail($company->getKey());

            if ($locked->account_type !== 'test') {
                throw ValidationException::withMessages(['account_type' => self::ONLY_TEST]);
            }

            $current = $locked->test_ends_at instanceof DateTimeInterface
                ? DateTimeImmutable::createFromInterface($locked->test_ends_at)
                : $at;
            $base = $current > $at ? $current : $at;
            $locked->forceFill([
                'test_ends_at' => $base->setTimezone(new DateTimeZone('UTC'))->modify('+'.$days.' days'),
            ])->save();

            app(SecurityAuditLogger::class)->record(
                'platform_billing.trial_extended',
                $locked,
                $actor,
                (int) $locked->getKey(),
                after: ['days' => $days, 'test_ends_at' => $locked->test_ends_at?->format(DateTimeInterface::ATOM)],
            );

            return $locked;
        });
    }

    public function switchToCommercial(User $actor, Company $company, Price $price, DateTimeImmutable $at): Company
    {
        if (! $actor->is_active || ! $actor->isSuperAdmin()) {
            throw new AuthorizationException(self::ONLY_SUPER_ADMIN);
        }

        return DB::transaction(function () use ($actor, $company, $price, $at): Company {
            $locked = Company::withoutGlobalScopes()->lockForUpdate()->findOrFail($company->getKey());

            if ($locked->account_type !== 'test') {
                throw ValidationException::withMessages(['account_type' => self::ONLY_TEST_SWITCH]);
            }

            $locked->forceFill([
                'account_type' => 'commercial',
                'test_ends_at' => null,
            ])->save();

            $draft = app(SubscriptionDrafts::class)->create($actor, $locked, $price, $at);
            $active = app(SubscriptionLifecycle::class)->activate($draft, $actor, $at);

            app(SecurityAuditLogger::class)->record(
                'platform_billing.account_became_commercial',
                $locked,
                $actor,
                (int) $locked->getKey(),
                after: ['subscription_id' => $active->getKey(), 'price_id' => $price->getKey()],
            );

            return $locked->fresh() ?? $locked;
        });
    }

    protected function assertMayExtend(User $actor, Company $company): void
    {
        if (! $actor->is_active) {
            throw new AuthorizationException(self::ONLY_STAFF);
        }

        if ($actor->isSuperAdmin()) {
            return;
        }

        if ($actor->isMultiCompanyAdmin() && app(MultiCompanyAdminService::class)->isCompanyAssigned($actor, (int) $company->getKey())) {
            return;
        }

        throw new AuthorizationException(self::ONLY_STAFF);
    }
}
