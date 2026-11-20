<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../Helpers/CompanyHelper.php';
require_once __DIR__.'/../../Helpers/TestBootstrapHelper.php';

/**
 * User, 2026-11-20: opening a module page the company is not entitled to (or
 * lacks the permission for) showed a bare 403 or 405 page. A signed-in user
 * now lands on the dashboard with a notice. Access is still refused; only
 * where the refusal is shown changes.
 */
function renderRefusal(Throwable $e, string $path, string $method = 'GET', array $headers = []): Symfony\Component\HttpFoundation\Response
{
    $request = Request::create($path, $method, server: collect($headers)->mapWithKeys(fn ($v, $k) => ['HTTP_'.strtoupper(str_replace('-', '_', $k)) => $v])->all());

    app()->instance('request', $request);

    return app(ExceptionHandler::class)->render($request, $e);
}

beforeEach(function (): void {
    $this->actingAs(User::factory()->create(), 'web');
});

it('sends a refused admin page to the dashboard', function (): void {
    $response = renderRefusal(new HttpException(403), '/admin/sale/orders/quotations');

    expect($response->getStatusCode())->toBe(302)
        ->and($response->headers->get('Location'))->toBe(url('admin'));
});

it('sends a policy refusal to the dashboard too', function (): void {
    $response = renderRefusal(new AuthorizationException, '/admin/logistics/operations/shipments');

    expect($response->getStatusCode())->toBe(302);
});

it('sends a GET to a post-only admin address to the dashboard', function (): void {
    $response = renderRefusal(new MethodNotAllowedHttpException(['POST']), '/admin/some/post-only');

    expect($response->getStatusCode())->toBe(302);
});

it('still refuses the dashboard itself instead of redirecting to it forever', function (): void {
    expect(renderRefusal(new HttpException(403), '/admin')->getStatusCode())->toBe(403);
});

it('still refuses API, JSON and form posts with a 403', function (): void {
    expect(renderRefusal(new HttpException(403), '/admin/api/v1/sales/orders')->getStatusCode())->toBe(403)
        ->and(renderRefusal(new HttpException(403), '/admin/sale/orders', 'GET', ['Accept' => 'application/json'])->getStatusCode())->toBe(403)
        ->and(renderRefusal(new HttpException(403), '/admin/company-context', 'POST')->getStatusCode())->toBe(403);
});

it('does not redirect a guest', function (): void {
    auth('web')->logout();

    expect(renderRefusal(new HttpException(403), '/admin/sale/orders/quotations')->getStatusCode())->toBe(403);
});
