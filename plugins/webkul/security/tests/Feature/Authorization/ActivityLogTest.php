<?php

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Livewire\Livewire;
use Webkul\Chatter\Models\Message;
use Webkul\Partner\Models\Partner;
use Webkul\Security\Filament\Clusters\Settings\Pages\ActivityLog;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';

/**
 * Company activity log (plan #1, CR-11; user, 2026-11-13): who changed or
 * deleted what, per company. Pinned: the page permission is generated; one
 * company never sees another's history, nor rows with no company; deletes
 * are recorded; a change is filed under the record's company, not the
 * actor's session; the export covers exactly the filtered rows.
 */
beforeEach(function (): void {
    // Partners is a core plugin with no install command; the ERP install
    // brings it, as in the other security tests.
    TestBootstrapHelper::ensureERPInstalled();
});

function activityRow(?int $companyId, string $event = 'updated', array $properties = []): Message
{
    return Message::withoutEvents(fn (): Message => Message::query()->create([
        'company_id'       => $companyId,
        'messageable_type' => Partner::class,
        'messageable_id'   => 1,
        'type'             => 'notification',
        'log_name'         => 'default',
        'body'             => 'test',
        'event'            => $event,
        'properties'       => $properties,
    ]));
}

it('generates the page permission the activity log checks', function (): void {
    FilamentHelper::bootAdminPanel();

    $generated = collect(FilamentShield::getPages())
        ->flatMap(fn (array $page): array => array_keys($page['permissions']))
        ->all();

    expect($generated)->toContain('page_security_activity_log');
});

it('opens only with the permission', function (): void {
    $company = Company::factory()->create();

    CompanyHelper::actingAsCompanyUser($company, []);
    expect(ActivityLog::canAccess())->toBeFalse();

    CompanyHelper::actingAsCompanyUser($company, ['page_security_activity_log']);
    expect(ActivityLog::canAccess())->toBeTrue();
});

it('never shows another company\'s history or rows with no company', function (): void {
    $mine = Company::factory()->create();
    $other = Company::factory()->create();

    $own = activityRow($mine->id);
    $foreign = activityRow($other->id);
    $shared = activityRow(null);

    CompanyHelper::actingAsCompanyUser($mine, ['page_security_activity_log']);

    $ids = ActivityLog::activityQuery()->pluck('id')->all();

    expect($ids)->toContain($own->id)
        ->and($ids)->not->toContain($foreign->id)
        ->and($ids)->not->toContain($shared->id);
});

it('records a delete, which used to leave no trace', function (): void {
    $company = Company::factory()->create();
    CompanyHelper::actingAsCompanyUser($company, ['page_security_activity_log']);

    $partner = Partner::factory()->create(['company_id' => $company->id, 'name' => 'Ama Traders']);
    $partner->delete();

    $logged = Message::query()->withoutGlobalScopes()
        ->where('messageable_id', $partner->id)
        ->whereIn('event', ['deleted', 'soft_deleted', 'hard_deleted'])
        ->first();

    expect($logged)->not->toBeNull()
        ->and($logged->company_id)->toBe($company->id);
});

it('files a change under the record\'s company, not the actor\'s session', function (): void {
    $sessionCompany = Company::factory()->create();
    $recordCompany = Company::factory()->create();

    CompanyHelper::actingAsCompanyUser([$sessionCompany, $recordCompany], [], activeIds: [$sessionCompany->id]);

    $partner = Partner::factory()->create(['company_id' => $recordCompany->id, 'name' => 'Before']);

    // Every history entry goes through addMessage(); Partner tracks no
    // fields on update, so the stamping is checked at that one point.
    $logged = $partner->addMessage(['type' => 'comment', 'body' => 'note']);

    expect($logged?->company_id)->toBe($recordCompany->id);
});

it('summarises an update as old and new values, as plain text', function (): void {
    $row = activityRow(1, 'updated', ['Name' => ['old_value' => '<b>Old</b>', 'new_value' => 'New']]);

    expect(ActivityLog::changesSummary($row))->toBe('Name: Old → New');
});

it('exports exactly the filtered rows', function (): void {
    $company = Company::factory()->create();

    activityRow($company->id, 'updated');
    activityRow($company->id, 'hard_deleted');

    FilamentHelper::actingAsCompanyUser($company, ['page_security_activity_log']);

    $page = Livewire::test(ActivityLog::class)
        ->filterTable('event', 'deleted')
        ->instance();

    expect($page->getTableQueryForExport()->pluck('event')->all())->toBe(['hard_deleted']);
});

it('names the record it logged, and falls back to the id', function (): void {
    // U30 (2026-11-14): "Partner: Ada Mensah" instead of "Partner #12".
    $partner = Partner::factory()->create(['name' => 'Ada Mensah']);

    $named = (new Message)->forceFill(['messageable_type' => Partner::class, 'messageable_id' => $partner->getKey()]);
    $gone = (new Message)->forceFill(['messageable_type' => Partner::class, 'messageable_id' => 999999999]);
    $uninstalled = (new Message)->forceFill(['messageable_type' => 'Webkul\\Removed\\Models\\Thing', 'messageable_id' => 5]);

    expect(ActivityLog::recordLabel($named))->toBe('Partner: Ada Mensah')
        ->and(ActivityLog::recordLabel($gone))->toBe('Partner #999999999')
        ->and(ActivityLog::recordLabel($uninstalled))->toBe('Thing #5');
});
