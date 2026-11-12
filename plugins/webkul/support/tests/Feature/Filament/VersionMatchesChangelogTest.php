<?php

use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;

require_once __DIR__.'/../../Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../Helpers/FilamentHelper.php';
require_once __DIR__.'/../../Helpers/CompanyHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensureERPInstalled();
});

/**
 * The profile menu showed "Version 1.5.0", hard-coded, while production ran
 * 1.7.0 (2026-11-12). The version now comes from config('app.version'), whose
 * default must equal the newest release heading in CHANGELOG.md, and the menu
 * row links to What's New.
 */
it('shows the changelog version in the profile menu, linked to the release notes', function (): void {
    preg_match('/^#\s.*?v(\d+\.\d+\.\d+[\w.\-]*)\s*$/m', (string) file_get_contents(base_path('CHANGELOG.md')), $matches);

    expect($matches[1] ?? null)->toBe(config('app.version'));

    FilamentHelper::bootAdminPanel();
    CompanyHelper::actingAsCompanyUser(CompanyHelper::company(), []);

    $html = (string) FilamentView::renderHook(PanelsRenderHook::USER_MENU_PROFILE_BEFORE);

    expect($html)->toContain(config('app.version'))
        ->and($html)->toContain('whats-new');
});
