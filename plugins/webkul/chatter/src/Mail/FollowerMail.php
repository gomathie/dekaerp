<?php

namespace Webkul\Chatter\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FollowerMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public string $viewTemplate,
        public array $payload
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->payload['subject'],
            from: new Address($this->payload['from']['address'], '"'.addslashes($this->payload['from']['name']).'"'),
            // The acting user, so a reply still reaches them even though the
            // envelope From is the application's verified address.
            replyTo: empty($this->payload['from']['reply_to']) ? [] : [new Address($this->payload['from']['reply_to'])],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(view: $this->viewTemplate);
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
