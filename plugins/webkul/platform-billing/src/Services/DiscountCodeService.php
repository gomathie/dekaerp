<?php

namespace Webkul\PlatformBilling\Services;

use Carbon\CarbonImmutable;
use DateTimeImmutable;
use DateTimeZone;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Webkul\PlatformBilling\Models\DiscountCode;
use Webkul\PlatformBilling\Models\DiscountRedemption;
use Webkul\PlatformBilling\Support\Money;
use Webkul\Security\Models\User;
use Webkul\Security\Services\MultiCompanyAdminService;
use Webkul\Security\Services\SecurityAuditLogger;
use Webkul\Support\Models\Company;

class DiscountCodeService
{
    /**
     * @param  array{code: string, discount_type: string, discount_value: int, duration_type: string, duration_months?: int|null, eligibility_type: string, company_ids?: array<int, int>, max_redemptions?: int|null, redemption_ends_at?: DateTimeImmutable|null}  $attributes
     */
    public function create(User $actor, array $attributes): DiscountCode
    {
        $this->assertCanCreate($actor);
        $companyIds = $this->normalizeCompanyIds($attributes['company_ids'] ?? []);
        $eligibility = $attributes['eligibility_type'];

        if ($actor->isSuperAdmin()) {
            $this->assertEligibility($eligibility, $companyIds);
        } elseif ($actor->isMultiCompanyAdmin()) {
            // MCA codes must name an explicit, assigned set; "any company" could
            // silently make a staff-created code usable in a different tenant.
            if ($eligibility !== 'company_list' || $companyIds === []) {
                throw new AuthorizationException('A Multi-Company Admin must limit discount codes to assigned companies.');
            }
            $assignedIds = app(MultiCompanyAdminService::class)->assignedCompanyIds($actor);
            if (array_diff($companyIds, $assignedIds) !== []) {
                throw new AuthorizationException('A Multi-Company Admin may create discount codes only for assigned companies.');
            }
            $this->assertEligibility($eligibility, $companyIds);
        } else {
            throw new AuthorizationException('Only a Super Admin or Multi-Company Admin may create discount codes.');
        }

        $this->assertTerms($attributes);
        $code = mb_strtoupper(trim($attributes['code']));
        if ($code === '' || mb_strlen($code) > 100) {
            throw ValidationException::withMessages(['code' => 'A discount code of at most 100 characters is required.']);
        }

        return DB::transaction(function () use ($actor, $attributes, $companyIds, $eligibility, $code): DiscountCode {
            $discount = DiscountCode::query()->create([
                'code'               => $code,
                'discount_type'      => $attributes['discount_type'],
                'discount_value'     => $attributes['discount_value'],
                'duration_type'      => $attributes['duration_type'],
                'duration_months'    => $attributes['duration_type'] === 'months' ? $attributes['duration_months'] : null,
                'eligibility_type'   => $eligibility,
                'max_redemptions'    => $attributes['max_redemptions'] ?? null,
                'redemption_ends_at' => $attributes['redemption_ends_at'] ?? null,
                'created_by'         => $actor->getKey(),
                'is_active'          => true,
            ]);

            if ($eligibility === 'company_list') {
                $discount->eligibleCompanies()->sync($companyIds);
            }

            app(SecurityAuditLogger::class)->record(
                'platform_billing.discount_code_created',
                $discount,
                $actor,
                after: ['eligibility_type' => $eligibility, 'company_ids' => $companyIds],
            );

            return $discount;
        });
    }

    public function redeem(string $code, Company $company, User $actor, ?DateTimeImmutable $at = null): DiscountRedemption
    {
        if (! $actor->is_active) {
            throw new AuthorizationException('An inactive user cannot redeem a discount code.');
        }
        $at ??= new DateTimeImmutable('now', new DateTimeZone('UTC'));

        return DB::transaction(function () use ($code, $company, $actor, $at): DiscountRedemption {
            $lockedCompany = Company::withoutGlobalScopes()->lockForUpdate()->findOrFail($company->getKey());
            $discount = DiscountCode::query()->whereRaw('UPPER(code) = ?', [mb_strtoupper(trim($code))])->lockForUpdate()->first();

            if (! $discount || ! $discount->is_active) {
                throw ValidationException::withMessages(['code' => 'This discount code is invalid or inactive.']);
            }
            $this->assertCanRedeem($actor, $lockedCompany);

            if ($discount->redemption_ends_at && $at >= $discount->redemption_ends_at) {
                throw ValidationException::withMessages(['code' => 'This discount code has expired.']);
            }
            if ($discount->eligibility_type === 'company_list'
                && ! DB::table('platform_billing_discount_code_companies')
                    ->where('discount_code_id', $discount->id)
                    ->where('company_id', $lockedCompany->id)
                    ->exists()) {
                throw new AuthorizationException('This discount code is not available to this company.');
            }
            // The company-specific refusal comes before the global cap. Once the
            // cap is exhausted, checking the cap first tells a company that
            // already holds the code that the code is globally used up, and the
            // duplicate they actually hit is never named.
            if (DiscountRedemption::withoutGlobalScopes()->where('discount_code_id', $discount->id)->where('company_id', $lockedCompany->id)->exists()) {
                throw ValidationException::withMessages(['code' => 'This company has already redeemed this discount code.']);
            }
            if ($discount->max_redemptions !== null && $discount->redemption_count >= $discount->max_redemptions) {
                throw ValidationException::withMessages(['code' => 'This discount code has reached its redemption limit.']);
            }

            // The company row lock serializes different codes too, so two
            // simultaneous redemptions cannot stack in one tenant. A separate
            // slot row makes the invariant durable without mutating the
            // immutable redemption snapshot when a term expires or is consumed.
            $currentSlot = DB::table('platform_billing_company_discount_slots')
                ->where('company_id', $lockedCompany->id)->lockForUpdate()->first();
            if ($currentSlot) {
                $current = DiscountRedemption::withoutGlobalScopes()->findOrFail($currentSlot->redemption_id);
                if ($current->duration_ends_at === null || $at < $current->duration_ends_at) {
                    throw ValidationException::withMessages(['code' => 'This company already has an active discount.']);
                }
                DB::table('platform_billing_company_discount_slots')->where('company_id', $lockedCompany->id)->delete();
            }

            // Carbon\CarbonImmutable, not Illuminate\Support\CarbonImmutable: that
            // class does not exist, so every N-month redemption crashed before the
            // duration could be stored (the stacking test is the pin).
            $endsAt = $discount->duration_type === 'months'
                ? CarbonImmutable::instance($at)->addMonthsNoOverflow($discount->duration_months)
                : null;

            try {
                $redemption = DiscountRedemption::query()->create([
                    'discount_code_id' => $discount->id,
                    'company_id'       => $lockedCompany->id,
                    'redeemed_by'      => $actor->getKey(),
                    'code_snapshot'    => $discount->code,
                    'discount_type'    => $discount->discount_type,
                    'discount_value'   => $discount->discount_value,
                    'duration_type'    => $discount->duration_type,
                    'duration_months'  => $discount->duration_months,
                    'redeemed_at'      => $at,
                    'duration_ends_at' => $endsAt,
                ]);
            } catch (QueryException $exception) {
                // The unique key is the final guard if a request raced outside
                // the expected company/code lock path.
                if (DiscountRedemption::withoutGlobalScopes()->where('discount_code_id', $discount->id)->where('company_id', $lockedCompany->id)->exists()) {
                    throw ValidationException::withMessages(['code' => 'This company has already redeemed this discount code.']);
                }
                throw $exception;
            }

            DB::table('platform_billing_company_discount_slots')->insert([
                'company_id'    => $lockedCompany->id,
                'redemption_id' => $redemption->id,
                'reserved_at'   => $at,
            ]);
            $discount->increment('redemption_count');

            app(SecurityAuditLogger::class)->record('platform_billing.discount_redeemed', $redemption, $actor, (int) $lockedCompany->id);

            return $redemption;
        }, 3);
    }

    /**
     * Calculate the pre-tax discount in integer USD minor units. SAAS-10 passes
     * whether this invoice consumes a free month after resolving the subscription
     * calendar; the accepted decision does not define its calendar semantics.
     */
    public function calculateBeforeVat(
        DiscountRedemption $redemption,
        Money $subtotal,
        DateTimeImmutable $invoiceAt,
        int $invoiceOrdinal = 1,
        bool $freeMonthApplies = false,
    ): Money {
        if ($subtotal->currency !== 'USD') {
            throw ValidationException::withMessages(['subtotal' => 'Discounts can be applied only to USD amounts.']);
        }
        if ($subtotal->isNegative() || $invoiceOrdinal < 1) {
            throw ValidationException::withMessages(['subtotal' => 'A discount requires a non-negative invoice amount and positive invoice ordinal.']);
        }
        if ($redemption->duration_ends_at && $invoiceAt >= $redemption->duration_ends_at) {
            return Money::zero('USD');
        }
        if ($redemption->duration_type === 'one_invoice' && $invoiceOrdinal !== 1) {
            return Money::zero('USD');
        }

        return match ($redemption->discount_type) {
            'percentage'  => $subtotal->percentage($this->percentageString($redemption->discount_value)),
            'fixed_usd'   => Money::of(min($subtotal->minor, $redemption->discount_value), 'USD'),
            'free_months' => $freeMonthApplies ? $subtotal : Money::zero('USD'),
            default       => throw ValidationException::withMessages(['discount' => 'This discount type is not supported.']),
        };
    }

    /** A one-invoice redemption remains immutable; the invoice caller releases its active slot after snapshotting it. */
    public function releaseOneInvoiceSlotAfterSnapshot(DiscountRedemption $redemption): void
    {
        if ($redemption->duration_type !== 'one_invoice') {
            throw ValidationException::withMessages(['redemption' => 'Only a one-invoice discount can be consumed by an invoice.']);
        }

        DB::transaction(function () use ($redemption): void {
            Company::withoutGlobalScopes()->whereKey($redemption->company_id)->lockForUpdate()->firstOrFail();
            DB::table('platform_billing_company_discount_slots')
                ->where('company_id', $redemption->company_id)
                ->where('redemption_id', $redemption->id)
                ->delete();
        });
    }

    public function setActive(DiscountCode $discount, User $actor, bool $active): DiscountCode
    {
        if (! $actor->is_active || ! $actor->isSuperAdmin()) {
            throw new AuthorizationException('Only a Super Admin may change discount code status.');
        }

        return DB::transaction(function () use ($discount, $actor, $active): DiscountCode {
            $locked = DiscountCode::query()->lockForUpdate()->findOrFail($discount->getKey());
            $locked->forceFill(['is_active' => $active])->save();
            app(SecurityAuditLogger::class)->record('platform_billing.discount_code_status_changed', $locked, $actor, after: ['is_active' => $active]);

            return $locked;
        });
    }

    protected function assertCanRedeem(User $actor, Company $company): void
    {
        if ($actor->isSuperAdmin()) {
            return;
        }
        if ($actor->isMultiCompanyAdmin()) {
            if (app(MultiCompanyAdminService::class)->isCompanyAssigned($actor, (int) $company->getKey())) {
                return;
            }
            throw new AuthorizationException('A Multi-Company Admin may redeem discounts only for assigned companies.');
        }

        $isMember = DB::table('user_allowed_companies')
            ->where('user_id', $actor->getKey())
            ->where('company_id', $company->getKey())
            ->exists();
        $isCompanyAdmin = $actor->isCompanyAdmin((int) $company->getKey());
        if ($actor->is_active && $isMember && $isCompanyAdmin) {
            return;
        }
        // Redemption must be authorized against the target membership, not a
        // session-selected company or a global permission from another tenant.
        throw new AuthorizationException("Only this company's administrator may redeem its discount.");
    }

    protected function assertCanCreate(User $actor): void
    {
        if (! $actor->is_active || (! $actor->isSuperAdmin() && ! $actor->isMultiCompanyAdmin())) {
            throw new AuthorizationException('Only a Super Admin or Multi-Company Admin may create discount codes.');
        }
    }

    /** @param array<string, mixed> $attributes */
    protected function assertTerms(array $attributes): void
    {
        $type = $attributes['discount_type'];
        $value = $attributes['discount_value'];
        if (! in_array($type, ['percentage', 'fixed_usd', 'free_months'], true) || ! is_int($value) || $value < 1
            || ($type === 'percentage' && $value > 10000)) {
            throw ValidationException::withMessages(['discount_value' => 'The discount amount or type is invalid.']);
        }
        $duration = $attributes['duration_type'];
        $months = $attributes['duration_months'] ?? null;
        if (! in_array($duration, ['one_invoice', 'months', 'forever'], true)
            || ($duration === 'months' && (! is_int($months) || $months < 1 || $months > 1200))
            || ($duration !== 'months' && $months !== null)) {
            throw ValidationException::withMessages(['duration' => 'The discount duration is invalid.']);
        }
        if (isset($attributes['max_redemptions']) && (! is_int($attributes['max_redemptions']) || $attributes['max_redemptions'] < 1)) {
            throw ValidationException::withMessages(['max_redemptions' => 'The redemption limit must be greater than zero.']);
        }
        if (isset($attributes['redemption_ends_at']) && ! $attributes['redemption_ends_at'] instanceof DateTimeImmutable) {
            throw ValidationException::withMessages(['redemption_ends_at' => 'The redemption end date is invalid.']);
        }
    }

    protected function assertEligibility(string $eligibility, array $companyIds): void
    {
        if (! in_array($eligibility, ['any_company', 'company_list'], true)
            || ($eligibility === 'company_list' && $companyIds === [])
            || ($eligibility === 'any_company' && $companyIds !== [])) {
            throw ValidationException::withMessages(['eligibility' => 'Choose any company or a non-empty named company list.']);
        }
        if ($companyIds !== [] && Company::withoutGlobalScopes()->whereIn('id', $companyIds)->count() !== count($companyIds)) {
            throw ValidationException::withMessages(['company_ids' => 'One or more eligible companies do not exist.']);
        }
    }

    /** @param array<int, mixed> $companyIds
     * @return array<int, int>
     */
    protected function normalizeCompanyIds(array $companyIds): array
    {
        $ids = array_values(array_unique(array_map('intval', $companyIds)));
        sort($ids);

        return $ids;
    }

    protected function percentageString(int $basisPoints): string
    {
        $whole = intdiv($basisPoints, 100);
        $fraction = str_pad((string) ($basisPoints % 100), 2, '0', STR_PAD_LEFT);

        return $whole.'.'.$fraction;
    }
}
