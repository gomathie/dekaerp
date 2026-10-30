<?php

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Webkul\Chatter\Models\Message;
use Webkul\Chatter\Notifications\ChatterDatabaseNotification;
use Webkul\Chatter\Services\ChatterNotificationService;
use Webkul\Project\Models\Project;

require_once __DIR__.'/../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

/**
 * A mention outside the record's company is not delivered (user decision,
 * 2026-10-30; §4k read audit in docs/company-admin-role-plan.md).
 *
 * A mention notification carries the record's name and the message body, and
 * makes the person a follower. Delivered to someone who is not a member of the
 * record's company, it told one client about another client's business.
 */
beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('projects');
});

function mentionMarkup(int $userId): string
{
    return '<span data-type="mention" data-id="'.$userId.'">@user</span>';
}

it('notifies a mentioned member of the record\'s company but not an outsider', function () {
    Mail::fake();
    Notification::fake();

    $companyA = CompanyHelper::company();
    $companyB = CompanyHelper::company();

    $insider = SecurityHelper::authenticateWithPermissions([]);
    $insider->allowedCompanies()->sync([$companyA->id]);

    $outsider = SecurityHelper::authenticateWithPermissions([]);
    $outsider->allowedCompanies()->sync([$companyB->id]);

    expect($outsider->isSuperAdmin())->toBeFalse();

    CompanyHelper::actingAsCompanyUser($companyA);

    $project = Project::factory()->create(['company_id' => $companyA->id]);

    $message = new Message([
        'company_id'       => $companyA->id,
        'messageable_type' => $project->getMorphClass(),
        'messageable_id'   => $project->id,
        'type'             => 'comment',
        'body'             => 'Please look '.mentionMarkup($insider->id).' and '.mentionMarkup($outsider->id),
    ]);
    $message->saveQuietly();

    app(ChatterNotificationService::class)->notifyFollowers($message);

    Notification::assertSentTo($insider, ChatterDatabaseNotification::class);
    Notification::assertNotSentTo($outsider, ChatterDatabaseNotification::class);

    expect($project->followers()->where('partner_id', $outsider->partner_id)->exists())->toBeFalse();
});
