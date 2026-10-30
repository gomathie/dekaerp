<?php

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Webkul\Chatter\Mail\MessageMail;
use Webkul\Chatter\Models\Follower;
use Webkul\Chatter\Models\Message;
use Webkul\Chatter\Notifications\ChatterDatabaseNotification;
use Webkul\Chatter\Services\ChatterNotificationService;
use Webkul\Partner\Models\Partner;
use Webkul\Project\Models\Project;

require_once __DIR__.'/../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('projects');
});

it('adds a responsible user contact as a chatter follower across company contexts', function () {
    $companyA = CompanyHelper::company();
    $companyB = CompanyHelper::company();

    $creator = SecurityHelper::authenticateWithPermissions([]);
    $partner = Partner::factory()->create(['company_id' => $companyA->id]);
    $creator->forceFill(['partner_id' => $partner->id])->saveQuietly();

    CompanyHelper::actingAsCompanyUser($companyB);

    $project = Project::factory()->forCompany($companyB)->create([
        'creator_id' => $creator->id,
        'user_id'    => $creator->id,
    ]);

    $project->addDefaultChatterFollowers();

    expect(Follower::query()
        ->where('followable_type', $project->getMorphClass())
        ->where('followable_id', $project->id)
        ->where('partner_id', $partner->id)
        ->exists())->toBeTrue();
});

it('syncs an explicitly assigned user contact as a chatter follower across company contexts', function () {
    $companyA = CompanyHelper::company();
    $companyB = CompanyHelper::company();

    $assignedUser = SecurityHelper::authenticateWithPermissions([]);
    $partner = Partner::factory()->create(['company_id' => $companyA->id]);
    $assignedUser->forceFill(['partner_id' => $partner->id])->saveQuietly();

    CompanyHelper::actingAsCompanyUser($companyB);

    $project = Project::factory()->forCompany($companyB)->create();
    $followers = Follower::query()
        ->where('followable_type', $project->getMorphClass())
        ->where('followable_id', $project->id)
        ->where('partner_id', $partner->id);

    $followers->delete();

    expect($followers->exists())->toBeFalse();

    $project->syncChatterResponsibleFollowers('attached', [$assignedUser->id]);

    expect($followers->exists())->toBeTrue();
});

it('emails a cross-company chatter follower when the notification record is loaded in another context', function () {
    Mail::fake();

    $companyA = CompanyHelper::company();
    $companyB = CompanyHelper::company();

    $partner = Partner::factory()->create([
        'company_id' => $companyA->id,
        'email'      => 'company-a@example.test',
    ]);

    CompanyHelper::actingAsCompanyUser($companyB);

    $project = Project::factory()->forCompany($companyB)->create();
    $project->addFollower($partner);

    $message = new Message([
        'company_id'       => $companyB->id,
        'messageable_type' => $project->getMorphClass(),
        'messageable_id'   => $project->id,
        'type'             => 'comment',
        'body'             => 'Cross-company notification',
    ]);
    $message->saveQuietly();

    app(ChatterNotificationService::class)->notifyFollowers($message);

    Mail::assertSent(MessageMail::class, fn (MessageMail $mail): bool => $mail->payload['to']['address'] === $partner->email);
});

it('sends a database notification to an explicit follower in another company', function () {
    Mail::fake();
    Notification::fake();

    $companyA = CompanyHelper::company();
    $companyB = CompanyHelper::company();

    $followerUser = SecurityHelper::authenticateWithPermissions([]);
    $partner = Partner::factory()->create(['company_id' => $companyA->id]);
    $followerUser->forceFill(['partner_id' => $partner->id])->saveQuietly();

    CompanyHelper::actingAsCompanyUser($companyB);

    $project = Project::factory()->forCompany($companyB)->create();
    $project->addFollower($partner);

    $message = new Message([
        'company_id'       => $companyB->id,
        'messageable_type' => $project->getMorphClass(),
        'messageable_id'   => $project->id,
        'type'             => 'comment',
        'body'             => 'Cross-company database notification',
    ]);
    $message->saveQuietly();

    app(ChatterNotificationService::class)->notifyFollowers($message);

    Notification::assertSentTo($followerUser, ChatterDatabaseNotification::class);
});
