<?php

namespace Webkul\Security\Services;

use Illuminate\Support\Facades\Mail;
use Throwable;
use Webkul\Security\Mail\UserInvitationMail;
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

    public function __construct(protected UserInvitationService $invitations) {}

    /**
     * @return self::INVITED|self::DISABLED|self::MISSING_EMAIL|self::MISSING_ROLE|self::INVITE_FAILED|self::MAIL_FAILED
     */
    public function invite(Company $company, User $actor): string
    {
        if (! settings(UserSettings::class)->enable_user_invitation) {
            return self::DISABLED;
        }

        // The company's own address is the only one this form collects. A
        // dedicated administrator field would be better, but the company form
        // is inherited and monolithic, so adding one belongs in its own change.
        $email = trim((string) $company->email);

        if ($email === '') {
            return self::MISSING_EMAIL;
        }

        $roleId = static::companyAdminRoleId();

        if (! $roleId) {
            return self::MISSING_ROLE;
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

    public static function companyAdminRoleId(): ?int
    {
        $id = Role::query()
            ->where('guard_name', 'web')
            ->whereRaw('LOWER(name) = ?', [mb_strtolower(Role::COMPANY_ADMIN)])
            ->value('id');

        return $id ? (int) $id : null;
    }
}
