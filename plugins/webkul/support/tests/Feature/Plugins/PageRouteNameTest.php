<?php

use Filament\Facades\Filament;
use Illuminate\Support\Facades\Route;

require_once __DIR__.'/../../Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../Helpers/FilamentHelper.php';

/**
 * Every page's getRouteName() must name a route that is actually registered.
 *
 * On 2026-10-23 /admin/time-off/dashboard returned a 500: the page overrode
 * getRouteName() with the hardcoded 'filament.admin.pages.time-off-dashboard',
 * but it lives in the MyTime cluster, so Filament had registered it as
 * 'filament.admin.time-off.dashboard.pages.dashboard'. Page::getUrl() calls
 * route() on whatever getRouteName() returns, and building the cluster's
 * sub-navigation calls getUrl() on every sibling - so one wrong string took the
 * page down.
 *
 * Five pages carried such an override. The three outside a cluster happened to
 * match the generated name; the two inside one could not. That is the dangerous
 * shape: the mistake is invisible until someone opens the page.
 *
 * Registration and URL generation are two different code paths here, which is
 * why this compares them rather than trusting either alone.
 */
it('resolves every registered page to a route that exists', function () {
    FilamentHelper::bootAdminPanel();

    $panel = Filament::getPanel('admin');

    $pages = $panel->getPages();

    // Without this the loop could pass by iterating nothing.
    expect($pages)->not->toBeEmpty();

    $broken = [];
    $checked = 0;

    foreach ($pages as $page) {
        if (! is_string($page) || ! class_exists($page)) {
            continue;
        }

        if (! method_exists($page, 'getRouteName')) {
            continue;
        }

        $name = $page::getRouteName($panel);

        $checked++;

        if (! Route::has($name)) {
            $broken[] = $page.' => '.$name;
        }
    }

    expect($checked)->toBeGreaterThan(0)
        ->and($broken)->toBe([]);
});

it('builds a URL for every registered page without throwing', function () {
    FilamentHelper::bootAdminPanel();

    $panel = Filament::getPanel('admin');

    $failed = [];
    $checked = 0;

    foreach ($panel->getPages() as $page) {
        if (! is_string($page) || ! class_exists($page) || ! method_exists($page, 'getUrl')) {
            continue;
        }

        $checked++;

        try {
            // This is the call the cluster sub-navigation makes, and the one
            // that actually threw in production.
            $page::getUrl();
        } catch (Throwable $e) {
            $failed[] = $page.': '.$e->getMessage();
        }
    }

    expect($checked)->toBeGreaterThan(0)
        ->and($failed)->toBe([]);
});
