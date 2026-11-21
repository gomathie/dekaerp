<?php

namespace Webkul\PluginManager\Filament\Resources\PluginResource\Tables;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Support\Enums\IconSize;
use Filament\Support\Enums\TextSize;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema as DBSchema;
use RuntimeException;
use Throwable;
use Webkul\PluginManager\Console\Commands\InstallInBackground;
use Webkul\PluginManager\Filament\Resources\PluginResource;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\Services\PluginEntitlementService;
use Webkul\PluginManager\Services\PluginInstallRunner;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class PluginsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Split::make([
                    IconColumn::make('package_icon')
                        ->label('')
                        ->state(true)
                        ->icon('heroicon-o-puzzle-piece')
                        ->size(IconSize::TwoExtraLarge)
                        ->color('primary')
                        ->visible(fn ($record) => ! $record?->package?->icon)
                        ->grow(false),

                    ImageColumn::make('package_image')
                        ->label('')
                        ->getStateUsing(fn ($record) => $record?->package?->icon
                            ? asset("svg/{$record->package->icon}.svg")
                            : null)
                        ->imageSize(56)
                        ->visible(fn ($record) => $record?->package?->icon)
                        ->grow(false),

                    Stack::make([
                        Split::make([
                            TextColumn::make('name')
                                ->weight('semibold')
                                ->searchable()
                                ->size(TextSize::Large)
                                ->formatStateUsing(fn (string $state) => PluginResource::localize('names', $state, ucfirst($state)))
                                ->grow(false),

                            TextColumn::make('latest_version')
                                ->label(__('plugin-manager::filament/resources/plugin.table.version'))
                                ->state(fn (Plugin $record): string => $record->currentVersion())
                                ->badge()
                                ->color('info'),
                        ]),

                        TextColumn::make('summary')
                            ->color('gray')
                            ->limit(80)
                            ->wrap()
                            ->formatStateUsing(fn ($state, $record) => PluginResource::localize('summaries', $record->name, $state)),

                        Split::make([
                            TextColumn::make('is_installed')
                                ->badge()
                                ->inline()
                                ->grow(false)
                                ->formatStateUsing(fn ($record) => $record->is_installed
                                    ? __('plugin-manager::filament/resources/plugin.status.installed')
                                    : __('plugin-manager::filament/resources/plugin.status.not_installed'))
                                ->color(fn ($record) => $record->is_installed ? 'success' : 'gray'),

                            TextColumn::make('dependencies_count')
                                ->label(__('plugin-manager::filament/resources/plugin.table.dependencies'))
                                ->state(fn ($record) => count($record->getDependenciesFromConfig()))
                                ->badge()
                                ->color('warning')
                                ->suffix(__('plugin-manager::filament/resources/plugin.table.dependencies_suffix'))
                                ->default(0),
                        ]),
                    ])->space(1),
                ]),
            ])
            ->contentGrid([
                'sm'  => 1,
                'md'  => 2,
                'lg'  => 2,
                'xl'  => 3,
                '2xl' => 4,
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()->icon('heroicon-o-eye'),

                    // Installing a plugin is global; this is which companies may
                    // use it. Without this the company_plugin grants could only
                    // be changed with SQL, which makes the feature unusable.
                    Action::make('companies')
                        ->label(__('plugin-manager::filament/resources/plugin.actions.companies.title'))
                        ->icon('heroicon-o-building-office-2')
                        ->color('info')
                        ->visible(fn ($record): bool => (bool) $record->is_installed
                            && auth()->user() instanceof User
                            && auth()->user()->isSuperAdmin())
                        ->modalHeading(fn ($record): string => __('plugin-manager::filament/resources/plugin.actions.companies.heading', [
                            'name' => ucfirst($record->name),
                        ]))
                        ->modalDescription(__('plugin-manager::filament/resources/plugin.actions.companies.description'))
                        ->schema([
                            CheckboxList::make('company_ids')
                                ->label(__('plugin-manager::filament/resources/plugin.actions.companies.field'))
                                ->options(fn (): array => Company::query()
                                    ->withoutGlobalScopes()
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->all())
                                ->bulkToggleable()
                                ->columns(2),
                        ])
                        ->fillForm(fn ($record): array => [
                            'company_ids' => $record->companies()->pluck('companies.id')->all(),
                        ])
                        ->action(function ($record, array $data): void {
                            // One writer for company_plugin: this action and the
                            // modules section on the company form both go through
                            // PluginEntitlementService, so dependency closure,
                            // authorization, the audit record and the cache flush
                            // cannot differ between the two screens.
                            app(PluginEntitlementService::class)->syncForPlugin(
                                $record,
                                $data['company_ids'] ?? [],
                            );

                            Notification::make()
                                ->success()
                                ->title(__('plugin-manager::filament/resources/plugin.actions.companies.notification.title'))
                                ->body(__('plugin-manager::filament/resources/plugin.actions.companies.notification.body', [
                                    'count' => count($data['company_ids'] ?? []),
                                    'name'  => ucfirst($record->name),
                                ]))
                                ->send();
                        }),

                    Action::make('install')
                        ->label(__('plugin-manager::filament/resources/plugin.actions.install.title'))
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('success')
                        ->visible(fn ($record) => ! $record->is_installed)
                        ->requiresConfirmation()
                        ->modalHeading(fn ($record) => __('plugin-manager::filament/resources/plugin.actions.install.heading', ['name' => $record->name]))
                        ->modalDescription(fn ($record) => __('plugin-manager::filament/resources/plugin.actions.install.description', ['name' => $record->name]))
                        ->modalSubmitActionLabel(__('plugin-manager::filament/resources/plugin.actions.install.submit'))
                        ->action(function ($record) {
                            // No transaction around the install.
                            //
                            // `{plugin}:install` runs as a separate PHP process on its
                            // own database connection, so its migrations and permission
                            // rows commit independently: a rollback here could never
                            // undo them, it only ever reverted the local `plugins` row.
                            // What the transaction did do was hold this request's
                            // connection open, idle in transaction, for as long as the
                            // child ran (up to the 300s timeout). Against a pooled
                            // managed database that is long enough to hit an
                            // idle-in-transaction timeout, or to block the child's own
                            // DDL on locks this connection still held - which surfaced
                            // as an install that failed once and then succeeded on a
                            // later attempt.
                            //
                            // In the background since 2026-11-12: the install takes
                            // minutes (it regenerates every permission), the browser
                            // gave up first, and the dialog stayed open although the
                            // install finished. The page now starts
                            // plugins:install-in-background detached and returns at
                            // once; that command notifies the user when it is done.
                            // Windows (local development) keeps the in-request path.
                            if (PHP_OS_FAMILY !== 'Windows') {
                                if (Cache::lock(InstallInBackground::lockKey($record->name), 1)->get() === false) {
                                    Notification::make()
                                        ->warning()
                                        ->title(__('plugin-manager::filament/resources/plugin.notifications.installing-already.title', ['name' => $record->name]))
                                        ->send();

                                    return;
                                }

                                // The probe above took the lock for one second only;
                                // release it so the background command can take it.
                                Cache::lock(InstallInBackground::lockKey($record->name))->forceRelease();

                                app(PluginInstallRunner::class)->startInBackground($record->name, auth()->id());

                                Notification::make()
                                    ->info()
                                    ->title(__('plugin-manager::filament/resources/plugin.notifications.installing.title', ['name' => $record->name]))
                                    ->body(__('plugin-manager::filament/resources/plugin.notifications.installing.body'))
                                    ->send();

                                return;
                            }

                            try {
                                $result = app(PluginInstallRunner::class)->install($record->name);

                                if ($result['exitCode'] === 124) {
                                    throw new RuntimeException('Installation timed out after '.intdiv(PluginInstallRunner::TIMEOUT_SECONDS, 60).' minutes.');
                                }

                                if (! $result['ok']) {
                                    throw new RuntimeException(
                                        "Installation failed with exit code {$result['exitCode']}.".
                                            ($result['output'] ? " Last output: {$result['output']}" : '')
                                    );
                                }

                                $record->update([
                                    'is_installed' => true,
                                    'is_active'    => true,
                                ]);

                                Notification::make()
                                    ->title(__('plugin-manager::filament/resources/plugin.notifications.installed.title'))
                                    ->body(__('plugin-manager::filament/resources/plugin.notifications.installed.body', ['name' => $record->name]))
                                    ->success()
                                    ->send();
                            } catch (Throwable $e) {
                                logger()->error('Plugin installation failed', [
                                    'plugin' => $record->name,
                                    'error'  => $e->getMessage(),
                                    'trace'  => $e->getTraceAsString(),
                                ]);

                                Notification::make()
                                    ->title(__('plugin-manager::filament/resources/plugin.notifications.installed-failed.title'))
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->persistent()
                                    ->send();
                            }
                        })
                        ->after(fn () => redirect(PluginResource::getUrl('index'))),

                    Action::make('uninstall')
                        ->label(__('plugin-manager::filament/resources/plugin.actions.uninstall.title'))
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->modalWidth(Width::ExtraLarge)
                        ->visible(fn ($record) => $record->is_installed)
                        ->modalHeading(__('plugin-manager::filament/resources/plugin.actions.uninstall.heading'))
                        ->modalSubmitActionLabel(__('plugin-manager::filament/resources/plugin.actions.uninstall.submit'))
                        ->modalSubmitAction(
                            fn ($action, $record) => $action->hidden(
                                collect($record->getDependentsFromConfig())
                                    ->contains(fn ($dependent) => Package::isPluginInstalled($dependent))
                            )
                        )
                        ->modalContent(function ($record) {
                            $dependents = $record->getDependentsFromConfig();

                            $installedDependents = collect($dependents)
                                ->filter(fn ($dependent) => Package::isPluginInstalled($dependent))
                                ->values();

                            $packages = collect([$record->name => $record->package])
                                ->merge(
                                    $dependents
                                        ? collect($dependents)->mapWithKeys(fn ($dep) => [$dep => Plugin::where('name', $dep)->first()?->package])
                                        : []
                                );

                            $tables = $packages
                                ->flatMap(fn ($package) => collect($package?->migrationFileNames ?? []))
                                ->map(function ($migrationFile) {
                                    if (preg_match('/create_(.*?)_table/', $migrationFile, $matches)) {
                                        $table = $matches[1];

                                        return DBSchema::hasTable($table)
                                            ? ['table' => $table, 'count' => DB::table($table)->count()]
                                            : null;
                                    }

                                    return null;
                                })
                                ->filter()
                                ->filter(fn ($item) => $item['count'] > 0)
                                ->unique('table')
                                ->values();

                            return view('plugin-manager::uninstall-modal', compact('record', 'dependents', 'installedDependents', 'tables'));
                        })
                        ->action(fn ($record) => PluginResource::uninstallPlugin($record))
                        // Guarded: this redirects to the page the user is
                        // already on, and it runs immediately after the caches
                        // were cleared. When the rebuild fails the route is
                        // momentarily unresolvable and getUrl() throws
                        // RouteNotFoundException, turning a completed uninstall
                        // into an unhandled error (2026-10-21). Skipping the
                        // redirect loses nothing: Livewire re-renders the table
                        // either way.
                        ->after(function () {
                            if (! Route::has(PluginResource::getRouteBaseName().'.index')) {
                                return null;
                            }

                            return redirect(PluginResource::getUrl('index'));
                        }),
                ]),
            ], position: RecordActionsPosition::BeforeColumns)
            ->recordActionsAlignment('end')
            ->defaultSort('sort', 'asc')
            ->paginated([16, 24, 32]);
    }
}
