<?php

use Illuminate\Support\Facades\Route;
use Webkul\Security\Filament\Resources\CompanyResource\Pages\CreateCompany;
use Webkul\Security\Filament\Resources\CompanyResource\Pages\EditCompany;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensureERPInstalled();
});

/**
 * Support's CompanyResource and Security's subclass both registered on the
 * "companies" slug, and Support's pages won the routes. Security's create and
 * edit pages - the only ones that act on the Administrator section - never
 * ran, so production never created a company administrator or invitation
 * (2026-11-11). Page tests mounted Security's classes directly and passed;
 * this asserts what the URL actually serves.
 */
it('serves the company create and edit pages from Security, which creates the administrator', function () {
    FilamentHelper::bootAdminPanel();

    expect(Route::getRoutes()->getByName('filament.admin.resources.companies.create')?->getActionName())
        ->toBe(CreateCompany::class)
        ->and(Route::getRoutes()->getByName('filament.admin.resources.companies.edit')?->getActionName())
        ->toBe(EditCompany::class);
});
