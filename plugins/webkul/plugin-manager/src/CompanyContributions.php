<?php

namespace Webkul\PluginManager;

use Closure;
use Filament\Forms\Components\CheckboxList;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Services\PluginEntitlementService;
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
    /**
     * Contributions accumulate, so this must only ever land once.
     *
     * packageBooted() is not guaranteed to run a single time per process: the
     * install flow and the test bootstrapper both re-boot plugin providers, and
     * the schema registry is static with no reset. Without this guard the
     * section is contributed again on each boot and the company form grows a
     * duplicate copy of it.
     */
    protected static bool $registered = false;

    public static function register(): void
    {
        if (static::$registered) {
            return;
        }

        static::$registered = true;

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
                    // Refused here, before anything saves: the edit page saves the
                    // company first and its relationships second, outside a
                    // transaction, so a refusal raised by the write itself left the
                    // company saved, the modules unchanged and the message on no
                    // field. Edit only - on create nothing was shown to untick.
                    ->rule(fn (?Company $record): Closure => function (string $attribute, mixed $value, Closure $fail) use ($record): void {
                        if (! $record || ! static::administersInstallation()) {
                            return;
                        }

                        try {
                            app(PluginEntitlementService::class)->assertCanSyncForCompany($record, (array) $value);
                        } catch (ValidationException $exception) {
                            $fail((string) collect($exception->errors())->flatten()->first());
                        }
                    })
                    ->saveRelationshipsUsing(function (?Company $record, array $state): void {
                        if (! $record) {
                            return;
                        }

                        // A new company was just provisioned with the default set,
                        // which the form never showed. Its unticked boxes mean "not
                        // chosen", so the choice is closed over its dependencies
                        // rather than read as revoking a dependency it needs.
                        if ($record->wasRecentlyCreated) {
                            $state = app(PluginEntitlementService::class)->withDependenciesOf($state);
                        }

                        static::syncGrants($record, $state);
                    }),
            ]);
    }

    /**
     * Who may decide a company's modules.
     *
     * Entitlement is an installation-level decision (D-SAAS-06): Super Admin
     * only, so neither a Company Admin nor Multi-Company Admin staff can grant
     * a module. Filament does not save a hidden component, and the write
     * re-checks anyway because the super-admin Gate::before skips policies.
     */
    protected static function administersInstallation(): bool
    {
        $user = auth()->user();

        if (! $user instanceof User) {
            return false;
        }

        return $user->isSuperAdmin();
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
     * answer the form does. The write itself - dependency closure, revocation
     * guard, transaction, audit - belongs to PluginEntitlementService.
     *
     * An unauthorized caller is refused silently rather than by exception: this
     * runs inside a Filament save, and an exception there would surface as a
     * broken form for someone who simply cannot see the section.
     *
     * @param  array<int, mixed>  $pluginIds
     */
    public static function syncGrants(Company $company, array $pluginIds): void
    {
        if (! static::administersInstallation()) {
            return;
        }

        app(PluginEntitlementService::class)->syncForCompany($company, $pluginIds);
    }
}
