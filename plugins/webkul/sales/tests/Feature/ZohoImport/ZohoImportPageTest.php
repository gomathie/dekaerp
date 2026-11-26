<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\Support\PluginAccess;
use Webkul\Sale\Filament\Pages\ZohoImport;
use Webkul\Sale\Jobs\RunZohoImport;
use Webkul\Sale\Models\ZohoImportRun;
use Webkul\Sale\Services\ZohoImport\ZohoImportException;
use Webkul\Sale\Services\ZohoImport\ZohoImportRunner;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../Helpers/ZohoImportHelper.php';

/**
 * The Super Admin import page (user, 2026-11-25): only an active Super Admin
 * reaches it or any of its steps, the run is queued, audited, and the
 * uploaded export is not kept once the run ends.
 */
beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('sales');

    DB::table('plugins')->updateOrInsert(['name' => 'sales'], ['is_installed' => true, 'is_active' => true, 'updated_at' => now()]);
    Package::$plugins = Plugin::all()->keyBy('name');
    PluginAccess::flush();
    URL::resolveMissingNamedRoutesUsing(fn () => '#');

    Mail::fake();
    Notification::fake();

    $this->company = zohoTestCompany();
    $this->json = file_get_contents(zohoFixturePath());
});

function zohoSignIn(User $user): User
{
    Auth::shouldUse('web');
    Auth::guard('web')->login($user);
    FilamentHelper::bootAdminPanel();

    return $user;
}

function zohoSuperAdmin(): User
{
    return zohoSignIn(User::query()->where('email', 'admin@example.com')->firstOrFail());
}

function zohoCompanyAdmin(): User
{
    $user = User::withoutEvents(fn (): User => User::factory()->create(['is_active' => true, 'is_default' => false]));
    $role = Role::query()->whereRaw('LOWER(name) = ?', [mb_strtolower(Role::COMPANY_ADMIN)])->first()
        ?? Role::create(['name' => Role::COMPANY_ADMIN, 'guard_name' => 'web']);
    $user->syncRoles([$role->getKey()]);

    return zohoSignIn($user->fresh());
}

/**
 * Play the queued steps of a run as the worker would, with no signed-in
 * user, until the run ends.
 */
function zohoDrive(ZohoImportRun $run): ZohoImportRun
{
    Auth::guard('web')->logout();
    $runner = app(ZohoImportRunner::class);

    for ($i = 0; $i < 500 && $run->refresh()->isActive(); $i++) {
        $runner->execute($run, (int) $run->step);
    }

    return $run->refresh();
}

it('opens for an active super admin only', function () {
    zohoSuperAdmin();

    expect(ZohoImport::canAccess())->toBeTrue();
    Livewire::test(ZohoImport::class)->assertOk();

    zohoCompanyAdmin();

    expect(ZohoImport::canAccess())->toBeFalse();
    Livewire::test(ZohoImport::class)->assertForbidden();
});

it('refuses every step to anyone but a super admin, whatever the page shows', function () {
    $admin = zohoSuperAdmin();
    $run = app(ZohoImportRunner::class)->propose($admin, $this->company->id, $this->json);

    $clerk = zohoCompanyAdmin();

    expect(fn () => app(ZohoImportRunner::class)->propose($clerk, $this->company->id, $this->json))->toThrow(AuthorizationException::class)
        ->and(fn () => app(ZohoImportRunner::class)->start($clerk, $run))->toThrow(AuthorizationException::class);

    $admin->forceFill(['is_active' => false])->saveQuietly();

    expect(fn () => app(ZohoImportRunner::class)->start($admin->fresh(), $run))->toThrow(AuthorizationException::class);
});

it('refuses a file that is not a Zoho export', function () {
    $admin = zohoSuperAdmin();

    expect(fn () => app(ZohoImportRunner::class)->propose($admin, $this->company->id, '{"invoices": []}'))
        ->toThrow(ZohoImportException::class, 'section');
});

it('proposes, queues, imports, compares, audits, and forgets the export', function () {
    Queue::fake();
    $admin = zohoSuperAdmin();
    $runner = app(ZohoImportRunner::class);

    $run = $runner->propose($admin, $this->company->id, $this->json);

    expect($run->status)->toBe(ZohoImportRun::PROPOSED)
        ->and($run->mapping['approved'])->toBeFalse();

    $run = $runner->start($admin, $run);

    Queue::assertPushed(RunZohoImport::class, fn (RunZohoImport $job) => $job->runId === $run->id);

    expect($run->status)->toBe(ZohoImportRun::QUEUED)
        ->and(fn () => $runner->start($admin, $run))->toThrow(ZohoImportException::class);

    // The worker has no signed-in user.
    Auth::guard('web')->logout();
    $run = zohoDrive($run);

    expect($run->status)->toBe(ZohoImportRun::FINISHED)
        ->and($run->export)->toBeNull()
        ->and($run->counts['invoices imported'])->toBe(8)
        ->and(collect($run->comparison['rows'])->where('status', 'DIFF')->all())->toBe([])
        ->and($run->comparison['unmatched'])->toBe([])
        ->and($run->cutoff_at?->toIso8601String())->toBe('2025-05-01T10:00:00+00:00')
        ->and(collect($run->comparison['rows'])->pluck('label')->implode(' '))->not->toContain('Synthetic Customer')
        ->and(DB::table('chatter_messages')->where('event', 'sales.zoho_import.finished')->where('messageable_id', $run->id)->exists())->toBeTrue();
});

it('records why a run stopped and still forgets the export', function () {
    $admin = zohoSuperAdmin();
    $runner = app(ZohoImportRunner::class);

    Queue::fake();
    $run = $runner->start($admin, $runner->propose($admin, $this->company->id, $this->json));

    $mapping = $run->mapping;
    unset($mapping['accounts']['A-TRAVEL']);
    $run->update(['mapping' => $mapping]);

    Auth::guard('web')->logout();
    $run = zohoDrive($run);

    expect($run->status)->toBe(ZohoImportRun::FAILED)
        ->and($run->message)->toContain('Travel & Transportation')
        ->and($run->export)->toBeNull();
});

it('refuses a second import from the page once the initial load is done', function () {
    Queue::fake();
    $admin = zohoSuperAdmin();
    $runner = app(ZohoImportRunner::class);

    $run = $runner->start($admin, $runner->propose($admin, $this->company->id, $this->json));
    Auth::guard('web')->logout();
    zohoDrive($run);

    zohoSuperAdmin();

    expect(fn () => $runner->propose($admin, $this->company->id, $this->json))
        ->toThrow(ZohoImportException::class, 'The initial load into this company is already done (Zoho export of 2025-05-01 10:00 UTC).');
});

it('compares a fresh export from the page without writing anything', function () {
    Queue::fake();
    $admin = zohoSuperAdmin();
    $runner = app(ZohoImportRunner::class);

    zohoDrive(tap($runner->start($admin, $runner->propose($admin, $this->company->id, $this->json)), fn () => Auth::guard('web')->logout()));

    zohoSuperAdmin();
    $moves = DB::table('accounts_account_moves')->where('company_id', $this->company->id)->count();

    $run = $runner->start($admin, $runner->propose($admin, $this->company->id, $this->json, mode: ZohoImportRun::MODE_COMPARE));
    Auth::guard('web')->logout();
    $run = zohoDrive($run);

    expect($run->status)->toBe(ZohoImportRun::FINISHED)
        ->and($run->mode)->toBe(ZohoImportRun::MODE_COMPARE)
        ->and($run->cutoff_at)->toBeNull()
        ->and($run->comparison['unmatched'])->toBe([])
        ->and(DB::table('accounts_account_moves')->where('company_id', $this->company->id)->count())->toBe($moves);
});

it('works in short steps, each a new queued job carrying only the run id and its step', function () {
    Queue::fake();
    $admin = zohoSuperAdmin();
    $runner = app(ZohoImportRunner::class);
    $run = $runner->start($admin, $runner->propose($admin, $this->company->id, $this->json));
    Auth::guard('web')->logout();

    // A budget of nothing: every step records one record and stops.
    ZohoImportRunner::$stepSeconds = 0;

    try {
        expect($runner->execute($run, 0))->toBe(ZohoImportRunner::STEP_DONE);

        Queue::assertPushed(RunZohoImport::class, fn (RunZohoImport $job) => $job->runId === $run->id && $job->step === 1);

        $steps = 1;

        while ($run->refresh()->isActive() && $steps < 500) {
            $runner->execute($run, (int) $run->step);
            $steps++;
        }
    } finally {
        ZohoImportRunner::$stepSeconds = 45;
    }

    expect($steps)->toBeGreaterThan(10)
        ->and($run->status)->toBe(ZohoImportRun::FINISHED)
        ->and($run->counts['invoices imported'])->toBe(8)
        ->and(collect($run->comparison['rows'])->where('status', 'DIFF')->all())->toBe([]);
});

it('adds nothing when the queue delivers a step twice', function () {
    Queue::fake();
    $admin = zohoSuperAdmin();
    $runner = app(ZohoImportRunner::class);
    $run = $runner->start($admin, $runner->propose($admin, $this->company->id, $this->json));
    Auth::guard('web')->logout();

    ZohoImportRunner::$stepSeconds = 0;

    try {
        $runner->execute($run, 0);
        $records = DB::table('sales_zoho_import_records')->where('company_id', $this->company->id)->count();

        // The same step again, after it was done: stale, nothing written.
        expect($runner->execute($run->refresh(), 0))->toBe(ZohoImportRunner::STEP_STALE)
            ->and(DB::table('sales_zoho_import_records')->where('company_id', $this->company->id)->count())->toBe($records);

        // A delivery while another holds the run: busy, nothing written, and
        // the queue's give-up does not mark the run failed.
        $lock = Cache::lock(ZohoImportRunner::lockKey((int) $this->company->id), ZohoImportRunner::LOCK_SECONDS);
        $lock->get();

        expect($runner->execute($run->refresh(), (int) $run->step))->toBe(ZohoImportRunner::STEP_BUSY);

        (new RunZohoImport($run->id, (int) $run->step))->failed(new RuntimeException('Max attempts'));

        expect($run->refresh()->status)->toBe(ZohoImportRun::RUNNING)
            ->and(DB::table('sales_zoho_import_records')->where('company_id', $this->company->id)->count())->toBe($records);

        $lock->release();
    } finally {
        ZohoImportRunner::$stepSeconds = 45;
    }

    // The retried step then carries on to the end.
    expect(zohoDrive($run)->status)->toBe(ZohoImportRun::FINISHED);
});

it('resumes a run after a step was cut short half way', function () {
    Queue::fake();
    $admin = zohoSuperAdmin();
    $runner = app(ZohoImportRunner::class);
    $run = $runner->start($admin, $runner->propose($admin, $this->company->id, $this->json));
    Auth::guard('web')->logout();

    ZohoImportRunner::$stepSeconds = 0;

    try {
        for ($i = 0; $i < 6; $i++) {
            $runner->execute($run->refresh(), (int) $run->step);
        }
    } finally {
        ZohoImportRunner::$stepSeconds = 45;
    }

    // A worker killed mid-step leaves no half document: each is one
    // transaction. The retry continues from the run's own step.
    $run = zohoDrive($run);

    expect($run->status)->toBe(ZohoImportRun::FINISHED)
        ->and(DB::table('accounts_account_moves')->where('company_id', $this->company->id)->where('move_type', 'out_invoice')->count())->toBe(8)
        ->and(collect($run->comparison['rows'])->where('status', 'DIFF')->all())->toBe([]);
});

it('marks a run failed when the queue gives up and nobody else is working on it', function () {
    Queue::fake();
    $admin = zohoSuperAdmin();
    $runner = app(ZohoImportRunner::class);
    $run = $runner->start($admin, $runner->propose($admin, $this->company->id, $this->json));

    (new RunZohoImport($run->id, 0))->failed(new RuntimeException('Worker lost'));

    expect($run->refresh()->status)->toBe(ZohoImportRun::FAILED)
        ->and($run->export)->toBeNull();
});
