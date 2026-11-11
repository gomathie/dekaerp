<?php

use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\App;

require_once __DIR__.'/../../Helpers/FilamentHelper.php';
require_once __DIR__.'/../../Helpers/TestBootstrapHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensureERPInstalled();
    FilamentHelper::bootAdminPanel();
});

/**
 * The admin panel had no language switcher (user, 2026-11-11), so it sits in
 * the profile pop-up. Changed the same day after complaints: the current
 * language shows once, and the others open below it on click. Each other
 * language sends ?lang=, which SetLocale keeps in the session.
 */
it('shows the current language once and offers every other language below it', function (): void {
    App::setLocale('fr');

    $html = (string) FilamentView::renderHook(PanelsRenderHook::USER_MENU_PROFILE_AFTER);
    $locales = config('app.supported_locales');

    // The row shows the current code once; the strip lists every code as a
    // chip named by its full native language, the current one marked.
    expect($html)->toContain('class="uml-current" lang="fr">FR<')
        ->and($html)->toContain('aria-expanded')
        ->and(substr_count($html, 'aria-current="true"'))->toBe(1);

    foreach ($locales as $code => $locale) {
        expect($html)->toContain('?lang='.$code.'"')
            ->and($html)->toContain('aria-label="'.e($locale['native']).'"')
            ->and($html)->toContain('>'.strtoupper(str_replace('_', '-', $code)).'</a>');
    }

    // The click handler must be plain JavaScript once rendered: an
    // uncompiled Blade directive here broke it in production (2026-11-11).
    expect($html)->not->toContain('@js(');
});
