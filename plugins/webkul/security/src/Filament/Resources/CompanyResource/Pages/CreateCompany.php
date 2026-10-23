<?php

namespace Webkul\Security\Filament\Resources\CompanyResource\Pages;

use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Webkul\Security\Filament\Resources\CompanyResource;
use Webkul\Security\Models\Invitation;
use Webkul\Security\Models\User;
use Webkul\Security\Services\CompanyAdministratorInviter;
use Webkul\Support\Filament\Resources\CompanyResource\Pages\CreateCompany as BaseCreateCompany;

class CreateCompany extends BaseCreateCompany
{
    protected static string $resource = CompanyResource::class;

    protected static string $lang = 'security::filament/resources/company.pages.create.administrator';

    /**
     * Invite the new company's administrator. The decision and all of its
     * failure modes live in CompanyAdministratorInviter so they can be tested
     * without mounting this page; here we only turn the outcome into a message.
     */
    protected function afterCreate(): void
    {
        $actor = Auth::user();

        if (! $actor instanceof User) {
            return;
        }

        // The administrator's own address, from the contributed "Administrator"
        // section. It is dehydrated(false) - not a company column - so it is
        // read from the raw form state. Blank falls back to the company's
        // address inside the inviter, which is what it always used.
        $administratorEmail = trim((string) ($this->data['administrator_email'] ?? ''));

        $reason = app(CompanyAdministratorInviter::class)->invite(
            $this->getRecord(),
            $actor,
            $administratorEmail === '' ? null : $administratorEmail,
        );

        if ($reason === CompanyAdministratorInviter::DISABLED) {
            return;
        }

        $invitedEmail = $administratorEmail === ''
            ? (string) $this->getRecord()->email
            : $administratorEmail;

        if ($reason === CompanyAdministratorInviter::INVITED) {
            Notification::make('companyAdministratorInvited')
                ->title(__(static::$lang.'.invited.title'))
                ->body(__(static::$lang.'.invited.body', ['email' => $invitedEmail]))
                ->success()
                ->send();

            return;
        }

        // Persistent: the company exists but nobody can administer it yet, and
        // that needs to survive the page transition to be acted on.
        Notification::make('companyAdministratorNotInvited')
            ->title(__(static::$lang.'.skipped.title'))
            ->body(__(static::$lang.'.skipped.'.$reason, [
                // Only MAIL_FAILED's message uses this. The invitation exists
                // but its link was only ever inside the email that failed, so
                // without showing it here the tenant cannot be onboarded at all.
                'link' => $reason === CompanyAdministratorInviter::MAIL_FAILED
                    ? (Invitation::query()
                        ->where('email', $invitedEmail)
                        ->latest('id')
                        ->first()
                        ?->acceptUrl() ?? '')
                    : '',
            ]))
            ->warning()
            ->persistent()
            ->send();
    }
}
