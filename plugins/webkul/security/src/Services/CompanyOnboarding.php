<?php

namespace Webkul\Security\Services;

use DateTimeImmutable;
use DateTimeZone;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Webkul\Security\Models\Company;
use Webkul\Security\Models\User;

/** Create the company and its first administrator as one atomic operation. */
class CompanyOnboarding
{
    public function __construct(
        protected SeatCapacityService $capacity,
        protected CompanyAdministratorAccount $accounts,
        protected CompanyAdministratorInviter $inviter,
    ) {}

    /**
     * The company model's created observer attaches its creator. The outer
     * transaction matters: if that seat is refused, the company and partner
     * created before the observer must roll back together (D-SAAS-03).
     *
     * @param  array<string, mixed>  $attributes
     * @return array{company: Company, outcome: string, password: bool}
     */
    public function create(
        User $actor,
        array $attributes,
        ?string $administratorEmail = null,
        ?string $administratorPassword = null,
        bool $mustChangePassword = true,
    ): array {
        if (! $actor->is_active || ! Gate::forUser($actor)->allows('create', Company::class)) {
            throw new AuthorizationException('You may not create a company.');
        }

        return DB::transaction(function () use ($actor, $attributes, $administratorEmail, $administratorPassword, $mustChangePassword): array {
            $attributes = $this->accountTypeAttributes($actor, $attributes);
            $attributes['creator_id'] = $actor->getKey();
            $company = Company::query()->create($attributes);

            $outcome = $this->capacity->withLockedCompany($company, function () use ($company, $actor, $administratorEmail, $administratorPassword, $mustChangePassword): string {
                if ($administratorPassword !== null && $administratorPassword !== '') {
                    return $this->accounts->setPassword(
                        $company, $actor, trim((string) $administratorEmail),
                        $administratorPassword, $mustChangePassword,
                    );
                }

                return $this->inviter->invite($company, $actor, $administratorEmail);
            });

            $password = $administratorPassword !== null && $administratorPassword !== '';
            $success = $password
                ? $outcome === CompanyAdministratorAccount::CREATED
                : in_array($outcome, [CompanyAdministratorInviter::INVITED, CompanyAdministratorInviter::DISABLED], true);

            if (! $success) {
                if ($outcome === CompanyAdministratorInviter::SEATS_EXHAUSTED || $outcome === CompanyAdministratorAccount::SEATS_EXHAUSTED) {
                    throw $this->capacity->capacityError(
                        (int) $this->capacity->limitFor($company),
                        $this->capacity->usedSeats($company),
                    );
                }

                // A saved company without its chosen administrator would leave
                // the onboarding form looking successful while nobody can log in.
                throw ValidationException::withMessages([
                    'administrator_email' => __('Company onboarding could not create the administrator invitation.'),
                ]);
            }

            return ['company' => $company, 'outcome' => $outcome, 'password' => $password];
        });
    }

    /**
     * The type is chosen in this same transaction (D-SAAS-11). A refusal here
     * rolls the company back with the administrator. A tenant Company Admin is
     * not offered the choice; omitting it leaves the company commercial.
     *
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    protected function accountTypeAttributes(User $actor, array $attributes): array
    {
        $choosing = array_key_exists('account_type', $attributes) || array_key_exists('test_days', $attributes);
        $accountType = (string) ($attributes['account_type'] ?? 'commercial');
        $testDays = $attributes['test_days'] ?? null;
        unset($attributes['account_type'], $attributes['test_days'], $attributes['test_ends_at']);

        if ($choosing && ! $actor->isSuperAdmin() && ! $actor->isMultiCompanyAdmin()) {
            throw new AuthorizationException('Only installation staff may choose the account type.');
        }

        if (! in_array($accountType, ['test', 'commercial'], true)) {
            throw ValidationException::withMessages([
                'account_type' => 'The account type must be test or commercial.',
            ]);
        }

        if ($accountType === 'commercial') {
            $attributes['account_type'] = 'commercial';
            $attributes['test_ends_at'] = null;

            return $attributes;
        }

        $days = filter_var($testDays, FILTER_VALIDATE_INT);

        if ($days === false || (int) $days < 1) {
            throw ValidationException::withMessages([
                'test_days' => 'A test company needs a number of days.',
            ]);
        }

        $attributes['account_type'] = 'test';
        $attributes['test_ends_at'] = (new DateTimeImmutable('now', new DateTimeZone('UTC')))->modify('+'.(int) $days.' days');

        return $attributes;
    }
}
