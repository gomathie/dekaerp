<?php

namespace Webkul\Security\Filament\Resources\CompanyResource\Pages\Concerns;

use Filament\Notifications\Notification;
use Webkul\Security\Models\User;
use Webkul\Security\Services\CompanyAdministratorAccount;

/**
 * The password half of the company form's "Administrator" section, shared by
 * the create and edit pages (user, 2026-11-03).
 *
 * The decision and its refusals live in CompanyAdministratorAccount; this only
 * turns the outcome into a message and clears the fields.
 */
trait SetsAdministratorPassword
{
    /**
     * Set the administrator's password when one was typed.
     *
     * Returns false when the password field was blank, so the page goes on to
     * its invitation path instead.
     */
    protected function setAdministratorPassword(User $actor): bool
    {
        $password = (string) ($this->data['administrator_password'] ?? '');

        if ($password === '') {
            return false;
        }

        $email = trim((string) ($this->data['administrator_email'] ?? ''));

        $reason = app(CompanyAdministratorAccount::class)->setPassword(
            $this->getRecord(),
            $actor,
            $email,
            $password,
            (bool) ($this->data['administrator_must_change_password'] ?? true),
        );

        $lang = 'security::filament/resources/company.pages.administrator-account';

        if (in_array($reason, [CompanyAdministratorAccount::CREATED, CompanyAdministratorAccount::PASSWORD_RESET], true)) {
            Notification::make('companyAdministratorPasswordSet')
                ->title(__($lang.'.'.$reason.'.title'))
                ->body(__($lang.'.'.$reason.'.body', ['email' => $email]))
                ->success()
                ->send();

            return true;
        }

        // Persistent, like the invitation's: nobody can sign in yet, and the
        // reason has to survive the redirect to be acted on.
        Notification::make('companyAdministratorPasswordRefused')
            ->title(__($lang.'.refused.title'))
            ->body(__($lang.'.refused.'.$reason, ['email' => $email]))
            ->warning()
            ->persistent()
            ->send();

        return true;
    }

    /**
     * Empty the section after it has been acted on.
     *
     * None of these is a company column, so Filament never refills them from
     * the record. Left in place, the next save of the same page would set the
     * password, or send the invitation, a second time.
     */
    protected function clearAdministratorFields(): void
    {
        $this->data['administrator_email'] = null;
        $this->data['administrator_password'] = null;
        $this->data['administrator_must_change_password'] = true;
    }
}
