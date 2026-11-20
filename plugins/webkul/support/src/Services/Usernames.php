<?php

namespace Webkul\Support\Services;

use Closure;
use Illuminate\Database\Eloquent\Builder;

/**
 * Every username rule, in one place (user, 2026-11-19): sign in with email
 * or username. A username is optional, unique across the installation, not
 * case-sensitive, and never contains "@", so a sign-in name with "@" is
 * always an email and one without is always a username.
 *
 * Lives in support, not security, because the Profile page (support) sets a
 * username too; users are read through the configured auth model.
 */
class Usernames
{
    public const PATTERN = '/^[A-Za-z0-9._-]{3,50}$/';

    /**
     * Validation rules for the user form and the profile page.
     *
     * @return list<mixed>
     */
    public static function rules(?int $ignoreUserId = null): array
    {
        return [
            'nullable',
            'string',
            'regex:'.static::PATTERN,
            function (string $attribute, mixed $value, Closure $fail) use ($ignoreUserId): void {
                if (blank($value)) {
                    return;
                }

                $taken = static::users()
                    ->whereRaw('LOWER(username) = ?', [mb_strtolower(trim((string) $value))])
                    ->when($ignoreUserId, fn (Builder $query) => $query->whereKeyNot($ignoreUserId))
                    ->exists();

                if ($taken) {
                    $fail(__('support::support.username-taken'));
                }
            },
        ];
    }

    /**
     * The email to authenticate for what was typed in the sign-in field.
     *
     * An unknown username returns what was typed, so the attempt fails with
     * the same message as a wrong password: the sign-in page must not reveal
     * which usernames exist.
     */
    public static function emailForLogin(string $login): string
    {
        $login = trim($login);

        if ($login === '' || str_contains($login, '@')) {
            return $login;
        }

        $email = static::users()
            ->whereRaw('LOWER(username) = ?', [mb_strtolower($login)])
            ->value('email');

        return $email ?? $login;
    }

    public static function normalize(?string $username): ?string
    {
        $username = trim((string) $username);

        return $username === '' ? null : $username;
    }

    protected static function users(): Builder
    {
        $model = config('auth.providers.users.model');

        return $model::query()->withoutGlobalScopes();
    }
}
