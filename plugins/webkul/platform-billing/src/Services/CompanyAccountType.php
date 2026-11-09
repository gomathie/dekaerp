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
 * Adding days and switching are the same people: a Super Admin, or a
 * Multi-Company Admin assigned to that company (user, 2026-11-09). The
 * switch is the one subscription write that assignment covers. Every other
 * subscription change stays Super Admin (D-SAAS-06). There is no path back
 * to test.
 */
class CompanyAccountType
{
    public const ONLY_STAFF = 'Only a Super Admin, or a Multi-Company Admin for this company, may change the trial.';

    public const ONLY_SWITCH_STAFF = 'Only a Super Admin, or a Multi-Company Admin for this company, may switch it to commercial.';

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
        $this->assertMaySwitch($actor, $company);

        return DB::transaction(function () use ($actor, $company, $price, $at): Company {
            $locked = Company::withoutGlobalScopes()->lockForUpdate()->findOrFail($company->getKey());

            if ($locked->account_type !== 'test') {
                throw ValidationException::withMessages(['account_type' => self::ONLY_TEST_SWITCH]);
            }

            $locked->forceFill([
                'account_type' => 'commercial',
                'test_ends_at' => null,
            ])->save();

            // create() and activate() stay Super Admin. These two entries are
            // the commercial switch only, so an assigned Multi-Company Admin
            // cannot open the rest of subscription administration.
            $draft = app(SubscriptionDrafts::class)->createForCommercialSwitch($actor, $locked, $price, $at);
            $active = app(SubscriptionLifecycle::class)->activateForCommercialSwitch($draft, $actor, $at);

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

    public function assertMaySwitch(User $actor, Company $company): void
    {
        if (! app(MultiCompanyAdminService::class)->mayAdministerCompany($actor, (int) $company->getKey())) {
            throw new AuthorizationException(self::ONLY_SWITCH_STAFF);
        }
    }

    protected function assertMayExtend(User $actor, Company $company): void
    {
        if (! app(MultiCompanyAdminService::class)->mayAdministerCompany($actor, (int) $company->getKey())) {
            throw new AuthorizationException(self::ONLY_STAFF);
        }
    }
}
