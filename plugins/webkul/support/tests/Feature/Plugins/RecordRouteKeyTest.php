<?php

use Illuminate\Support\Facades\Route;

require_once __DIR__.'/../../Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../Helpers/FilamentHelper.php';

/**
 * A record address that is not a number is "not found", never a server error.
 *
 * Sentry DEKAERP-15 (2026-11-12): /admin/invoices/overview matched the invoice
 * view route, so "overview" reached PostgreSQL as an id on a bigint column and
 * the page answered 500. AppServiceProvider now restricts {record} to digits
 * for every route, so such an address no longer matches and answers 404.
 */
it('restricts every record route parameter to digits', function () {
    FilamentHelper::bootAdminPanel();

    $routes = collect(Route::getRoutes()->getRoutes())
        ->filter(fn ($route) => in_array('record', $route->parameterNames(), true));

    // Without this the check below could pass by looking at nothing.
    expect($routes)->not->toBeEmpty();

    $unrestricted = $routes
        ->reject(fn ($route) => ($route->wheres['record'] ?? null) === '[0-9]+')
        ->map(fn ($route) => $route->uri())
        ->values()
        ->all();

    expect($unrestricted)->toBe([]);
});

it('answers 404, not 500, for a record address that is not a number', function () {
    FilamentHelper::bootAdminPanel();

    $uri = collect(Route::getRoutes()->getRoutes())
        ->map(fn ($route) => $route->uri())
        ->first(fn (string $uri) => str_starts_with($uri, 'admin/') && str_ends_with($uri, '/{record}'));

    expect($uri)->not->toBeNull();

    $this->get('/'.str_replace('{record}', 'overview', $uri))->assertNotFound();
});
