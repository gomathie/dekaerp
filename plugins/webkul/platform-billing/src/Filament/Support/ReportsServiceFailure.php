<?php

namespace Webkul\PlatformBilling\Filament\Support;

use Filament\Notifications\Notification;
use Filament\Support\Exceptions\Halt;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use Livewire\Component;
use Throwable;

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
