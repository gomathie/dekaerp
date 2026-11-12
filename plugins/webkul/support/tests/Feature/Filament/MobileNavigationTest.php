<?php

use Illuminate\Support\Facades\Auth;
use Webkul\Security\Models\User;
use Webkul\Security\Filament\Resources\UserResource;

require_once __DIR__.'/../../Helpers/TestBootstrapHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensureERPInstalled();
});

/**
 * On a phone, navigation was two unlabelled 24px icons side by side: one
 * opened the module's pages, the other the module list, and nothing said
 * which (mobile plan F2/P2, 2026-11-10). The page now carries one labelled
 * Menu button.
 *
 * Changed 2026-11-12 (user: the drawer was cumbersome): the current module's
 * pages are no longer listed above the modules in the drawer. They sit in a
 * one-line strip under the top bar, marked with aria-current, and the drawer
 * lists modules only. Layout itself is CSS and is checked on a phone, not here.
 */
it('renders one labelled menu button and the current module pages as a strip under the top bar', function () {
    $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();
    Auth::guard('web')->login($admin);

    // A page that is in the navigation, so its module (Settings) is active.
    $response = $this->get(UserResource::getUrl('index'))->assertOk();

    // Markup only: the class names also appear in the page's CSS.
    $response->assertSee('class="module-mobile-menu-btn"', false)
        ->assertSee(__('admin.mobile-menu.menu'))
        ->assertSee('<nav class="module-mobile-tabs"', false)
        ->assertSee('module-mobile-tab is-active', false)
        ->assertSee('aria-current="page"', false)
        ->assertDontSee('module-sidebar-current', false)
        ->assertSee('class="company-switcher-name"', false)
        // The phone CSS finds the switcher's panel by this class, to keep it
        // on screen (2026-11-11: its Reset button was cut off at 390px).
        ->assertSee('class="company-switcher-form"', false);
});
