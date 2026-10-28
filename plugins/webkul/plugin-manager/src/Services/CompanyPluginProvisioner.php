<?php

namespace Webkul\PluginManager\Services;

use Webkul\Support\Models\Company;

/**
 * Gives a new company the plugins it is meant to start with.
 *
 * Without this, per-company availability would create a cliff: the migration
 * backfills existing companies, but a company created afterwards would start
 * with nothing and look like a broken install. Which plugins those are is
 * `config('plugin-manager.default_company_plugins')`; `null` there means the
 * installed set, which keeps the behaviour identical to before the setting
 * existed, and leaves taking access away as the deliberate act.
 *
 * Needs no acting user, so it is driven by a model event rather than from the
 * create page - that way seeders, factories, console commands and the UI all
 * behave the same.
 *
 * Delegates to PluginEntitlementService so there is one writer for
 * `company_plugin`, not two that can drift.
 */
class CompanyPluginProvisioner
{
    /**
     * @return int Number of plugins granted.
     */
    public function grantInstalledPlugins(Company $company): int
    {
        // The set itself lives in `config('plugin-manager.default_company_plugins')`;
        // null there means every installed plugin, which is what this class did
        // before the setting existed. Additive either way - an existing grant is
        // never revoked by provisioning.
        return app(PluginEntitlementService::class)->grantDefaults($company);
    }
}
