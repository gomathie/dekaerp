<?php

use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Webkul\Security\Models\User;
use Webkul\Support\Filament\Pages\WhatsNew;

require_once __DIR__.'/../../Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../Helpers/FilamentHelper.php';
require_once __DIR__.'/../../Helpers/CompanyHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensureERPInstalled();
});

/**
 * What's New (plan docs/completed/whats-new-plan.md). The parser is the one
 * reader of CHANGELOG.md for the page and the PDF.
 */
it('parses releases newest first, with dates, mapped categories and security badges', function (): void {
    $releases = WhatsNew::parseChangelog(<<<'MD'
        # CHANGELOG — v2.0.0 — 2026-12-01

        ### 🧩 Features

        * First line
          continues here
        * [Security] Locked down

        ### Fixes

        * Repaired

        # 🚀 CHANGELOG — v1.9.0

        ### Upgrade

        Run the migration first.
        MD);

    expect($releases)->toHaveCount(2)
        ->and($releases[0]['version'])->toBe('v2.0.0')
        ->and($releases[0]['id'])->toBe('2-0-0')
        ->and($releases[0]['date'])->toBe('2026-12-01')
        ->and($releases[0]['sections'][0]['category'])->toBe('new')
        ->and($releases[0]['sections'][1]['category'])->toBe('fixed')
        // G4: a wrapped bullet keeps its continuation.
        ->and($releases[0]['sections'][0]['items'][0]['text'])->toBe('first line continues here')
        ->and($releases[0]['sections'][0]['items'][1]['security'])->toBeTrue()
        ->and($releases[0]['sections'][0]['items'][1]['text'])->toBe('locked down')
        // Prose under a heading is kept as one entry.
        ->and($releases[1]['date'])->toBeNull()
        ->and($releases[1]['sections'][0]['category'])->toBe('upgrade')
        ->and($releases[1]['sections'][0]['items'][0]['text'])->toBe('run the migration first.');
});

it('hides the Unreleased block and HTML comments from users (D-WN-1)', function (): void {
    $releases = WhatsNew::parseChangelog(<<<'MD'
        <!-- authoring note: secret plan -->

        ## Unreleased

        ### Features

        * Not shipped yet

        # CHANGELOG — v1.0.0

        ### Features

        * Shipped
        MD);

    expect($releases)->toHaveCount(1)
        ->and($releases[0]['version'])->toBe('v1.0.0')
        ->and(json_encode($releases))->not->toContain('Not shipped')
        ->and(json_encode($releases))->not->toContain('secret plan');
});

it('strips raw HTML and refuses javascript links (G1)', function (): void {
    $releases = WhatsNew::parseChangelog(<<<'MD'
        # CHANGELOG — v1.0.0

        ### Fixes

        * Bad <script>alert(1)</script> and [click](javascript:alert(1)) and [ok](https://example.com)
        MD);

    $html = $releases[0]['sections'][0]['items'][0]['html'];

    expect($html)->not->toContain('<script')
        ->and($html)->not->toContain('javascript:')
        ->and($html)->toContain('href="https://example.com"');
});

it('keeps a code fence whole', function (): void {
    $releases = WhatsNew::parseChangelog(<<<'MD'
        # CHANGELOG — v1.0.0

        ### Upgrade

        ```
        php artisan migrate

        php artisan erp:sync-permissions
        ```
        MD);

    expect($releases[0]['sections'][0]['items'])->toHaveCount(1)
        ->and($releases[0]['sections'][0]['items'][0]['html'])->toContain('erp:sync-permissions');
});

it('reads the real CHANGELOG.md and its newest release matches the app version', function (): void {
    expect(WhatsNew::loadReleases())->not->toBeEmpty()
        ->and(ltrim((string) WhatsNew::latestVersion(), 'v'))->toBe(config('app.version'));
});

it('opens for a company user and clears the new dot', function (): void {
    FilamentHelper::bootAdminPanel();
    $user = CompanyHelper::actingAsCompanyUser(CompanyHelper::company(), []);

    User::query()->whereKey($user->getKey())->update(['whats_new_seen_version' => null]);
    $user->refresh();
    auth()->setUser($user);

    expect(WhatsNew::hasUnseenRelease())->toBeTrue();

    $this->get(WhatsNew::getUrl())->assertOk()->assertSee(WhatsNew::latestVersion());

    expect($user->fresh()->whats_new_seen_version)->toBe(WhatsNew::latestVersion());

    auth()->setUser($user->fresh());
    expect(WhatsNew::hasUnseenRelease())->toBeFalse();
});

it('shows the top-bar shortcut, with a dot only while a release is unread', function (): void {
    FilamentHelper::bootAdminPanel();
    $user = CompanyHelper::actingAsCompanyUser(CompanyHelper::company(), []);

    User::query()->whereKey($user->getKey())->update(['whats_new_seen_version' => 'v0.0.1']);
    auth()->setUser($user->fresh());

    $html = (string) FilamentView::renderHook(PanelsRenderHook::GLOBAL_SEARCH_AFTER);

    expect($html)->toContain('whats-new')->and($html)->toContain('data-whats-new-dot');

    User::query()->whereKey($user->getKey())->update(['whats_new_seen_version' => WhatsNew::latestVersion()]);
    auth()->setUser($user->fresh());

    expect((string) FilamentView::renderHook(PanelsRenderHook::GLOBAL_SEARCH_AFTER))
        ->not->toContain('data-whats-new-dot');
});

it('streams the PDF inline for all releases and for one, and 404s an unknown release', function (): void {
    FilamentHelper::bootAdminPanel();
    CompanyHelper::actingAsCompanyUser(CompanyHelper::company(), []);

    $all = $this->get(route('support.whats-new.pdf'));
    $all->assertOk();
    expect($all->headers->get('content-type'))->toContain('application/pdf')
        ->and($all->headers->get('content-disposition'))->toContain('inline');

    $id = WhatsNew::loadReleases()[0]['id'];
    $this->get(route('support.whats-new.pdf', ['release' => $id]))->assertOk();

    $this->get(route('support.whats-new.pdf', ['release' => 'no-such-release']))->assertNotFound();
});

it('refuses the PDF to a guest', function (): void {
    auth()->guard('web')->logout();

    $this->get(route('support.whats-new.pdf'))->assertRedirect();
});
