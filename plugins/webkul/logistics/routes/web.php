<?php

use Illuminate\Support\Facades\Route;
use Webkul\Logistics\Http\Controllers\StopLinkController;

/*
 * The POD capture link (D15, WP-5b) - the only public route in this plugin.
 *
 * The middleware is declared here, not inherited: PackageServiceProvider loads
 * plugin web route files with a plain loadRoutesFrom(), which applies no group
 * at all. Without this the page would have no session and therefore no CSRF
 * protection on the form, and no throttling on an unauthenticated endpoint that
 * accepts file uploads.
 *
 * The token is not a route parameter to look a record up by id - it is the
 * credential, so it is matched on shape here and only ever compared as a hash.
 */
Route::middleware(['web', 'throttle:logistics-stop-link'])
    ->prefix('logistics/pod')
    ->group(function (): void {
        Route::get('{token}', [StopLinkController::class, 'show'])
            ->where('token', '[A-Za-z0-9]{32,128}')
            ->name('logistics.stop-link.show');

        Route::post('{token}', [StopLinkController::class, 'store'])
            ->where('token', '[A-Za-z0-9]{32,128}')
            ->name('logistics.stop-link.store');
    });
