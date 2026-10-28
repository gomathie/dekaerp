<?php

namespace Webkul\Security\Models;

use Illuminate\Database\Eloquent\Builder;
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
            'expires_at'  => 'datetime',
            'accepted_at' => 'datetime',
            'revoked_at'  => 'datetime',
        ];
    }

    /**
     * Invitations that still reserve a seat: not accepted, not revoked, not
     * expired.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query
            ->whereNull('accepted_at')
            ->whereNull('revoked_at')
            ->where(function (Builder $notExpired): void {
                $notExpired
                    ->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    public function isAccepted(): bool
    {
        return $this->accepted_at !== null;
    }

    public function isRevoked(): bool
    {
        return $this->revoked_at !== null;
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null
            && ! $this->isAccepted()
            && ! $this->isRevoked()
            && $this->expires_at->isPast();
    }

    /**
     * Pending means the link is still usable and the seat is still reserved. A
     * row is never deleted any more: "was this person invited, and what
     * happened?" is a question support has to be able to answer, and a seat
     * count cannot be taken against a state that does not exist.
     */
    public function isPending(): bool
    {
        return ! $this->isAccepted() && ! $this->isRevoked() && ! $this->isExpired();
    }

    public function revoke(?User $actor = null): void
    {
        if (! $this->isPending()) {
            return;
        }

        $this->forceFill([
            'revoked_at'    => now(),
            'revoked_by_id' => $actor?->getKey(),
        ])->save();
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
