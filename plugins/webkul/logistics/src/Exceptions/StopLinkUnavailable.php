<?php

namespace Webkul\Logistics\Exceptions;

use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * A stop link that cannot be used: unknown, expired, already used or revoked.
 *
 * Deliberately one exception for all four, carrying no detail. This is thrown on
 * a public, unauthenticated route, and telling a caller *why* a token failed
 * tells them whether it ever existed - which turns the endpoint into an oracle
 * for guessing tokens. The recipient sees one "this link is no longer valid"
 * page whatever the reason, and the dispatcher can see the real state of the
 * link in the panel.
 */
class StopLinkUnavailable extends NotFoundHttpException
{
    public static function make(): self
    {
        return new self;
    }

    /**
     * A plain 404 would satisfy the security requirement but leaves a driver at
     * a customer's gate staring at an error page. Same status, same absence of
     * detail, with a sentence telling them to ask the office for a new link.
     */
    public function render(): Response
    {
        return response()->view('logistics::stop-link.expired', [], 404);
    }
}
