<?php

namespace Webkul\PluginManager;

use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\LaravelPackageTools\Package as BasePackage;
use Symfony\Component\Process\PhpExecutableFinder;
use Throwable;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Console\Commands\UninstallCommand;
use Webkul\PluginManager\Models\Plugin;

class Package extends BasePackage
{
    public static $plugins = [];

    public ?Plugin $plugin = null;

    public bool $isCore = false;

    public bool $runsSettings = false;

    public array $settingFileNames = [];

    public array $dependencies = [];

    public bool $runsSeeders = false;

    public array $seederClasses = [];

    public ?string $icon = null;

    public function hasInstallCommand($callable): static
    {
        $installCommand = new InstallCommand($this);

        $callable($installCommand);

        $this->consoleCommands[] = $installCommand;

        return $this;
    }

    public function hasUninstallCommand($callable): static
    {
        $uninstallCommand = new UninstallCommand($this);

        $callable($uninstallCommand);

        $this->consoleCommands[] = $uninstallCommand;

        return $this;
    }

    public function isCore(bool $isCore = true): static
    {
        $this->isCore = $isCore;

        return $this;
    }

    public function runsSettings(bool $runsSettings = true): static
    {
        $this->runsSettings = $runsSettings;

        return $this;
    }

    public function hasSetting(string $settingFileName): static
    {
        $this->settingFileNames[] = $settingFileName;

        return $this;
    }

    public function hasSettings(...$settingFileNames): static
    {
        $this->settingFileNames = array_merge(
            $this->settingFileNames,
            collect($settingFileNames)->flatten()->toArray()
        );

        return $this;
    }

    public function runsSeeders(bool $runsSeeders = true): static
    {
        $this->runsSeeders = $runsSeeders;

        return $this;
    }

    public function hasSeeder(string $seederClass): static
    {
        $this->seederClasses[] = $seederClass;

        return $this;
    }

    public function hasSeeders(...$seederClasses): static
    {
        $this->seederClasses = array_merge(
            $this->seederClasses,
            collect($seederClasses)->flatten()->toArray()
        );

        return $this;
    }

    public function hasDependency(string $dependency): static
    {
        $this->dependencies[] = $dependency;

        return $this;
    }

    public function hasDependencies(...$dependencies): static
    {
        $this->dependencies = array_merge(
            $this->dependencies,
            collect($dependencies)->flatten()->toArray()
        );

        return $this;
    }

    public function icon(string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function delete(): void
    {
        Plugin::where('name', $this->name)->delete();

        unset(static::$plugins[$this->name]);

        $this->plugin = null;
    }

    public function updateOrCreate(): Plugin
    {
        $composerPath = $this->basePath('../composer.json');
        $composerData = json_decode(file_get_contents($composerPath), true);

        $this->plugin = Plugin::updateOrCreate([
            'name' => $this->name,
        ], [
            'author'         => $composerData['authors'][0]['name'] ?? null,
            'summary'        => $composerData['description'] ?? null,
            'description'    => $composerData['description'] ?? null,
            'latest_version' => $this->version ?? null,
            'license'        => $composerData['license'] ?? null,
            'sort'           => $this->sort ?? null,
            'is_active'      => true,
            'is_installed'   => true,
        ]);

        static::$plugins[$this->name] = $this->plugin;

        return $this->plugin;
    }

    public function getPlugin(): ?Plugin
    {
        if ($this->plugin) {
            return $this->plugin;
        }

        return $this->plugin = static::getPackagePlugin($this->name);
    }

    public function isInstalled(): bool
    {
        return static::isPluginInstalled($this->name);
    }

    public static function getPackagePlugin(string $name): ?Plugin
    {
        if (count(static::$plugins) == 0) {
            if (Schema::hasTable('plugins') === false) {
                return null;
            }

            static::$plugins = Plugin::all()->keyBy('name');
        }

        if (isset(static::$plugins[$name])) {
            return static::$plugins[$name];
        }

        return static::$plugins[$name] ??= Plugin::where('name', $name)->first();
    }

    public static function refreshPluginCaches(): void
    {
        try {
            Artisan::call('optimize:clear');

            if (app()->isProduction()) {
                // Rebuilt in-process, on purpose. This used to fire a detached
                // `php artisan optimize &`, which returned immediately: the
                // redirect straight after an install could then reach the app
                // while that background process was still writing
                // bootstrap/cache/config.php and routes-v7.php. Those are
                // written with file_put_contents, so a concurrent request could
                // `require` a half-written file and fatal - an error that fixed
                // itself once the rebuild finished. The detached command also
                // sent its own failures to /dev/null. Rebuilding here costs the
                // install action a few seconds and leaves the caches whole
                // before anyone is redirected.
                Artisan::call('optimize');
            }
        } catch (Throwable $e) {
            report($e);
        }
    }

    /**
     * Backported from upstream aureuserp (631dbcdfb, "Fix Plugins installation
     * in windows"): `which php` does not exist on Windows, and shell_exec is
     * disabled on some hosts, so installing a plugin from the Plugins page
     * could not find a binary. PhpExecutableFinder also covers the php-fpm case
     * the old hand-rolled candidate list existed for: it only returns PHP_BINARY
     * when PHP_SAPI is cli/cli-server/phpdbg, so under fpm it falls through to
     * PHP_BINDIR instead of handing back the fpm binary.
     *
     * find(false) because the result is passed through escapeshellarg(), which
     * would quote any appended SAPI arguments into a single unusable token.
     */
    public static function phpBinaryPath(): string
    {
        $php = (new PhpExecutableFinder)->find(false);

        if ($php && is_file($php)) {
            return $php;
        }

        return 'php';
    }

    public static function syncPostgresSequences(): void
    {
        db_dialect()->syncSequences();
    }

    public static function isPluginInstalled(string $name): bool
    {
        static $isLoaded = false;

        try {
            if (! $isLoaded) {
                DB::connection()->getPdo();

                if (Schema::hasTable('plugins') === false) {
                    $isLoaded = true;

                    return false;
                }

                static::$plugins = Plugin::all()->keyBy('name');
                $isLoaded = true;
            }

            if (isset(static::$plugins[$name]) && static::$plugins[$name]->is_installed) {
                return true;
            }

            return false;
        } catch (Exception) {
            return false;
        }
    }
}
