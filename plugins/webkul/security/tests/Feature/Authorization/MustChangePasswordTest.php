<?php

use Illuminate\Http\Request;
use Webkul\Security\Http\Middleware\RequirePasswordChange;
use Webkul\Security\Models\User;
use Webkul\Support\Filament\Pages\Profile;

require_once __DIR__.'/MultiCompanyAdminTest.php';

/**
 * `must_change_password` exists so an administrator who sets someone's password
 * by hand can require it to be replaced, instead of a password shared over chat
 * staying valid forever (user, 2026-10-21).
 *
 * The flag is only worth anything if something enforces it, so that is what is
 * asserted here rather than the column's presence.
 */
it('defaults to false on a user built in memory, matching the column default', function () {
    // The trap documented on User::$attributes: a column default is applied by
    // the database and not read back, so a model created in memory would carry
    // null and every check against it would be falsy by accident rather than by
    // intent. Asserted on an unsaved instance on purpose.
    expect((new User)->must_change_password)->toBeFalse();
});

it('sends a flagged user to the profile page instead of where they asked to go', function () {
    $user = multiCompanyAdminTestUser();
    $user->forceFill(['must_change_password' => true])->save();

    multiCompanyAdminTestAuthenticate($user, []);

    $request = Request::create('/admin/users', 'GET');

    $response = app(RequirePasswordChange::class)->handle($request, fn (): mixed => 'passed-through');

    expect($response)->not->toBe('passed-through')
        ->and($response->getStatusCode())->toBe(302)
        ->and($response->headers->get('location'))->toBe(Profile::getUrl());
});

it('lets the profile page itself through, or the user could never comply', function () {
    $user = multiCompanyAdminTestUser();
    $user->forceFill(['must_change_password' => true])->save();

    multiCompanyAdminTestAuthenticate($user, []);

    $request = Request::create(strtok(Profile::getUrl(), '?'), 'GET');

    $passed = app(RequirePasswordChange::class)->handle($request, fn (): string => 'passed-through');

    expect($passed)->toBe('passed-through');
});

it('does not interfere with a user who is not flagged', function () {
    $user = multiCompanyAdminTestUser();

    multiCompanyAdminTestAuthenticate($user, []);

    $request = Request::create('/admin/users', 'GET');

    $passed = app(RequirePasswordChange::class)->handle($request, fn (): string => 'passed-through');

    expect($user->must_change_password)->toBeFalse()
        ->and($passed)->toBe('passed-through');
});

it('clears the requirement once the password is changed', function () {
    $user = multiCompanyAdminTestUser();
    $user->forceFill(['must_change_password' => true])->save();

    $user->update(['password' => 'a-brand-new-secret']);

    expect($user->refresh()->must_change_password)->toBeFalse();
});

it('keeps the requirement when an administrator sets a password and flags it at once', function () {
    // Otherwise the auto-clear would cancel the very thing being asked for:
    // setting someone's password and requiring them to replace it is one save.
    $user = multiCompanyAdminTestUser();

    $user->update([
        'password'             => 'temporary-handover-secret',
        'must_change_password' => true,
    ]);

    expect($user->refresh()->must_change_password)->toBeTrue();
});
