<?php

namespace Webkul\PluginManager\Console\Commands;

use Closure;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Throwable;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;

class InstallCommand extends Command
{
    protected Package $package;

    public ?Closure $startWith = null;

    protected array $publishes = [];

    protected bool $askToInstallDependencies = false;

    protected bool $askToRunMigrations = false;

    protected bool $askToRunSeeders = false;

    protected bool $installDependencies = false;

    protected bool $runsMigrations = false;

    protected bool $runsSeeders = false;

    protected bool $copyServiceProviderInApp = false;

    protected ?string $starRepo = null;

    public ?Closure $endWith = null;

    public $hidden = true;

    public function __construct(Package $package)
    {
        $this->signature = $package->shortName().':install';

        $this->description = 'Install '.$package->name;

        $this->package = $package;

        parent::__construct();
    }

    public function handle()
    {
        if ($this->startWith) {
            ($this->startWith)($this);
        }

        if ($this->askToInstallDependencies) {
            $choice = $this->choice(
                "This package <comment>{$this->package->shortName()}</comment> has dependencies. What would you like to do?",
                ['Install', 'Skip', 'Show Dependencies'],
                0
            );

            if ($choice === 'Install') {
                $this->info("🚀 Installing <comment>{$this->package->shortName()}</comment> dependencies...");

                $this->newLine();

                foreach ($this->package->dependencies as $dependency) {
                    $this->comment('Installing <info>'.$dependency.'</info>...');

                    $this->newLine();

                    $this->call($dependency.':install');
                }

                $this->newLine();
            } elseif ($choice === 'Show Dependencies') {
                $this->info('This package requires the following dependencies:');

                foreach ($this->package->dependencies as $dependency) {
                    $this->line('- <info>'.$dependency.'</info>');
                }

                $this->newLine();

                return $this->handle();
            } else {
                $this->error('Please install the dependencies first.');

                return;
            }
        } elseif ($this->installDependencies) {
            $this->info("🚀 Installing <comment>{$this->package->shortName()}</comment> dependencies...");

            $this->newLine();

            foreach ($this->package->dependencies as $dependency) {
                $this->comment('Installing <info>'.$dependency.'</info>...');

                $this->newLine();

                $this->call($dependency.':install');
            }

            $this->newLine();
        }

        foreach ($this->publishes as $tag) {
            $name = str_replace('-', ' ', $tag);
            $this->comment("Publishing {$name}...");

            $this->callSilently('vendor:publish', [
                '--tag' => "{$this->package->shortName()}-{$tag}",
            ]);
        }

        if ($this->askToRunMigrations) {
            if ($this->confirm('Would you like to run the migrations now?')) {
                $this->runMigrations();
            }
        } elseif ($this->runsMigrations) {
            $this->runMigrations();
        }

        if ($this->askToRunSeeders) {
            if ($this->confirm('Would you like to seed the data now?')) {
                $this->runSeeders();
            }
        } elseif ($this->runsSeeders) {
            $this->runSeeders();
        }

        if ($this->copyServiceProviderInApp) {
            $this->comment('Publishing service provider...');

            $this->newLine();

            $this->copyServiceProviderInApp();
        }

        if ($this->starRepo) {
            if ($this->confirm('Would you like to star our repo on GitHub?')) {
                $repoUrl = "https://github.com/{$this->starRepo}";

                if (PHP_OS_FAMILY == 'Darwin') {
                    exec("open {$repoUrl}");
                }
                if (PHP_OS_FAMILY == 'Windows') {
                    exec("start {$repoUrl}");
                }
                if (PHP_OS_FAMILY == 'Linux') {
                    exec("xdg-open {$repoUrl}");
                }
            }
        }

        $package = $this->package->updateOrCreate();

        foreach ($this->package->dependencies as $dependencyName) {
            $dependency = Plugin::where('name', $dependencyName)->first();

            $package->dependencies()->syncWithoutDetaching($dependency);
        }

        if ($this->endWith) {
            ($this->endWith)($this);
        }

        $this->regenerateAdminPanelPermissions();

        $this->info('⚙️ Refreshing application caches so the plugin navigation is reflected...');

        Package::refreshPluginCaches();

        $this->info("🎉 Package <comment>{$this->package->shortName()}</comment> has been installed!");
    }

    public function publish(string ...$tag): self
    {
        $this->publishes = array_merge($this->publishes, $tag);

        return $this;
    }

    public function publishConfigFile(): self
    {
        return $this->publish('config');
    }

    public function publishAssets(): self
    {
        return $this->publish('assets');
    }

    public function publishInertiaComponents(): self
    {
        return $this->publish('inertia-components');
    }

    public function publishMigrations(): self
    {
        return $this->publish('migrations');
    }

    public function askToInstallDependencies(): self
    {
        $this->askToInstallDependencies = true;

        return $this;
    }

    public function askToRunMigrations(): self
    {
        $this->askToRunMigrations = true;

        return $this;
    }

    public function askToRunSeeders(): self
    {
        $this->askToRunSeeders = true;

        return $this;
    }

    public function installDependencies(): self
    {
        $this->installDependencies = true;

        return $this;
    }

    public function runsMigrations(): self
    {
        $this->runsMigrations = true;

        return $this;
    }

    public function runsSeeders(): self
    {
        $this->runsSeeders = true;

        return $this;
    }

    public function runMigrations(): self
    {
        $migrationsToRun = collect([]);

        foreach ($this->package->migrationFileNames as $migration) {
            if ($this->hasMigrationAlreadyRun($migration)) {
                continue;
            }

            $fullPath = $this->package->basePath("../database/migrations/{$migration}.php");

            $path = Str::after($fullPath, base_path().DIRECTORY_SEPARATOR);

            $migrationsToRun[] = $path;
        }

        if (! $migrationsToRun->isEmpty()) {
            $this->info("⚙️ Running <comment>{$this->package->shortName()}</comment> database migrations...");

            // --force is required, not optional. migrate is a Confirmable
            // command: in production it asks before running, and with no TTY
            // that prompt resolves to "no". The call then returns quietly
            // having done nothing, the success line below still prints, and
            // the plugin is marked installed with none of its tables.
            $exitCode = $this->call('migrate', [
                '--path'  => $migrationsToRun->toArray(),
                '--force' => true,
            ]);

            if ($exitCode !== 0) {
                $this->error("❌ Migrations for {$this->package->shortName()} failed (exit {$exitCode}). The plugin will not work correctly.");

                return $this;
            }

            $this->info("✅ Migrations <comment>{$this->package->shortName()}</comment> completed successfully.");

            $this->newLine();
        }

        $settingsToRun = collect([]);

        foreach ($this->package->settingFileNames as $setting) {
            if ($this->hasMigrationAlreadyRun($setting)) {
                continue;
            }

            $fullPath = $this->package->basePath("../database/settings/{$setting}.php");

            $path = Str::after($fullPath, base_path().DIRECTORY_SEPARATOR);

            $settingsToRun[] = $path;
        }

        if (! $settingsToRun->isEmpty()) {
            $this->info("⚙️ Running <comment>{$this->package->shortName()}</comment> settings database migrations...");

            // Same --force requirement as above. Skipping these is worse than
            // skipping tables: a missing settings row is not a missing feature,
            // it is a fatal boot error. Resources read settings while the panel
            // is being constructed, so the whole admin panel goes down - not
            // just this plugin's screens.
            $exitCode = $this->call('migrate', [
                '--path'  => $settingsToRun->toArray(),
                '--force' => true,
            ]);

            if ($exitCode !== 0) {
                $this->error("❌ Settings migrations for {$this->package->shortName()} failed (exit {$exitCode}).");
                $this->warn('Resources read these settings during panel construction, so the');
                $this->warn('admin panel will fail to boot until they exist. Resolve before continuing.');

                return $this;
            }

            $this->info("✅ Settings migrations <comment>{$this->package->shortName()}</comment> completed successfully.");

            $this->newLine();
        }

        return $this;
    }

    public function runSeeders(): self
    {
        if ($this->package->isInstalled()) {
            $choice = $this->choice(
                "This package <comment>{$this->package->shortName()}</comment> is already installed. What would you like to do?",
                ['Reseed', 'Skip', 'Show Seeders'],
                1
            );

            if ($choice === 'Skip') {
                return $this;
            }

            if ($choice === 'Show Seeders') {
                $this->newLine();
                $this->info('This package includes the following seeders:');

                foreach ($this->package->seederClasses as $seeder) {
                    $this->line('- <info>'.$seeder.'</info>');
                }
                $this->newLine();

                return $this->runSeeders();
            }
        }

        $this->info("⚙️ Running <comment>{$this->package->shortName()}</comment> database seeders...");

        foreach ($this->package->seederClasses as $seeder) {
            // db:seed is Confirmable too, so without --force it silently does
            // nothing in production and the plugin installs with empty
            // reference data.
            $exitCode = $this->call('db:seed', [
                '--class' => $seeder,
                '--force' => true,
            ]);

            if ($exitCode !== 0) {
                $this->error("❌ Seeder {$seeder} failed (exit {$exitCode}). Reference data for {$this->package->shortName()} is incomplete.");
            }
        }

        Package::syncPostgresSequences();

        $this->info("✅ Seeders <comment>{$this->package->shortName()}</comment> completed successfully.");

        $this->newLine();

        return $this;
    }

    public function copyAndRegisterServiceProviderInApp(): self
    {
        $this->copyServiceProviderInApp = true;

        return $this;
    }

    public function askToStarRepoOnGitHub($vendorSlashRepoName): self
    {
        $this->starRepo = $vendorSlashRepoName;

        return $this;
    }

    public function startWith($callable): self
    {
        $this->startWith = $callable;

        return $this;
    }

    public function endWith($callable): self
    {
        $this->endWith = $callable;

        return $this;
    }

    public function hasMigrationAlreadyRun($migrationName): bool
    {
        return DB::table('migrations')
            ->where('migration', $migrationName)
            ->exists();
    }

    protected function copyServiceProviderInApp(): self
    {
        $providerName = $this->package->publishableProviderName;

        if (! $providerName) {
            return $this;
        }

        $this->callSilent('vendor:publish', ['--tag' => $this->package->shortName().'-provider']);

        $namespace = Str::replaceLast('\\', '', $this->laravel->getNamespace());

        if (intval(app()->version()) < 11 || ! file_exists(base_path('bootstrap/providers.php'))) {
            $appConfig = file_get_contents(config_path('app.php'));
        } else {
            $appConfig = file_get_contents(base_path('bootstrap/providers.php'));
        }

        $class = '\\Providers\\'.Str::replace('/', '\\', $providerName).'::class';

        if (Str::contains($appConfig, $namespace.$class)) {
            return $this;
        }

        if (intval(app()->version()) < 11 || ! file_exists(base_path('bootstrap/providers.php'))) {
            file_put_contents(config_path('app.php'), str_replace(
                "{$namespace}\\Providers\\BroadcastServiceProvider::class,",
                "{$namespace}\\Providers\\BroadcastServiceProvider::class,".PHP_EOL."        {$namespace}{$class},",
                $appConfig
            ));
        } else {
            file_put_contents(base_path('bootstrap/providers.php'), str_replace(
                "{$namespace}\\Providers\\AppServiceProvider::class,",
                "{$namespace}\\Providers\\AppServiceProvider::class,".PHP_EOL."        {$namespace}{$class},",
                $appConfig
            ));
        }

        file_put_contents(app_path('Providers/'.$providerName.'.php'), str_replace(
            "namespace App\Providers;",
            "namespace {$namespace}\Providers;",
            file_get_contents(app_path('Providers/'.$providerName.'.php'))
        ));

        return $this;
    }

    protected function regenerateAdminPanelPermissions(): void
    {
        $this->info('⚙️ Refreshing access controls for the admin panel...');

        try {
            $phpPath = $this->getPhpExecutablePath();

            $php = escapeshellarg($phpPath);

            $artisan = escapeshellarg(base_path('artisan'));

            // 60s was the original budget. It is enough against a local
            // database and nowhere near enough against a managed one, where
            // this writes hundreds of permission rows at network latency. When
            // it expired the plugin still reported success while the admin
            // role kept none of the new permissions, so every resource the
            // plugin added stayed invisible in the panel.
            $timeout = max(60, (int) config('deka.plugins.permission_timeout', 900));

            $cmd = $this->buildTimeoutCommand($timeout, "$php $artisan shield:generate --all --option=permissions --panel=admin 2>&1");

            exec($cmd, $output, $exitCode);

            if ($exitCode === 124) {
                throw new RuntimeException("Permission generation timed out after {$timeout} seconds.");
            }

            if ($exitCode !== 0) {
                $errorOutput = implode(PHP_EOL, array_slice($output, -5));

                throw new RuntimeException("Failed to generate admin panel permissions. Error: {$errorOutput}");
            }

            $role = Role::first();

            if (! $role) {
                $this->warn('⚠️  No role found to sync permissions.');

                return;
            }

            $permissions = Permission::query()->pluck('id')->all();

            $role->permissions()->sync($permissions);

            $this->info('✅ Admin panel permissions refreshed successfully.');
        } catch (Throwable $e) {
            // A warning alone is not enough here. The plugin is installed and
            // its tables exist, but without permissions none of its resources
            // appear in the panel - which reads as "the plugin did nothing"
            // rather than "access control needs rebuilding". Say plainly what
            // broke and what fixes it.
            $this->error("❌ Permission refresh failed: {$e->getMessage()}");
            $this->newLine();
            $this->warn('The plugin is installed, but its screens will NOT appear in the');
            $this->warn('panel until permissions are rebuilt. Run these, in order:');
            $this->newLine();
            $this->line('  php artisan shield:generate --all --option=permissions --panel=admin');
            $this->line('  php artisan permission:cache-reset');
            $this->newLine();
            $this->warn('Then reassign permissions to the admin role. If this timed out,');
            $this->warn('raise DEKA_PLUGIN_PERMISSION_TIMEOUT - see config/deka.php.');
        }
    }

    protected function getPhpExecutablePath(): string
    {
        return Package::phpBinaryPath();
    }

    protected function buildTimeoutCommand(int $seconds, string $command): string
    {
        if (PHP_OS_FAMILY === 'Windows') {
            return $command;
        }

        if (PHP_OS_FAMILY === 'Darwin') {
            $gtimeout = trim((string) shell_exec('which gtimeout 2>/dev/null'));

            if ($gtimeout !== '') {
                return "gtimeout {$seconds} {$command}";
            }

            return $command;
        }

        return "timeout {$seconds} {$command}";
    }
}
