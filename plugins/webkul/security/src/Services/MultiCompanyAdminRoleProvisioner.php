<?php

namespace Webkul\Security\Services;

use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;
use Webkul\Security\Models\Permission;
use Webkul\Security\Models\Role;

class MultiCompanyAdminRoleProvisioner
{
    public const BASE_PERMISSIONS = [
        'view_any_security_user',
        'view_security_user',
        'create_security_user',
        'update_security_user',
        'delete_security_user',
        'delete_any_security_user',
        'view_any_security_company',
        'view_security_company',
    ];

    public function provision(): Role
    {
        return DB::transaction(function (): Role {
            $role = Role::query()
                ->where('guard_name', 'web')
                ->whereRaw('LOWER(name) = ?', [mb_strtolower(Role::MULTI_COMPANY_ADMIN)])
                ->first();

            $role ??= Role::query()->create([
                'name'       => Role::MULTI_COMPANY_ADMIN,
                'guard_name' => 'web',
                'is_default' => false,
            ]);

            $permissions = collect(self::BASE_PERMISSIONS)
                ->map(fn (string $name): Permission => Permission::query()->firstOrCreate([
                    'name'       => $name,
                    'guard_name' => 'web',
                ]));

            // map() gives a plain support collection, which has no modelKeys().
            $role->permissions()->syncWithoutDetaching($permissions->map->getKey()->all());

            app(PermissionRegistrar::class)->forgetCachedPermissions();

            return $role->refresh();
        });
    }
}
