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

    expect(substr_count($html, e($locales['fr']['native'])))->toBe(1)
        ->and($html)->not->toContain('?lang=fr"')
        ->and($html)->toContain('aria-expanded');

    foreach (array_diff_key($locales, ['fr' => true]) as $code => $locale) {
        expect($html)->toContain('?lang='.$code.'"')
            ->and($html)->toContain(e($locale['native']));
    }

    // The click handler must be plain JavaScript once rendered: an
    // uncompiled Blade directive here broke it in production (2026-11-11).
    expect($html)->not->toContain('@js(');
});
