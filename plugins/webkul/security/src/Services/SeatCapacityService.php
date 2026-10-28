<?php

namespace Webkul\Security\Services;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Webkul\Security\Models\Invitation;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

/**
 * How many people a company may have, and who counts (D-SAAS-03/D-SAAS-04).
 *
 * A seat is consumed by an **active membership**: one per company per person,
 * including that company's administrators. A valid **pending invitation reserves**
 * a seat, because otherwise ten invitations could be sent against a five-seat
 * company and the last five would fail at acceptance - after the customer had
 * already been told they had been invited. Inactive users, expired or revoked
 * invitations, and platform support identities do not count.
 *
 * `seat_limit = null` means unlimited, which is an approved state (a sold
 * unlimited plan), not a missing value.
 *
 * Counting and mutating must happen under one lock, or two administrators
 * inviting the last seat at the same moment both see "one free" and both succeed.
 * `withLockedCompany()` is that lock; `assertCanReserve()` and
 * `assertCanAccept()` are only meaningful inside it. On PostgreSQL the lock is
 * on the company row, so the second transaction waits, then re-counts.
 *
 * Lowering a limit never deactivates anyone (D-SAAS-04): the company simply
 * cannot add members until usage is reduced or the limit raised.
 */
class SeatCapacityService
{
    /**
     * Run a capacity decision and the write it guards under a row lock.
     *
     * @template TReturn
     *
     * @param  Closure(Company): TReturn  $callback
     * @return TReturn
     */
    public function withLockedCompany(Company|int $company, Closure $callback): mixed
    {
        $companyId = $company instanceof Company ? (int) $company->getKey() : $company;

        return DB::transaction(function () use ($companyId, $callback): mixed {
            $locked = Company::query()
                ->withoutGlobalScopes()
                ->lockForUpdate()
                ->find($companyId);

            if (! $locked instanceof Company) {
                throw ValidationException::withMessages([
                    'company' => __('The company no longer exists.'),
                ]);
            }

            return $callback($locked);
        });
    }

    public function limitFor(Company|int $company): ?int
    {
        $companyId = $company instanceof Company ? (int) $company->getKey() : $company;

        $limit = DB::table('companies')->where('id', $companyId)->value('seat_limit');

        return $limit === null ? null : (int) $limit;
    }

    public function isUnlimited(Company|int $company): bool
    {
        return $this->limitFor($company) === null;
    }

    /**
     * Active memberships: one per person per company, administrators included.
     * An inactive user consumes nothing - they cannot sign in, so they are not
     * occupying a seat anybody else could use.
     *
     * Platform identities (Super Admin, Multi-Company Admin, the installation
     * administrator) do not consume a seat either, unless they also hold a
     * tenant role in this company (D-SAAS-03). They are attached to companies
     * for support - the installer attaches the administrator to every company,
     * and `Company::created` attaches whoever created it - so counting them
     * would take one of a new tenant's seats before the customer had a user.
     */
    public function activeMembershipCount(Company|int $company): int
    {
        $companyId = $company instanceof Company ? (int) $company->getKey() : $company;

        $platformRoleIds = DB::table('roles')
            ->whereIn(DB::raw('LOWER(name)'), Role::getSystemRoleNames())
            ->pluck('id')
            ->all();

        $userMorphClass = (new User)->getMorphClass();
        $tenantRolesSupported = app(TenantRoleResolver::class)->supported();

        return DB::table('user_allowed_companies')
            ->join('users', 'users.id', '=', 'user_allowed_companies.user_id')
            ->where('user_allowed_companies.company_id', $companyId)
            ->where('users.is_active', true)
            ->whereNull('users.deleted_at')
            ->where(function ($seat) use ($companyId, $platformRoleIds, $userMorphClass, $tenantRolesSupported): void {
                $seat->whereNotExists(fn ($platform) => $platform
                    ->selectRaw('1')
                    ->from('model_has_roles')
                    ->whereColumn('model_has_roles.model_id', 'users.id')
                    ->where('model_has_roles.model_type', $userMorphClass)
                    ->whereIn('model_has_roles.role_id', $platformRoleIds));

                if ($tenantRolesSupported) {
                    $seat->orWhereExists(fn ($tenant) => $tenant
                        ->selectRaw('1')
                        ->from('company_membership_roles')
                        ->whereColumn('company_membership_roles.user_id', 'users.id')
                        ->where('company_membership_roles.company_id', $companyId));
                }
            })
            ->distinct()
            ->count('users.id');
    }

    /**
     * Invitations that reserve a seat: not accepted, not revoked, not expired.
     */
    public function pendingInvitationCount(Company|int $company): int
    {
        $companyId = $company instanceof Company ? (int) $company->getKey() : $company;

        return Invitation::query()
            ->where('default_company_id', $companyId)
            ->pending()
            ->count();
    }

    public function usedSeats(Company|int $company): int
    {
        return $this->activeMembershipCount($company) + $this->pendingInvitationCount($company);
    }

    public function remainingSeats(Company|int $company): ?int
    {
        $limit = $this->limitFor($company);

        return $limit === null ? null : max(0, $limit - $this->usedSeats($company));
    }

    /**
     * A company can be over a limit that was lowered after it grew. That is a
     * state to report, never a reason to deactivate anybody.
     */
    public function isOverLimit(Company|int $company): bool
    {
        $limit = $this->limitFor($company);

        return $limit !== null && $this->activeMembershipCount($company) > $limit;
    }

    /**
     * Reserving a seat for a new invitation. Call inside withLockedCompany().
     *
     * @throws ValidationException
     */
    public function assertCanReserve(Company|int $company, int $reservations = 1): void
    {
        $limit = $this->limitFor($company);

        if ($limit === null) {
            return;
        }

        if ($this->usedSeats($company) + $reservations > $limit) {
            throw $this->capacityError($limit, $this->usedSeats($company));
        }
    }

    /**
     * The same question without throwing - for callers that report an outcome
     * rather than an error (the company onboarding inviter returns a reason, it
     * cannot fail the request).
     */
    public function canReserve(Company|int $company, int $reservations = 1): bool
    {
        $limit = $this->limitFor($company);

        return $limit === null || $this->usedSeats($company) + $reservations <= $limit;
    }

    /**
     * Attaching an existing person to a company (the Users form, an import).
     *
     * A user who is already an active member of that company consumes nothing
     * new - re-saving their form must not be blocked by a full company.
     *
     * @throws ValidationException
     */
    public function assertCanAttachUser(User $user, int $companyId): void
    {
        $limit = $this->limitFor($companyId);

        if ($limit === null) {
            return;
        }

        $alreadyMember = DB::table('user_allowed_companies')
            ->where('user_id', $user->getKey())
            ->where('company_id', $companyId)
            ->exists();

        if ($alreadyMember && $user->is_active) {
            return;
        }

        $active = $this->activeMembershipCount($companyId);

        if ($active + 1 > $limit) {
            throw $this->capacityError($limit, $active);
        }
    }

    /**
     * Accepting an invitation turns a reservation into a membership, so the
     * reservation itself must not be counted twice - only the active memberships
     * plus this one are compared with the limit.
     *
     * @throws ValidationException
     */
    public function assertCanAccept(Company|int $company): void
    {
        $limit = $this->limitFor($company);

        if ($limit === null) {
            return;
        }

        $active = $this->activeMembershipCount($company);

        if ($active + 1 > $limit) {
            throw $this->capacityError($limit, $active);
        }
    }

    /**
     * Adding an existing user to a company (a membership attach).
     *
     * Counts pending invitations, exactly like a new invitation does: an attach
     * that ignored them could take the seat an invitee was already promised,
     * and that invitee would then be refused at acceptance.
     *
     * @throws ValidationException
     */
    public function assertCanAttachMembership(Company|int $company): void
    {
        $this->assertCanReserve($company);
    }

    /**
     * Audited limit change (D-SAAS-06: platform administration only).
     */
    public function setLimit(Company $company, ?int $limit, ?User $actor = null): void
    {
        if ($limit !== null && $limit < 0) {
            throw ValidationException::withMessages([
                'seat_limit' => __('A seat limit cannot be negative.'),
            ]);
        }

        $before = $this->limitFor($company);

        if ($before === $limit) {
            return;
        }

        DB::transaction(function () use ($company, $limit, $before, $actor): void {
            DB::table('companies')->where('id', $company->getKey())->update([
                'seat_limit' => $limit,
            ]);

            app(SecurityAuditLogger::class)->record(
                'security.company.seat_limit_updated',
                $company,
                $actor,
                (int) $company->getKey(),
                before: ['seat_limit' => $before],
                after: ['seat_limit' => $limit],
            );
        });
    }

    /**
     * One message for every path (SAAS-07 wires the remaining ones), so a
     * customer sees the same explanation whether they were blocked at invite,
     * at acceptance or at a membership attach.
     */
    public function capacityError(int $limit, int $used): ValidationException
    {
        return ValidationException::withMessages([
            'seats' => __('This company has reached its :limit user seats (:used in use, including pending invitations). Raise the seat limit to add more users.', [
                'limit' => $limit,
                'used'  => $used,
            ]),
        ]);
    }
}
