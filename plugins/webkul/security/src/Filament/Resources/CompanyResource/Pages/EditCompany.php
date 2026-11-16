<?php

namespace Webkul\Security\Filament\Resources\CompanyResource\Pages;

use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Webkul\Security\Filament\Resources\CompanyResource;
use Webkul\Security\Filament\Resources\CompanyResource\Pages\Concerns\SetsAdministratorPassword;
use Webkul\Security\Models\Invitation;
use Webkul\Security\Models\User;
use Webkul\Security\Services\CompanyAdministratorInviter;
use Webkul\Support\Filament\Resources\CompanyResource\Pages\EditCompany as BaseEditCompany;

class EditCompany extends BaseEditCompany
{
    use SetsAdministratorPassword;

    protected static string $resource = CompanyResource::class;

    protected static string $lang = 'security::filament/resources/company.pages.edit.administrator';

    /**
     * Give an existing company an administrator, or set their password
     * (user, 2026-11-03). Before this, only a company being created could get
     * one from this form.
     *
     * A blank email does nothing here, unlike create, which falls back to the
     * company's address: on edit that fallback would send an invitation on
     * every save.
     */
    protected function afterSave(): void
    {
        $actor = Auth::user();

        if (! $actor instanceof User) {
            return;
        }

        if ($this->setAdministratorPassword($actor)) {
            $this->clearAdministratorFields();

            return;
        }

        $administratorEmail = trim((string) ($this->data['administrator_email'] ?? ''));

        if ($administratorEmail === '') {
            return;
        }

        $reason = app(CompanyAdministratorInviter::class)->invite($this->getRecord(), $actor, $administratorEmail);

        $this->clearAdministratorFields();

        if ($reason === CompanyAdministratorInviter::INVITED) {
            Notification::make('companyAdministratorInvited')
                ->title(__('security::filament/resources/company.pages.create.administrator.invited.title'))
                ->body(__('security::filament/resources/company.pages.create.administrator.invited.body', ['email' => $administratorEmail]))
                ->success()
                ->send();

            return;
        }

        Notification::make('companyAdministratorNotInvited')
            ->title(__(static::$lang.'.skipped.title'))
            ->body(__(static::$lang.'.skipped.'.$reason, [
                // The link was only ever inside the email that failed.
                'link' => $reason === CompanyAdministratorInviter::MAIL_FAILED
                    ? (Invitation::query()
                        ->where('email', $administratorEmail)
                        ->latest('id')
                        ->first()
                        ?->acceptUrl() ?? '')
                    : '',
                'email' => $administratorEmail,
            ]))
            ->warning()
            ->persistent()
            ->send();
    }
}
