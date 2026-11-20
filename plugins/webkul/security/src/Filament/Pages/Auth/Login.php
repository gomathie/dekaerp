<?php

namespace Webkul\Security\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use SensitiveParameter;
use Webkul\Support\Services\Usernames;

/**
 * Sign in with email or username (user, 2026-11-19). Only the field and the
 * credential lookup change: Filament's rate limiting, failure message and
 * multi-factor steps stay as they are, so a wrong username fails exactly
 * like a wrong password.
 */
class Login extends BaseLogin
{
    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label(__('security::filament/pages/login.email-or-username'))
            ->required()
            ->maxLength(255)
            ->autocomplete('username')
            ->autofocus();
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function getCredentialsFromFormData(#[SensitiveParameter] array $data): array
    {
        return [
            'email'    => Usernames::emailForLogin((string) $data['email']),
            'password' => $data['password'],
        ];
    }
}
