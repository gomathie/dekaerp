<?php

use Illuminate\Support\Facades\Schema;
use Spatie\LaravelSettings\Migrations\SettingsMigration;
use Webkul\Security\Models\Role;

/**
 * The business role a new company administrator gets alongside "Company
 * Admin" (user, 2026-11-11). "Company Admin" alone manages users and nothing
 * else, so an administrator signed in to an almost empty panel.
 *
 * Preselects "Admin2" where it exists: that is the business role production's
 * working tenant administrators already hold. Elsewhere it stays empty until
 * someone chooses one on the Users settings page.
 */
return new class extends SettingsMigration
{
    public function up(): void
    {
        $roleId = Schema::hasTable('roles')
            ? Role::query()->where('guard_name', 'web')->whereRaw('LOWER(name) = ?', ['admin2'])->value('id')
            : null;

        $this->migrator->add('general.company_admin_business_role_id', $roleId ? (int) $roleId : null);
    }

    public function down(): void
    {
        $this->migrator->delete('general.company_admin_business_role_id');
    }
};
