<?php

namespace Webkul\PluginManager\Services;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Support\PluginAccess;
use Webkul\Security\Models\User;
use Webkul\Security\Services\SecurityAuditLogger;
use Webkul\Support\Models\Company;

/**
 * The one place a company's plugin entitlements are written.
 *
 * Two screens reach this: the "Companies" action on the Plugins page (plugin
 * first) and the modules section on the company form (company first). Before
 * SAAS-03 they wrote `company_plugin` separately - one through a relationship
 * `sync()` with no authorization re-check and no dependency awareness, the other
 * through its own delete/insert - so the two could disagree and neither left a
 * record of who granted what.
 *
 * Rules, all enforced here rather than in a Filament action:
 *
 *  - Only installed plugins can be granted; a grant for something installed
 *    elsewhere is left alone rather than silently dropped.
 *  - Granting a plugin grants the plugins it declares as dependencies, because a
 *    plugin that is on without its dependency is a screen that 500s.
 *  - Revoking a plugin that a still-granted plugin depends on is refused, with
 *    the dependents named. Revoking the dependent first is the way to do it; the
 *    alternative - silently revoking a set the administrator did not tick - is
 *    the kind of surprise this project avoids.
 *  - Entitlement changes are installation decisions (D-SAAS-06): Super Admin
 *    only, re-checked here because the super-admin `Gate::before` skips policies
 *    and a forged Livewire payload must not land either.
 *  - One transaction, one audit record per company whose set actually changed,
 *    and a PluginAccess flush so the change is visible in this request.
 */
class PluginEntitlementService
{
    /**
     * The configured default set for a new company, as plugin ids.
     *
     * `null` in config means "every installed plugin" - the pre-SAAS-03
     * behaviour - so this returns that full list in that case.
     *
     * @return array<int, int>
     */
    public function defaults(): array
    {
        if (! $this->schemaReady()) {
            return [];
        }

        $installed = Plugin::query()
            ->where('is_installed', true)
            ->get(['id', 'name']);

        $configured = config('plugin-manager.default_company_plugins');

        if ($configured === null) {
            return $installed->pluck('id')->map(fn ($id): int => (int) $id)->sort()->values()->all();
        }

        $wanted = collect((array) $configured)
            ->filter(fn ($name): bool => is_string($name) && $name !== '')
            ->map(fn (string $name): string => mb_strtolower(trim($name)))
            ->all();

        return $installed
            ->filter(fn (Plugin $plugin): bool => in_array(mb_strtolower(trim((string) $plugin->name)), $wanted, true))
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->sort()
            ->values()
            ->all();
    }

    /**
     * Give a new company the configured defaults.
     *
     * Deliberately additive: a company that already holds a grant keeps it, so
     * narrowing the default set cannot strip an existing tenant. That is also
     * why this is safe to call again (it is, on every install).
     *
     * @return int Number of grants added.
     */
    public function grantDefaults(Company $company): int
    {
        $companyId = (int) $company->getKey();

        if (! $companyId || ! $this->schemaReady()) {
            return 0;
        }

        $defaultIds = $this->defaults();

        if ($defaultIds === []) {
            return 0;
        }

        $existing = $this->grantedPluginIds($companyId);

        $missing = array_values(array_diff($defaultIds, $existing));

        if ($missing === []) {
            return 0;
        }

        $now = now();

        DB::table('company_plugin')->insertOrIgnore(array_map(fn (int $pluginId): array => [
            'company_id' => $companyId,
            'plugin_id'  => $pluginId,
            'created_at' => $now,
            'updated_at' => $now,
        ], $missing));

        PluginAccess::flush();

        return count($missing);
    }

    /**
     * Make a newly installed plugin available to every existing company.
     *
     * Installing is global and always has been: before per-company grants
     * existed, installing a plugin made it available everywhere, and the
     * 2026-10-22 migration preserved exactly that. Without this, a plugin
     * installed *after* the companies were created would be reachable by nobody
     * - and once API routes are enforced, that is a 403 rather than a missing
     * menu entry. Revoking stays the deliberate act.
     *
     * Called from InstallCommand right after the plugin is marked installed.
     *
     * @return int Number of grants added.
     */
    public function grantToEveryCompany(Plugin $plugin): int
    {
        if (! $this->schemaReady() || ! $plugin->is_installed) {
            return 0;
        }

        $companyIds = Company::query()
            ->withoutGlobalScopes()
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->all();

        if ($companyIds === []) {
            return 0;
        }

        $pluginId = (int) $plugin->getKey();
        $now = now();
        $rows = [];

        foreach ($companyIds as $companyId) {
            if (in_array($pluginId, $this->grantedPluginIds($companyId), true)) {
                continue;
            }

            $rows[] = [
                'company_id' => $companyId,
                'plugin_id'  => $pluginId,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if ($rows === []) {
            return 0;
        }

        DB::table('company_plugin')->insertOrIgnore($rows);

        PluginAccess::flush();

        return count($rows);
    }

    /**
     * Replace one company's grants with the given installed plugin ids.
     *
     * @param  array<int, mixed>  $pluginIds
     *
     * @throws AuthorizationException when the actor may not change entitlements
     * @throws ValidationException when a revocation would break a granted plugin
     */
    public function syncForCompany(Company $company, array $pluginIds, ?User $actor = null): void
    {
        $this->assertCanMutate($actor);

        DB::transaction(fn () => $this->applyForCompany($company, $pluginIds, $actor));
    }

    /**
     * The same operation from the plugin side: which companies may use it.
     *
     * @param  array<int, mixed>  $companyIds
     */
    public function syncForPlugin(Plugin $plugin, array $companyIds, ?User $actor = null): void
    {
        $this->assertCanMutate($actor);

        if (! $this->schemaReady()) {
            return;
        }

        $wantedCompanyIds = collect($companyIds)
            ->filter(fn ($id): bool => is_numeric($id))
            ->map(fn ($id): int => (int) $id)
            ->unique()
            ->all();

        $companyIds = Company::query()
            ->withoutGlobalScopes()
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->all();

        DB::transaction(function () use ($plugin, $companyIds, $wantedCompanyIds, $actor): void {
            foreach ($companyIds as $companyId) {
                $current = $this->grantedPluginIds($companyId);

                $wanted = in_array($companyId, $wantedCompanyIds, true)
                    ? $this->sorted([...$current, (int) $plugin->getKey()])
                    : $this->sorted(array_diff($current, [(int) $plugin->getKey()]));

                if ($wanted === $current) {
                    continue;
                }

                $company = Company::query()->withoutGlobalScopes()->find($companyId);

                if ($company) {
                    $this->applyForCompany($company, $wanted, $actor);
                }
            }
        });
    }

    /**
     * Plugin names this plugin declares it needs.
     *
     * Read from the package registration (`->hasDependencies([...])`), which is
     * the live source: the `plugin_dependencies` table exists but nothing writes
     * to it.
     *
     * @return array<int, string>
     */
    public function dependencyNamesFor(string $pluginName): array
    {
        $plugin = Plugin::query()->where('name', $pluginName)->first();

        if (! $plugin instanceof Plugin) {
            return [];
        }

        return array_values(array_filter(
            $plugin->getDependenciesFromConfig(),
            fn ($name): bool => is_string($name) && $name !== '',
        ));
    }

    public function assertCanMutate(?User $actor = null): void
    {
        $actor ??= auth()->user() instanceof User ? auth()->user() : null;

        if (! $actor instanceof User || ! $actor->is_active || ! $actor->isSuperAdmin()) {
            throw new AuthorizationException(__('Only a platform administrator may change plugin entitlements.'));
        }
    }

    /**
     * Write one company's set, with dependency closure, revocation guard, audit
     * and cache flush. Callers own the transaction.
     *
     * @param  array<int, mixed>  $pluginIds
     */
    protected function applyForCompany(Company $company, array $pluginIds, ?User $actor): void
    {
        if (! $this->schemaReady()) {
            return;
        }

        $installed = $this->installedPlugins();

        // Grants for plugins that are not installed *here* are deliberately left
        // alone: they belong to a plugin this installation does not carry, and
        // dropping them would lose the entitlement of a company that would get
        // it back the moment the plugin is installed. They are therefore outside
        // the set this method compares and audits.
        $before = array_values(array_intersect(
            $this->grantedPluginIds((int) $company->getKey()),
            $installed->keys()->map(fn ($id): int => (int) $id)->all(),
        ));

        $requestedIds = collect($pluginIds)
            ->filter(fn ($id): bool => is_numeric($id))
            ->map(fn ($id): int => (int) $id)
            ->all();

        // Only installed plugins participate; the rest are ignored rather than
        // written as rows nothing can honour.
        $requestedIds = $installed->keys()
            ->map(fn ($id): int => (int) $id)
            ->intersect($requestedIds)
            ->values()
            ->all();

        $wanted = $this->withDependencies($installed, $requestedIds);

        // The guard reads what was *requested*, not the dependency-closed set:
        // closure re-adds any dependency of a wanted plugin, so measured against
        // $wanted a revoked dependency vanished from the diff, the guard could
        // never fire, and an untick was silently ignored.
        $this->guardRevocations($installed, $before, $requestedIds, $wanted);

        if ($wanted === $before) {
            return;
        }

        DB::table('company_plugin')
            ->where('company_id', $company->getKey())
            ->whereIn('plugin_id', $installed->keys()->all())
            ->delete();

        if ($wanted !== []) {
            $now = now();

            DB::table('company_plugin')->insert(array_map(fn (int $pluginId): array => [
                'company_id' => $company->getKey(),
                'plugin_id'  => $pluginId,
                'created_at' => $now,
                'updated_at' => $now,
            ], $wanted));
        }

        PluginAccess::flush();

        app(SecurityAuditLogger::class)->record(
            'plugin.entitlement.updated',
            $company,
            $actor,
            (int) $company->getKey(),
            before: ['plugin_ids' => $this->sorted($before)],
            after: ['plugin_ids' => $this->sorted($wanted)],
        );
    }

    /**
     * Add the dependencies of the wanted plugins, recursively.
     *
     * Only installed dependencies are added: granting a dependency that is not
     * installed here would be a row nothing can honour.
     *
     * @param  Collection<int, Plugin>  $installed  keyed by id
     * @param  array<int, int>  $wanted
     * @return array<int, int>
     */
    protected function withDependencies(Collection $installed, array $wanted): array
    {
        $byName = $installed->keyBy(fn (Plugin $plugin): string => mb_strtolower(trim((string) $plugin->name)));

        $queue = $wanted;

        while ($queue !== []) {
            $pluginId = array_shift($queue);
            $plugin = $installed->get($pluginId);

            if (! $plugin instanceof Plugin) {
                continue;
            }

            foreach ($this->dependencyNamesFor((string) $plugin->name) as $dependencyName) {
                $dependency = $byName->get(mb_strtolower(trim($dependencyName)));

                if (! $dependency instanceof Plugin) {
                    continue;
                }

                $dependencyId = (int) $dependency->getKey();

                if (in_array($dependencyId, $wanted, true)) {
                    continue;
                }

                $wanted[] = $dependencyId;
                $queue[] = $dependencyId;
            }
        }

        return $this->sorted($wanted);
    }

    /**
     * Refuse a revocation that would leave a still-granted plugin without a
     * dependency it declares.
     *
     * @param  Collection<int, Plugin>  $installed  keyed by id
     * @param  array<int, int>  $before
     * @param  array<int, int>  $requested  what the caller asked for, before dependency closure
     * @param  array<int, int>  $wanted  $requested plus its dependencies
     *
     * @throws ValidationException
     */
    protected function guardRevocations(Collection $installed, array $before, array $requested, array $wanted): void
    {
        $revoked = array_values(array_diff($before, $requested));

        if ($revoked === []) {
            return;
        }

        $blocked = [];

        foreach ($revoked as $revokedId) {
            $revokedPlugin = $installed->get($revokedId);

            if (! $revokedPlugin instanceof Plugin) {
                continue;
            }

            foreach ($installed as $candidate) {
                if (! in_array((int) $candidate->getKey(), $wanted, true)) {
                    continue;
                }

                $dependencies = array_map(
                    fn (string $name): string => mb_strtolower(trim($name)),
                    $this->dependencyNamesFor((string) $candidate->name),
                );

                if (in_array(mb_strtolower(trim((string) $revokedPlugin->name)), $dependencies, true)) {
                    $blocked[] = ucfirst((string) $revokedPlugin->name).' (required by '.ucfirst((string) $candidate->name).')';
                }
            }
        }

        if ($blocked === []) {
            return;
        }

        throw ValidationException::withMessages([
            'company_ids' => __('These plugins cannot be revoked while a plugin that needs them is still granted: :plugins.', [
                'plugins' => implode(', ', array_unique($blocked)),
            ]),
        ]);
    }

    /**
     * @return Collection<int, Plugin> keyed by id
     */
    protected function installedPlugins(): Collection
    {
        return Plugin::query()
            ->where('is_installed', true)
            ->get(['id', 'name'])
            ->keyBy(fn (Plugin $plugin): int => (int) $plugin->getKey());
    }

    /**
     * @return array<int, int>
     */
    public function grantedPluginIds(int $companyId): array
    {
        if (! $this->schemaReady()) {
            return [];
        }

        return DB::table('company_plugin')
            ->where('company_id', $companyId)
            ->pluck('plugin_id')
            ->map(fn ($id): int => (int) $id)
            ->sort()
            ->values()
            ->all();
    }

    /**
     * @param  array<int, int>  $ids
     * @return array<int, int>
     */
    protected function sorted(array $ids): array
    {
        $ids = array_values(array_unique(array_map('intval', $ids)));

        sort($ids);

        return $ids;
    }

    protected function schemaReady(): bool
    {
        return Schema::hasTable('company_plugin') && Schema::hasTable('plugins');
    }
}
