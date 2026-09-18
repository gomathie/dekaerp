<?php

namespace Webkul\Security\Database\Seeders;

use Illuminate\Database\Seeder;
use Webkul\Security\Services\MultiCompanyAdminRoleProvisioner;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @param  array  $parameters
     * @return void
     */
    public function run($parameters = []): void
    {
        app(MultiCompanyAdminRoleProvisioner::class)->provision();
    }
}
