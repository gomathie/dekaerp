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
 * the profile pop-up. Each entry sends ?lang=, which SetLocale keeps in the
 * session, and the current language is marked.
 */
it('lists every supported language in the profile menu and marks the current one', function (): void {
    App::setLocale('fr');

    $html = (string) FilamentView::renderHook(PanelsRenderHook::USER_MENU_PROFILE_AFTER);

    foreach (config('app.supported_locales') as $code => $locale) {
        expect($html)->toContain('?lang='.$code)
            ->and($html)->toContain(e($locale['native']));
    }

    expect($html)->toContain('lang="fr"');
});
