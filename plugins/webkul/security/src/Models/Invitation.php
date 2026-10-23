<?php

namespace Webkul\Security\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\URL;
use Webkul\Support\Models\Company;

class Invitation extends Model
{
    use HasFactory;

    public const ACCEPT_WINDOW_DAYS = 7;

    protected $table = 'user_invitations';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'company_ids' => 'array',
            'role_ids'    => 'array',
        ];
    }

    /**
     * The signed link that lets the invitee set their own name and password.
     *
     * Defined here rather than in UserInvitationMail because it is also needed
     * when the mail could not be delivered: without a way to read the link
     * outside the email, a failed send left the invitation unreachable and the
     * tenant unable to onboard.
     *
     * Hand this to someone over a channel you trust. It authorises creating the
     * account for this address, and expires after the window below.
     */
    public function acceptUrl(): string
    {
        return URL::temporarySignedRoute(
            'security.invitation.accept',
            now()->addDays(self::ACCEPT_WINDOW_DAYS),
            ['invitation' => $this],
        );
    }

    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inviter_id');
    }

    public function defaultCompany(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'default_company_id');
    }
}
