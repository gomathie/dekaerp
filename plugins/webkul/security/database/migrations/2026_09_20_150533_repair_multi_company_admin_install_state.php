<?php

use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Webkul\Security\Models\Role;
use Webkul\Security\Services\MultiCompanyAdminRoleProvisioner;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('roles')) {
            return;
        }

        if (Schema::hasTable('permissions') && Schema::hasTable('role_has_permissions')) {
            app(MultiCompanyAdminRoleProvisioner::class)->provision();
        }

        if (! Schema::hasTable('settings')) {
            return;
        }

        $adminRoleId = Role::query()
            ->where('guard_name', 'web')
            ->whereRaw('LOWER(name) = ?', [mb_strtolower(Utils::getPanelUserRoleName())])
            ->value('id');

        $multiCompanyAdminRoleId = Role::query()
            ->where('guard_name', 'web')
            ->whereRaw('LOWER(name) = ?', [mb_strtolower(Role::MULTI_COMPANY_ADMIN)])
            ->value('id');

        if (! $adminRoleId || ! $multiCompanyAdminRoleId || (int) $adminRoleId === (int) $multiCompanyAdminRoleId) {
            return;
        }

        $settings = DB::table('settings')
            ->where('group', 'general')
            ->where('name', 'default_role_id');

        if (Schema::hasColumn('settings', 'company_id')) {
            $settings->whereNull('company_id');
        }

        $settings
            ->get(['id', 'payload'])
            ->filter(fn (object $setting): bool => (int) json_decode($setting->payload, true) === (int) $multiCompanyAdminRoleId)
            ->each(fn (object $setting) => DB::table('settings')
                ->where('id', $setting->id)
                ->update([
                    'payload'    => json_encode((int) $adminRoleId),
                    'updated_at' => now(),
                ]));
    }

    public function down(): void
    {
        // Role permissions and production settings are intentionally not reverted.
    }
};
