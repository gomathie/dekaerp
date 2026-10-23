<?php

use Filament\Facades\Filament;

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

    $middleware = Filament::getPanel('admin')->getMiddleware();

    // Pins the specific regression: the admin panel must carry the middleware,
    // and under the namespace it is actually declared in.
    expect($middleware)->toContain(\Webkul\Security\Http\Middleware\RequirePasswordChange::class)
        ->and($middleware)->not->toContain('App\Providers\Filament\RequirePasswordChange');
});
