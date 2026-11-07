<?php

namespace Webkul\Security\Services;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Throwable;
use Webkul\Security\Mail\UserInvitationMail;
use Webkul\Security\Models\Invitation;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\User;
use Webkul\Security\Settings\UserSettings;
use Webkul\Support\Models\Company;

/**
 * Invites a new company's own administrator, so a tenant can sign in and manage
 * their own people rather than DEKA staff creating every user by hand
 * (user, 2026-10-21).
 *
 * An invitation, never a user with a generated password: the invitee sets their
 * own name and password through AcceptInvitation, so no password is minted here
 * or placed in an email. The role is "Company Admin", which administers that
 * company's people and nothing else - CompanyAdminRoleProvisioner documents
 * what it deliberately withholds.
 *
 * This is a service rather than logic inside the create page. The invitation
 * remains durable when its after-commit mail fails; staff recover its link
 * from the company's Users and logins list.
 */
class CompanyAdministratorInviter
{
    public const INVITED = 'invited';

    public const DISABLED = 'disabled';

    public const MISSING_EMAIL = 'missing-email';

    public const MISSING_ROLE = 'missing-role';

    public const INVITE_FAILED = 'invite-failed';

    public const MAIL_FAILED = 'mail-failed';

    /**
     * The company has no free seats, so inviting its administrator would exceed
     * the limit. Reported as its own reason rather than folded into
     * INVITE_FAILED: "raise the seat limit" is a different action from "that
     * address already has an account", and the create page shows the difference.
     */
    public const SEATS_EXHAUSTED = 'seats-exhausted';

    public function __construct(protected UserInvitationService $invitations) {}

    /**
     * @param  string|null  $email  The administrator's own address. Falls back
     *                              to the company's address when not given, so
     *                              existing callers behave exactly as before.
     * @return self::INVITED|self::DISABLED|self::MISSING_EMAIL|self::MISSING_ROLE|self::SEATS_EXHAUSTED|self::INVITE_FAILED
     */
    public function invite(Company $company, User $actor, ?string $email = null): string
    {
        if (! settings(UserSettings::class)->enable_user_invitation) {
            return self::DISABLED;
        }

        // The administrator is usually a person, not the company's shared
        // mailbox, so the company form now collects an address of its own
        // (CompanyAdminContributions). The company's address remains the
        // default, which is what this service used to assume outright.
        $email = trim((string) ($email ?? $company->email));

        if ($email === '') {
            return self::MISSING_EMAIL;
        }

        $roleId = static::companyAdminRoleId();

        if (! $roleId) {
            return self::MISSING_ROLE;
        }

        // Capacity is checked before creating anything, and reported as its own
        // outcome: the company exists by now, so this must not throw, and
        // "raise the seat limit" is not the same advice as "that address is
        // already a user".
        if (! app(SeatCapacityService::class)->canReserve($company)) {
            return self::SEATS_EXHAUSTED;
        }

        try {
            $invitation = $this->invitations->create(
                $actor,
                $email,
                (int) $company->getKey(),
                [$roleId],
            );
        } catch (Throwable $exception) {
            // Two expected cases, both the caller's to act on: the address
            // already belongs to a user, and an actor who is not a super admin
            // cannot yet assign the company they just created, because it is
            // not among their allowed companies. See
            // MultiCompanyAdminService::assertUserAssignment().
            report($exception);

            return self::INVITE_FAILED;
        }

        $this->sendAfterCommit($invitation, $actor);

        return self::INVITED;
    }

    /**
     * Send a pending invitation again.
     *
     * Same outcome vocabulary as `invite()`: the caller is a page or a service
     * that has a company to keep, so a failure is reported, never thrown.
     */
    public function resend(Invitation $invitation, User $actor): string
    {
        try {
            $this->invitations->renew($invitation, $actor);
        } catch (Throwable $exception) {
            report($exception);

            return self::INVITE_FAILED;
        }

        $this->sendAfterCommit($invitation, $actor);

        return self::INVITED;
    }

    /** A pending link is a login credential; installation staff may copy only their assigned company's link. */
    public function pendingLink(Company $company, User $actor, int $invitationId): string
    {
        if (! $actor->is_active || ! ($actor->isSuperAdmin()
            || ($actor->isMultiCompanyAdmin() && app(MultiCompanyAdminService::class)->isCompanyAssigned($actor, (int) $company->getKey())))) {
            throw new AuthorizationException('You may not copy invitation links for this company.');
        }

        return Invitation::query()
            ->whereKey($invitationId)
            ->where('default_company_id', $company->getKey())
            ->pending()
            ->firstOrFail()
            ->acceptUrl();
    }

    protected function sendAfterCommit(Invitation $invitation, User $actor): void
    {
        // Never deliver a link for a row that may still roll back. The callback
        // re-reads pending state because staff may revoke it before dispatch.
        DB::afterCommit(function () use ($invitation, $actor): void {
            $pending = Invitation::query()->whereKey($invitation->getKey())->pending()->first();

            if (! $pending) {
                return;
            }

            try {
                Mail::to($pending->email)->send(new UserInvitationMail($pending));
            } catch (Throwable $exception) {
                report($exception);

                // The durable row and staff-only copy action are the recovery
                // path; never put the signed link in an audit payload.
                app(SecurityAuditLogger::class)->record(
                    'security.user.invitation_mail_failed',
                    $pending,
                    $actor,
                    (int) $pending->default_company_id,
                    after: ['email' => $pending->email, 'error' => $exception::class],
                );
            }
        });
    }

    public static function companyAdminRoleId(): ?int
    {
        $id = Role::query()
            ->where('guard_name', 'web')
            ->whereRaw('LOWER(name) = ?', [mb_strtolower(Role::COMPANY_ADMIN)])
            ->value('id');

        return $id ? (int) $id : null;
    }
}
