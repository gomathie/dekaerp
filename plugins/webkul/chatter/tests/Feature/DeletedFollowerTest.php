<?php

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Webkul\Chatter\Livewire\ChatterPanel;
use Webkul\Chatter\Mail\MessageMail;
use Webkul\Chatter\Models\Message;
use Webkul\Chatter\Notifications\ChatterDatabaseNotification;
use Webkul\Chatter\Services\ChatterNotificationService;
use Webkul\Partner\Models\Partner;
use Webkul\Project\Filament\Resources\ProjectResource;
use Webkul\Project\Models\Project;

require_once __DIR__.'/../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

/**
 * UF-031 (upstream PR #1703, 2026-11-25): a record followed by a contact that
 * was deleted (archived) later. The follower relation hid the trashed contact,
 * so the follower list and its remove button failed on the record; and
 * notification delivery read partners with withoutGlobalScopes(), which also
 * lifts the soft-delete scope, so the deleted contact was still mailed.
 */
beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('projects');

    $this->company = CompanyHelper::company();

    CompanyHelper::actingAsCompanyUser($this->company);

    $this->project = Project::factory()->create(['company_id' => $this->company->id]);

    $this->gone = Partner::factory()->create([
        'company_id' => $this->company->id,
        'name'       => 'Kofi <b>Left</b>',
        'email'      => 'left@example.test',
    ]);

    $this->project->addFollower($this->gone);

    $this->gone->delete();
});

function deletedFollowerTestMessage(Project $project): Message
{
    $message = new Message([
        'company_id'       => $project->company_id,
        'messageable_type' => $project->getMorphClass(),
        'messageable_id'   => $project->id,
        'type'             => 'comment',
        'body'             => 'Update for followers',
    ]);
    $message->saveQuietly();

    return $message;
}

it('lists a record\'s followers when one contact was deleted, marked as deleted and escaped', function () {
    expect($this->gone->refresh()->trashed())->toBeTrue();

    // The project also gets default followers on create; pick the deleted one.
    $follower = $this->project->followers()->with('partner')->where('partner_id', $this->gone->id)->firstOrFail();

    expect($follower->partner)->not->toBeNull()
        ->and($follower->partner->id)->toBe($this->gone->id);

    $html = view('chatter::filament.actions.follower-action', ['record' => $this->project->fresh()])->render();

    expect($html)->toContain('Kofi &lt;b&gt;Left&lt;/b&gt;')
        ->and($html)->not->toContain('<b>Left</b>')
        ->and($html)->toContain(__('chatter::filament/resources/actions/chatter/follower-action.setup.deleted'))
        ->and($html)->toContain('removeFollower('.$this->gone->id.')');
});

it('removes a deleted contact from the followers', function () {
    Livewire::test(ChatterPanel::class, [
        'record'        => $this->project,
        'resourceClass' => ProjectResource::class,
        'activityPlans' => collect(),
    ])->call('removeFollower', $this->gone->id);

    expect($this->project->followers()->where('partner_id', $this->gone->id)->exists())->toBeFalse();
});

it('sends no mail and no notification to a deleted contact', function () {
    Mail::fake();
    Notification::fake();

    $user = SecurityHelper::authenticateWithPermissions([]);
    $this->gone->forceFill(['user_id' => $user->id])->saveQuietly();
    $user->forceFill(['partner_id' => $this->gone->id])->saveQuietly();

    $active = Partner::factory()->create([
        'company_id' => $this->company->id,
        'email'      => 'stays@example.test',
    ]);
    $this->project->addFollower($active);

    CompanyHelper::actingAsCompanyUser($this->company);

    app(ChatterNotificationService::class)->notifyFollowers(deletedFollowerTestMessage($this->project));

    Mail::assertSent(MessageMail::class, fn (MessageMail $mail): bool => $mail->payload['to']['address'] === 'stays@example.test');
    Mail::assertNotSent(MessageMail::class, fn (MessageMail $mail): bool => $mail->payload['to']['address'] === 'left@example.test');
    Notification::assertNotSentTo($user, ChatterDatabaseNotification::class);
});
