<?php

use Filament\Facades\Filament;
use Illuminate\Support\Once;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

require_once __DIR__.'/SecurityHelper.php';
require_once __DIR__.'/CompanyHelper.php';

class FilamentHelper
{
    public static function bootAdminPanel(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        Filament::bootCurrentPanel();

        // FilamentShield memoizes getPages()/getWidgets() with once() for the
        // whole process. A plugin install earlier in the same test (the first
        // test of a file installs the ERP) fills that cache before the
        // plugin's pages are registered, so the page list came back empty and
        // permission-name tests failed by test order alone. A real request
        // starts fresh, so only tests need this.
        Once::flush();
    }

    /**
     * Admin panel resources of the given namespace that build their own global
     * search details instead of inheriting the Filament default.
     *
     * @return array<int, class-string>
     */
    public static function resourcesWithOwnGlobalSearchDetails(string $namespace): array
    {
        static::bootAdminPanel();

        $resources = [];

        foreach (Filament::getPanel('admin')->getResources() as $resource) {
            if (! str_starts_with($resource, $namespace)) {
                continue;
            }

            $method = new ReflectionMethod($resource, 'getGlobalSearchResultDetails');

            if ($method->getDeclaringClass()->getName() === $resource) {
                $resources[] = $resource;
            }
        }

        sort($resources);

        return $resources;
    }

    public static function actingAs(array $permissions = [], bool $global = true): User
    {
        $user = SecurityHelper::authenticateWithPermissions($permissions);

        $attributes = ['default_company_id' => Company::query()->value('id')];

        if ($global) {
            $attributes['resource_permission'] = PermissionType::GLOBAL;
        }

        $user->forceFill($attributes)->saveQuietly();

        static::bootAdminPanel();

        return $user;
    }

    public static function actingAsCompanyUser($companies, array $permissions = [], bool $global = true): User
    {
        $user = CompanyHelper::actingAsCompanyUser($companies, $permissions);

        if ($global) {
            $user->forceFill(['resource_permission' => PermissionType::GLOBAL])->saveQuietly();
        }

        static::bootAdminPanel();

        return $user;
    }
}
