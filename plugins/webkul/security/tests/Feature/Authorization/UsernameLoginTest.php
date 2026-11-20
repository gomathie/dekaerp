<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Livewire\Livewire;
use Webkul\Security\Filament\Pages\Auth\Login;
use Webkul\Security\Models\User;
use Webkul\Support\Services\Usernames;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';

/**
 * Sign in with email or username (user, 2026-11-19): optional, unique across
 * the installation, not case-sensitive, never containing "@", and a wrong
 * username fails exactly like a wrong password.
 */
beforeEach(function (): void {
    FilamentHelper::bootAdminPanel();
    Auth::forgetGuards();

    $this->user = User::factory()->create([
        'email'    => 'bennet@trace.test',
        'username' => 'Bennet',
        'password' => Hash::make('Correct-horse-42'),
    ]);
});

it('signs in with a username in any letter case', function () {
    Livewire::test(Login::class)
        ->fillForm(['email' => 'BENNET', 'password' => 'Correct-horse-42'])
        ->call('authenticate')
        ->assertHasNoFormErrors();

    expect(Auth::guard('web')->id())->toBe($this->user->getKey());
});

it('still signs in with the email', function () {
    Livewire::test(Login::class)
        ->fillForm(['email' => 'bennet@trace.test', 'password' => 'Correct-horse-42'])
        ->call('authenticate')
        ->assertHasNoFormErrors();

    expect(Auth::guard('web')->id())->toBe($this->user->getKey());
});

it('fails an unknown username with the same message as a wrong password', function () {
    $unknown = Livewire::test(Login::class)
        ->fillForm(['email' => 'nobody', 'password' => 'Correct-horse-42'])
        ->call('authenticate')
        ->errors()->get('data.email');

    $wrongPassword = Livewire::test(Login::class)
        ->fillForm(['email' => 'bennet', 'password' => 'wrong-password'])
        ->call('authenticate')
        ->errors()->get('data.email');

    expect($unknown)->not->toBeEmpty()
        ->and($unknown)->toBe($wrongPassword)
        ->and(Auth::guard('web')->check())->toBeFalse();
});

it('refuses a username already taken in another letter case, or one with @', function () {
    $taken = Validator::make(['username' => 'bennet'], ['username' => Usernames::rules()]);
    $own = Validator::make(['username' => 'BENNET'], ['username' => Usernames::rules($this->user->getKey())]);
    $withAt = Validator::make(['username' => 'ben@net'], ['username' => Usernames::rules()]);

    expect($taken->fails())->toBeTrue()
        ->and($own->fails())->toBeFalse()
        ->and($withAt->fails())->toBeTrue();
});
