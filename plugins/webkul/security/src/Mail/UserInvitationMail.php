<?php

namespace Webkul\Security\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Webkul\Security\Models\Invitation;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class UserInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    private Invitation $invitation;

    /**
     * Create a new message instance.
     */
    public function __construct(Invitation $invitation)
    {
        $this->invitation = $invitation;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('security::mail/user-invitation-mail.user-invitation.subject', [
                'app'     => config('app.name'),
                'company' => $this->companyName(),
            ]),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // Read scope-free: the mail may be rendered by a queue worker or by staff
        // in another company, where a scoped relation read would return null.
        $inviter = $this->invitation->inviter_id
            ? User::withoutGlobalScopes()->find($this->invitation->inviter_id)
            : null;

        return new Content(
            markdown: 'security::emails.user-invitation',
            with: [
                // One definition, shared with the UI that shows the link when
                // this mail could not be sent, so the two cannot drift apart.
                'acceptUrl'   => $this->invitation->acceptUrl(),
                'companyName' => $this->companyName(),
                'inviterName' => $inviter?->name,
                'expiresOn'   => now()->addDays(Invitation::ACCEPT_WINDOW_DAYS)->toFormattedDateString(),
            ]
        );
    }

    /**
     * Read scope-free: the mail may be rendered by a queue worker or by staff
     * in another company, where a scoped read would return null.
     */
    private function companyName(): string
    {
        $company = $this->invitation->default_company_id
            ? Company::withoutGlobalScopes()->find($this->invitation->default_company_id)
            : null;

        return $company?->name ?? config('app.name');
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
