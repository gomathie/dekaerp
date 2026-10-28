<?php

namespace Webkul\Security\Services;

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
 * This is a service rather than logic inside the create page so the outcome can
 * be asserted without mounting Filament. Every outcome is a returned reason and
 * never an exception, because the company has already been saved by the time
 * this runs and must not be lost over a failed invitation.
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
     * @return self::INVITED|self::DISABLED|self::MISSING_EMAIL|self::MISSING_ROLE|self::SEATS_EXHAUSTED|self::INVITE_FAILED|self::MAIL_FAILED
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

        try {
            Mail::to($invitation->email)->send(new UserInvitationMail($invitation));
        } catch (Throwable $exception) {
            report($exception);

            // The invitation row outlives a mail failure on purpose, so it can
            // be resent from the Users page instead of recreated.
            return self::MAIL_FAILED;
        }

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

        try {
            Mail::to($invitation->email)->send(new UserInvitationMail($invitation));
        } catch (Throwable $exception) {
            report($exception);

            return self::MAIL_FAILED;
        }

        return self::INVITED;
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
