<?php

namespace Webkul\Security\Filament\Resources\CompanyResource\Pages;

use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Webkul\Security\Filament\Resources\CompanyResource;
use Webkul\Security\Models\User;
use Webkul\Security\Services\CompanyAdministratorInviter;
use Webkul\Security\Services\CompanyOnboarding;
use Webkul\Support\Filament\Concerns\ChecksPagePermission;
use Webkul\Support\Filament\Resources\CompanyResource\Pages\CreateCompany as BaseCreateCompany;

class CreateCompany extends BaseCreateCompany
{
    use ChecksPagePermission;

    protected static string $resource = CompanyResource::class;

    protected static string $lang = 'security::filament/resources/company.pages.create.administrator';

    // Keep Filament's relationship saves in the same outer transaction. Mail
    // registered after commit cannot leave before the whole page is durable.
    protected ?bool $hasDatabaseTransactions = true;

    protected ?array $onboardingResult = null;

    protected function handleRecordCreation(array $data): Model
    {
        $actor = Auth::user();

        if (! $actor instanceof User) {
            abort(403);
        }

        $email = trim((string) ($this->data['administrator_email'] ?? ''));
        $this->onboardingResult = app(CompanyOnboarding::class)->create(
            $actor,
            $data,
            $email === '' ? null : $email,
            (string) ($this->data['administrator_password'] ?? ''),
            (bool) ($this->data['administrator_must_change_password'] ?? true),
        );

        return $this->onboardingResult['company'];
    }

    protected function afterCreate(): void
    {
        if (! $this->onboardingResult) {
            return;
        }

        $email = trim((string) ($this->data['administrator_email'] ?? ''));
        $email = $email === '' ? (string) $this->getRecord()->email : $email;

        if ($this->onboardingResult['password']) {
            $lang = 'security::filament/resources/company.pages.administrator-account.created';
            Notification::make('companyAdministratorPasswordSet')
                ->title(__($lang.'.title'))
                ->body(__($lang.'.body', ['email' => $email]))
                ->success()->send();

            return;
        }

        if ($this->onboardingResult['outcome'] === CompanyAdministratorInviter::DISABLED) {
            return;
        }

        Notification::make('companyAdministratorInvited')
            ->title(__(static::$lang.'.invited.title'))
            ->body(__(static::$lang.'.invited.body', ['email' => $email]))
            ->success()->send();
    }
}
