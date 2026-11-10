<?php

namespace Webkul\Security\Settings;

use Spatie\LaravelSettings\Settings;

class UserSettings extends Settings
{
    public bool $enable_user_invitation;

    public bool $enable_reset_password;

    public ?int $default_role_id;

    public ?int $default_company_id;

    /**
     * Given to every new company administrator alongside "Company Admin"
     * (user, 2026-11-11). Read only through
     * CompanyAdministratorInviter::administratorRoleIds(), which refuses a
     * platform role.
     */
    public ?int $company_admin_business_role_id;

    public static function group(): string
    {
        return 'general';
    }
}
