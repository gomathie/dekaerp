<?php

namespace Webkul\Security\Services;

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
}
