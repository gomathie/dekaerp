<?php

use Webkul\Logistics\Models\Shipment;
use Webkul\Logistics\Services\CompanyProvisioner;
use Webkul\Logistics\Support\LogisticsAccess;
use Webkul\Partner\Models\Partner;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/CompanyScopeHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/SecurityHelper.php';

class LogisticsHelper
{
    public static function install(): void
    {
        TestBootstrapHelper::ensurePluginInstalled('accounts');
        TestBootstrapHelper::ensurePluginInstalled('logistics');

        LogisticsAccess::flush();
    }

    public static function company(array $overrides = []): Company
    {
        return CompanyHelper::company($overrides);
    }

    public static function enable(Company $company): Company
    {
        app(CompanyProvisioner::class)->enable($company);

        return $company;
    }

    public static function disable(Company $company): Company
    {
        app(CompanyProvisioner::class)->disable($company);

        return $company;
    }

    public static function shipment(Company $company, array $overrides = []): Shipment
    {
        return Shipment::factory()
            ->forCompany($company)
            ->create(array_merge([
                'customer_id' => Partner::factory()->create(['company_id' => $company->id])->id,
            ], $overrides));
    }
}
