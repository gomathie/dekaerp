<?php

namespace Webkul\PluginManager\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Webkul\PluginManager\Support\PluginAccess;
use Webkul\Support\Models\Company;

/**
 * Gives a new company the plugins every other company already has.
 *
 * Without this, per-company availability would create a cliff: the migration
 * backfills existing companies, but a company created afterwards would start
 * with nothing and look like a broken install. Granting the installed set on
 * creation keeps the default behaviour identical to before the feature
 * existed, and leaves taking access away as the deliberate act.
 *
 * Needs no acting user, so it is driven by a model event rather than from the
 * create page - that way seeders, factories, console commands and the UI all
 * behave the same.
 */
class CompanyPluginProvisioner
{
    /**
     * @return int Number of plugins granted.
     */
    public function grantInstalledPlugins(Company $company): int
    {
        if (! Schema::hasTable('company_plugin') || ! Schema::hasTable('plugins')) {
            return 0;
        }

        $companyId = (int) $company->getKey();

        if (! $companyId) {
            return 0;
        }

        $pluginIds = DB::table('plugins')->where('is_installed', true)->pluck('id');

        if ($pluginIds->isEmpty()) {
            return 0;
        }

        $alreadyGranted = DB::table('company_plugin')
            ->where('company_id', $companyId)
            ->pluck('plugin_id')
            ->all();

        $now = now();

        $rows = $pluginIds
            ->reject(fn ($pluginId): bool => in_array($pluginId, $alreadyGranted))
            ->map(fn ($pluginId): array => [
                'company_id' => $companyId,
                'plugin_id'  => $pluginId,
                'created_at' => $now,
                'updated_at' => $now,
            ])
            ->values()
            ->all();

        if ($rows === []) {
            return 0;
        }

        DB::table('company_plugin')->insert($rows);

        PluginAccess::flush();

        return count($rows);
    }
}
