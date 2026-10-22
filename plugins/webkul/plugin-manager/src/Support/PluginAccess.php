<?php

namespace Webkul\PluginManager\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Webkul\Support\Services\CompanyContext;

/**
 * Which plugins a given company may use.
 *
 * Installing a plugin is global, so this is the per-company layer on top of it:
 * `Package::isPluginInstalled()` answers "does this code exist here", and this
 * answers "may this company use it". The two are different questions and both
 * have to be true.
 *
 * Modelled on Logistics' LogisticsAccess, which solved the same problem for one
 * plugin and proved the shape: a scoped singleton so the flags are read once per
 * request or queued job, read without the company scope so a record's own
 * company can be checked even when it is not the active one, and guarded with
 * Schema::hasTable so it survives booting before the migration has run.
 *
 * Bound as a scoped singleton in PluginManagerServiceProvider.
 */
class PluginAccess
{
    /**
     * company_id => [plugin name => true]
     *
     * @var array<int, array<string, bool>>|null
     */
    protected ?array $grants = null;

    public static function enabledFor(string $plugin, ?int $companyId): bool
    {
        if (! $companyId) {
            return false;
        }

        return app(static::class)->flags()[$companyId][$plugin] ?? false;
    }

    /**
     * True when any of the user's active companies may use the plugin.
     *
     * This is the one navigation and policies want: a user switched into
     * several companies should see a plugin they can use in at least one of
     * them, and per-record checks then use enabledFor() with that record's own
     * company.
     */
    public static function enabledForCurrent(string $plugin): bool
    {
        $context = app(CompanyContext::class);

        $ids = $context->activeIds();

        if ($ids === [] && $context->currentId()) {
            $ids = [$context->currentId()];
        }

        foreach ($ids as $id) {
            if (static::enabledFor($plugin, (int) $id)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array<int, int>
     */
    public static function companyIdsFor(string $plugin): array
    {
        $ids = [];

        foreach (app(static::class)->flags() as $companyId => $plugins) {
            if ($plugins[$plugin] ?? false) {
                $ids[] = $companyId;
            }
        }

        return $ids;
    }

    public static function flush(): void
    {
        app(static::class)->grants = null;
    }

    /**
     * @return array<int, array<string, bool>>
     */
    protected function flags(): array
    {
        if ($this->grants !== null) {
            return $this->grants;
        }

        if (! Schema::hasTable('company_plugin') || ! Schema::hasTable('plugins')) {
            return $this->grants = [];
        }

        // Read with the query builder rather than models: this runs during
        // navigation and policy checks on every request, and a company-scoped
        // Eloquent read here would hide the very rows it needs to see.
        $rows = DB::table('company_plugin')
            ->join('plugins', 'plugins.id', '=', 'company_plugin.plugin_id')
            ->where('plugins.is_installed', true)
            ->get(['company_plugin.company_id', 'plugins.name']);

        $grants = [];

        foreach ($rows as $row) {
            $grants[(int) $row->company_id][$row->name] = true;
        }

        return $this->grants = $grants;
    }
}
