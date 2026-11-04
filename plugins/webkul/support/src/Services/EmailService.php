<?php

namespace Webkul\Support\Services;

use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Webkul\Support\Models\EmailLog;

class EmailService
{
    public function send(string $view, string $mailClass, array $payload, array $attachments = [])
    {
        try {
            $author = Auth::user();

            // The envelope From has to be an address the relay is allowed to
            // send as. Sending as the acting user was rejected with
            // "550-From header sender domain not verified" for every address the
            // provider had not verified - which is every tenant's - and would
            // fail the recipient's DMARC check even where the relay accepted it
            // (Sentry 158147606). The author is still named in the message and
            // stays reachable as the reply-to.
            $payload['from'] = [
                'address'  => config('mail.from.address'),
                'name'     => $author?->name ?? config('mail.from.name'),
                'reply_to' => $author?->email,
            ];

            if (current_company()) {
                $payload['from']['company'] = current_company()->toArray();
            }

            Mail::to($payload['to']['address'], '"'.addslashes($payload['to']['name']).'"')
                ->send((new $mailClass($view, $payload))->withAttachments($attachments));

            $this->logEmail($payload['to']['address'], $payload['to']['name'], $payload['subject'], 'sent');

            return true;
        } catch (Exception $e) {
            $this->logEmail($payload['to']['address'], $payload['to']['name'], $payload['subject'], 'failed', $e->getMessage());

            throw $e;
        }
    }

    protected function logEmail(string $recipientEmail, string $recipientName, string $subject, string $status, ?string $errorMessage = null)
    {
        EmailLog::create([
            'recipient_email' => $recipientEmail,
            'recipient_name'  => $recipientName,
            'subject'         => $subject,
            'status'          => $status,
            'error_message'   => $errorMessage,
            'sent_at'         => now(),
        ]);
    }
}
