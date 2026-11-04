<?php

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Http\Request;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;

/**
 * Livewire refuses a request that tries to write a #[Locked] property, and
 * anyone can send one - the login page included, before authenticating. Before
 * this guard the request answered 500 and reported itself to Sentry, so a
 * single probe became issue 151536596 and could be repeated without limit.
 * It is a bad request, not a server fault.
 */
it('answers a tampered Livewire property update with a client error, not a 500', function () {
    $response = app(ExceptionHandler::class)->render(
        Request::create('/livewire/abc123/update', 'POST'),
        new CannotUpdateLockedPropertyException('discoveredSchemaNames'),
    );

    expect($response->getStatusCode())->toBe(400);
});

it('does not report a tampered Livewire property update', function () {
    $handler = app(ExceptionHandler::class);

    $reported = false;

    $handler->reportable(function () use (&$reported): void {
        $reported = true;
    });

    $handler->report(new CannotUpdateLockedPropertyException('discoveredSchemaNames'));

    expect($reported)->toBeFalse();
});
