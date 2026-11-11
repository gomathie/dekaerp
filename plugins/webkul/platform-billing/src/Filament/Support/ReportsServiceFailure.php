<?php

namespace Webkul\PlatformBilling\Filament\Support;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Exceptions\Halt;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use Livewire\Component;
use Throwable;
use Webkul\Security\Models\User;

/**
 * Puts a service refusal on the screen without rewriting the rule.
 *
 * Validation keys from CompanyReadOnlyWriteGuard use `company_id`, which is
 * not a field on these actions. The guard already notifies. This also sends
 * the message so a Filament action, whose form has no matching field, still
 * shows it.
 */
class ReportsServiceFailure
{
    public static function notify(Throwable $exception): void
    {
        Notification::make()
            ->danger()
            ->title(static::message($exception))
            ->send();
    }

    /**
     * Create pages have fields named like the service's error keys. Halt
     * rolls the page transaction back; the service already refused the write.
     */
    public static function haltOnForm(Component $page, Throwable $exception): never
    {
        if ($exception instanceof ValidationException) {
            foreach ($exception->errors() as $key => $messages) {
                foreach ((array) $messages as $message) {
                    $page->addError('data.'.$key, (string) $message);
                }
            }

            static::notify($exception);

            throw new Halt;
        }

        if ($exception instanceof AuthorizationException || $exception instanceof InvalidArgumentException) {
            static::notify($exception);

            throw new Halt;
        }

        throw $exception;
    }

    /**
     * Same shape as the Users page: a validation error lands on the action
     * field that carries the service's key, and an authorization refusal is
     * the danger notification. The notification stays as well, because
     * CompanyReadOnlyWriteGuard reports `company_id`, which is not a field
     * on these actions.
     */
    public static function onAction(Action $action, Throwable $exception): never
    {
        if ($exception instanceof ValidationException) {
            static::notify($exception);

            throw ValidationException::withMessages(collect($exception->errors())
                ->mapWithKeys(fn (array $messages, string $key): array => ['mountedActions.0.data.'.$key => $messages])
                ->all());
        }

        if ($exception instanceof AuthorizationException || $exception instanceof InvalidArgumentException) {
            static::notify($exception);
            $action->halt();
        }

        throw $exception;
    }

    public static function actor(): User
    {
        $actor = auth()->user();

        if (! $actor instanceof User) {
            abort(403);
        }

        return $actor;
    }

    /**
     * Filament's integer input can arrive as a whole-number float. Services
     * that type an amount as int would otherwise refuse a valid entry.
     */
    public static function wholeNumber(mixed $value): mixed
    {
        if (is_int($value)) {
            return $value;
        }

        if (is_string($value) && preg_match('/^\d+$/', trim($value)) === 1) {
            return (int) $value;
        }

        if (is_float($value) && $value >= 0 && floor($value) === $value) {
            return (int) $value;
        }

        return $value;
    }

    public static function message(Throwable $exception): string
    {
        if ($exception instanceof ValidationException) {
            return (string) collect($exception->errors())->flatten()->first();
        }

        if ($exception instanceof AuthorizationException || $exception instanceof InvalidArgumentException) {
            return $exception->getMessage();
        }

        throw $exception;
    }
}
