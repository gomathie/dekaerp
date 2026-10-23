<?php

namespace Webkul\PluginManager;

use Filament\Forms\Components\CheckboxList;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Facades\DB;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Support\PluginAccess;
use Webkul\Security\Models\User;
use Webkul\Support\Filament\Resources\CompanyResource\Support\CompanySchemaRegistry;
use Webkul\Support\Models\Company;

/**
 * Per-company module grants, shown while editing a company.
 *
 * The same `company_plugin` rows the "Companies" action on the Plugins page
 * manages, reached from the other side: open a company and decide which modules
 * it may use (user, 2026-10-23, comparing it to modules on a PILOT account).
 * One pivot, two views - deliberately not a second mechanism.
 *
 * Contributed through CompanySchemaRegistry rather than edited into
 * support's CompanyResource, because `support` is the base plugin and must not
 * reference Plugin Manager, which depends on it.
 *
 * Registered from PluginManagerServiceProvider.
 */
class CompanyContributions
{
    public static function register(): void
    {
        CompanySchemaRegistry::form('main.after', fn () => [static::modulesSection()]);
    }

    protected static function modulesSection(): Section
    {
        return Section::make(__('plugin-manager::filament/contributions/company.modules.title'))
            ->description(__('plugin-manager::filament/contributions/company.modules.description'))
            ->icon('heroicon-o-puzzle-piece')
            ->visible(fn (): bool => static::administersInstallation())
            ->schema([
                CheckboxList::make('company_modules')
                    ->hiddenLabel()
                    ->columns(2)
                    ->bulkToggleable()
                    ->options(fn (): array => Plugin::query()
                        ->where('is_installed', true)
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->all())
                    // Not a column on companies, so it must never be dehydrated
                    // into the model's attributes.
                    ->dehydrated(false)
                    ->loadStateFromRelationshipsUsing(function (CheckboxList $component, ?Company $record): void {
                        $component->state($record ? static::grantedPluginIds($record) : []);
                    })
                    ->saveRelationshipsUsing(function (?Company $record, array $state): void {
                        if ($record) {
                            static::syncGrants($record, $state);
                        }
                    }),
            ]);
    }

    /**
     * Who may decide a company's modules.
     *
     * Entitlement is an installation-level decision, so a Company Admin must
     * not be able to grant their own company a module. Filament does not save a
     * hidden component, and the write re-checks anyway because the super-admin
     * Gate::before skips policies entirely.
     */
    protected static function administersInstallation(): bool
    {
        $user = auth()->user();

        if (! $user instanceof User) {
            return false;
        }

        return $user->isSuperAdmin() || $user->isMultiCompanyAdmin();
    }

    /**
     * @return array<int, int>
     */
    protected static function grantedPluginIds(Company $company): array
    {
        return DB::table('company_plugin')
            ->where('company_id', $company->getKey())
            ->pluck('plugin_id')
            ->map(fn ($id): int => (int) $id)
            ->all();
    }

    /**
     * Replace a company's grants with the given installed plugin ids.
     *
     * Public because it is the operation, not an implementation detail of the
     * form: it carries its own authorization check, so any caller gets the same
     * answer the form does.
     *
     * @param  array<int, mixed>  $pluginIds
     */
    public static function syncGrants(Company $company, array $pluginIds): void
    {
        // Entitlement is an installation-level decision. Filament will not save
        // a hidden component, but this re-checks rather than trusting that,
        // because the super-admin Gate::before skips policies entirely and a
        // forged form payload must not land either.
        if (! static::administersInstallation()) {
            return;
        }

        $state = $pluginIds;

        $installed = Plugin::query()->where('is_installed', true)->pluck('id');

        // Only installed plugins can be granted, and only installed plugins are
        // revoked - a grant for something not installed here is left untouched
        // rather than silently dropped.
        $wanted = $installed
            ->intersect(array_map('intval', $state))
            ->values();

        DB::table('company_plugin')
            ->where('company_id', $company->getKey())
            ->whereIn('plugin_id', $installed)
            ->delete();

        if ($wanted->isNotEmpty()) {
            DB::table('company_plugin')->insert(
                $wanted->map(fn (int $pluginId): array => [
                    'company_id' => $company->getKey(),
                    'plugin_id'  => $pluginId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])->all()
            );
        }

        PluginAccess::flush();
    }
}
