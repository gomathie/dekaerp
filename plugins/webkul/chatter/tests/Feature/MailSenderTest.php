<?php

use Illuminate\Support\Facades\Mail;
use Webkul\Chatter\Mail\FollowerMail;
use Webkul\Chatter\Mail\MessageMail;
use Webkul\Chatter\Models\Message;
use Webkul\Chatter\Services\ChatterNotificationService;
use Webkul\Partner\Models\Partner;
use Webkul\Project\Models\Project;
use Webkul\Security\Models\User;
use Webkul\Support\Services\EmailService;

require_once __DIR__.'/../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

/**
 * A relay only accepts an envelope From on a domain it has verified, and the
 * recipient's DMARC check rejects anything else. Both chatter senders used to
 * put the acting user's own address there, so on 2026-11-02 production refused
 * every notification with "550-From header sender domain not verified (the
 * sender domain's)" for a tenant address (Sentry 158147606). The application's
 * configured address is the sender; the author is still named in the message.
 */
beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('projects');

    Mail::fake();
});

it('notifies followers from the application sender rather than the author', function () {
    $company = CompanyHelper::company();
    CompanyHelper::actingAsCompanyUser($company);

    $author = User::factory()->create(['email' => 'clerk@tenant-unverified.test']);

    $partner = Partner::factory()->create([
        'company_id' => $company->id,
        'email'      => 'follower@example.test',
    ]);

    $project = Project::factory()->create(['company_id' => $company->id]);
    $project->addFollower($partner);

    $message = new Message([
        'company_id'       => $company->id,
        'messageable_type' => $project->getMorphClass(),
        'messageable_id'   => $project->id,
        'type'             => 'comment',
        'body'             => 'Please review the attached quotation',
        'causer_type'      => $author->getMorphClass(),
        'causer_id'        => $author->id,
    ]);

    // saveQuietly: the created hook would defer a second notification.
    $message->saveQuietly();

    app(ChatterNotificationService::class)->notifyFollowers($message);

    Mail::assertSent(MessageMail::class, function (MessageMail $mail) use ($author): bool {
        $envelope = $mail->envelope();

        return $mail->payload['to']['address'] === 'follower@example.test'
            && $envelope->from->address === config('mail.from.address')
            && $envelope->from->address !== $author->email
            && $envelope->replyTo[0]->address === $author->email;
    });
});

it('sends follower mail from the application sender rather than the acting user', function () {
    $company = CompanyHelper::company();
    $actor = CompanyHelper::actingAsCompanyUser($company);

    app(EmailService::class)->send(
        view: 'chatter::mail.follower-mail',
        mailClass: FollowerMail::class,
        payload: [
            'subject'     => 'You were added as a follower',
            'record_name' => 'A project',
            'model_name'  => 'Project',
            'note'        => 'Have a look when you can',
            'to'          => [
                'address' => 'follower@example.test',
                'name'    => 'Follower',
            ],
        ],
    );

    Mail::assertSent(FollowerMail::class, function (FollowerMail $mail) use ($actor): bool {
        $envelope = $mail->envelope();

        return $envelope->from->address === config('mail.from.address')
            && $envelope->from->address !== $actor->email
            && $envelope->replyTo[0]->address === $actor->email;
    });
});
