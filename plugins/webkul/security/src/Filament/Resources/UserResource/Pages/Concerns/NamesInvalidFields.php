<?php

namespace Webkul\Security\Filament\Resources\UserResource\Pages\Concerns;

use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;

/**
 * The user form is long and its company fields sit in a side column, so a
 * refused save could look like nothing happened (TRACE365, 2026-11-19).
 * Name every message in a notice as well as under the field.
 */
trait NamesInvalidFields
{
    protected function onValidationError(ValidationException $exception): void
    {
        Notification::make()
            ->danger()
            ->title(__('security::filament/resources/user.form.validation.needs-attention'))
            ->body(e(implode(' ', array_unique($exception->validator->errors()->all()))))
            ->send();
    }
}
