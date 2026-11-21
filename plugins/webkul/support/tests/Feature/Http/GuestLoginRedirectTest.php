<?php

/**
 * Sentry DEKAERP-1Y, 2026-11-21: a signed-out browser loading an avatar from
 * /secure-storage got a 500 "Route [login] not defined", because the 'login'
 * redirect in routes/web.php was behind a guard that was always false. A guest
 * on any 'auth' route is sent to the panel login instead, and still sees no
 * file.
 */
it('defines the login route the auth middleware sends guests to', function (): void {
    expect(route('login', absolute: false))->toBe('/login');
});

it('sends a guest asking for a stored file to the login page', function (): void {
    $this->get('/secure-storage/companies/1/users/avatars/example.png')
        ->assertRedirect(route('login'));
});

it('forwards the login route to the panel login', function (): void {
    $this->get('/login')->assertRedirect('/admin/login');
});
