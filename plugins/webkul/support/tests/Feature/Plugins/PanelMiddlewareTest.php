<?php

use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;
use Webkul\Security\Http\Middleware\RequirePasswordChange;

require_once __DIR__.'/../../Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../Helpers/FilamentHelper.php';

/**
 * Every middleware a panel registers must be a class that actually exists.
 *
 * On 2026-10-23 /admin returned a 500 for every user in production:
 * AdminPanelProvider named RequirePasswordChange::class without importing it,
 * so PHP resolved the unqualified name against the provider's own namespace and
 * ::class produced the string "App\Providers\Filament\RequirePasswordChange".
 *
 * ::class on an unimported name is not a compile error - it just yields the
 * wrong string - so nothing failed until the container tried to build it, at
 * which point the whole panel was down. Pint, php -l and the middleware's own
 * unit test all passed: the middleware class was fine, it was the registration
 * that pointed at nothing.
 *
 * This resolves what the panels really report rather than reading the provider
 * source, so an import added for one panel and forgotten for another is caught
 * too.
 */
it('registers only middleware classes that exist, on every panel', function () {
    FilamentHelper::bootAdminPanel();

    $panels = Filament::getPanels();

    // Without this the loop below could pass by iterating nothing at all.
    expect($panels)->not->toBeEmpty();

    $missing = [];
    $checked = 0;

    foreach ($panels as $panel) {
        $middleware = array_merge($panel->getMiddleware(), $panel->getAuthMiddleware());

        foreach ($middleware as $entry) {
            // Closures and already-instantiated middleware need no resolving.
            if (! is_string($entry)) {
                continue;
            }

            // A middleware string may carry parameters, as in "throttle:60,1".
            $class = explode(':', $entry, 2)[0];

            // Laravel resolves aliases such as "auth" through the router, not
            // the autoloader, so only namespaced entries are class names.
            if (! str_contains($class, '\\')) {
                continue;
            }

            $checked++;

            if (! class_exists($class)) {
                $missing[] = $panel->getId().': '.$class;
            }
        }
    }

    expect($checked)->toBeGreaterThan(0)
        ->and($missing)->toBe([]);
});

it('registers the password-change middleware by its real class name', function () {
    FilamentHelper::bootAdminPanel();

    // It belongs to authMiddleware, not middleware: it inspects the signed-in
    // user, so it has to run after Authenticate has resolved one. Asserting the
    // bucket and the order together is what makes this a real pin - the first
    // version of this test looked only at getMiddleware() and failed against
    // correct code.
    $authMiddleware = Filament::getPanel('admin')->getAuthMiddleware();

    expect($authMiddleware)->toContain(RequirePasswordChange::class)
        ->and($authMiddleware)->not->toContain('App\Providers\Filament\RequirePasswordChange')
        ->and($authMiddleware)->toContain(Authenticate::class);

    expect(array_search(RequirePasswordChange::class, $authMiddleware, true))
        ->toBeGreaterThan(array_search(Authenticate::class, $authMiddleware, true));
});
