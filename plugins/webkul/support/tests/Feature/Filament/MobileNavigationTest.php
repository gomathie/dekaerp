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
 * Menu button, and the module drawer lists the current module's pages above
 * the modules. Layout itself is CSS and is checked on a phone, not here.
 */
it('renders one labelled menu button and the current module pages in the drawer', function () {
    $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();
    Auth::guard('web')->login($admin);

    // A page that is in the navigation, so its module (Settings) is active.
    $response = $this->get(UserResource::getUrl('index'))->assertOk();

    // Markup only: the class names also appear in the page's CSS.
    $response->assertSee('class="module-mobile-menu-btn"', false)
        ->assertSee(__('admin.mobile-menu.menu'))
        ->assertSee('<div class="module-sidebar-current">', false)
        ->assertSee('module-sidebar-link module-sidebar-page-link', false)
        ->assertSee(__('admin.mobile-menu.modules'))
        ->assertSee('class="company-switcher-name"', false)
        // The phone CSS finds the switcher's panel by this class, to keep it
        // on screen (2026-11-11: its Reset button was cut off at 390px).
        ->assertSee('class="company-switcher-form"', false);
});
