<?php

use Filament\Facades\Filament;
use Illuminate\Support\Facades\Route;
use Webkul\Support\Http\Controllers\ApiDocsController;

require_once __DIR__.'/../../Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../Helpers/FilamentHelper.php';

/**
 * /api/docs, its OpenAPI document and its Postman collection.
 *
 * Sentry DEKAERP-26, 2026-11-25: production answered 500 on /api/docs.openapi
 * because Scribe's route served storage/app/private/scribe/openapi.yaml, a file
 * only `scribe:generate` writes and the repository ignores. The documents are
 * now built from the committed .scribe/endpoints files. The reference is also
 * sign-in only (user, 2026-11-25), on all three URLs.
 */
dataset('api docs urls', [
    'page'    => ['/api/docs'],
    'openapi' => ['/api/docs.openapi'],
    'postman' => ['/api/docs.postman'],
]);

it('sends a guest to the panel login', function (string $url): void {
    $this->get($url)->assertRedirect(Filament::getPanel('admin')->getLoginUrl());
})->with('api docs urls');

it('answers a signed-in user', function (string $url): void {
    FilamentHelper::actingAs();

    $this->get($url)->assertOk();
})->with('api docs urls');

it('refuses a deactivated user whose session outlived the deactivation', function (): void {
    $user = FilamentHelper::actingAs();
    $user->forceFill(['is_active' => false])->saveQuietly();

    $this->get('/api/docs.openapi')->assertForbidden();
});

it('serves the docs from this application, not from Scribe\'s own routes', function (): void {
    // Scribe's routes read a file production never had; turning them back on
    // would also put the docs back in front of guests.
    expect(config('scribe.laravel.add_routes'))->toBeFalse();

    foreach (['scribe', 'scribe.openapi', 'scribe.postman'] as $name) {
        expect(Route::getRoutes()->getByName($name)?->getControllerClass())->toBe(ApiDocsController::class);
    }
});

it('builds a valid OpenAPI document from the committed endpoint files', function (): void {
    config(['app.url' => 'https://erp.example.test']);

    FilamentHelper::actingAs();

    $response = $this->get('/api/docs.openapi')->assertOk();

    $spec = json_decode((string) file_get_contents($response->baseResponse->getFile()->getPathname()), true, 512, JSON_THROW_ON_ERROR);

    expect($spec['openapi'] ?? null)->toStartWith('3.')
        ->and($spec['info']['title'] ?? null)->toBe('DEKA ERP API Documentation')
        ->and($spec['servers'][0]['url'] ?? null)->toBe('https://erp.example.test')
        ->and($spec['paths'])->toHaveKey('/admin/api/v1/sales/orders')
        ->and($spec['paths'])->toHaveKey('/admin/api/v1/login');
});

it('builds a Postman collection from the committed endpoint files', function (): void {
    FilamentHelper::actingAs();

    $response = $this->get('/api/docs.postman')->assertOk();

    $collection = json_decode((string) file_get_contents($response->baseResponse->getFile()->getPathname()), true, 512, JSON_THROW_ON_ERROR);

    expect($collection['item'] ?? [])->not->toBeEmpty();
});

it('loads a pinned, integrity-checked Scalar build under the product title', function (): void {
    FilamentHelper::actingAs();

    $this->get('/api/docs')
        ->assertOk()
        ->assertSee('<title>DEKA ERP API Documentation</title>', false)
        ->assertSee('https://cdn.jsdelivr.net/npm/@scalar/api-reference@1.76.4/dist/browser/standalone.js', false)
        ->assertSee('integrity="sha384-', false)
        ->assertSee(route('scribe.openapi'), false)
        ->assertDontSee('DEKA Central');
});
