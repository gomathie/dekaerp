<?php

namespace Webkul\PluginManager\Console\Commands;

use Filament\Notifications\Notification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Services\PluginInstallRunner;
use Webkul\Security\Models\User;

/**
 * Installs a plugin outside the web request and tells the person who asked.
 *
 * Added 2026-11-12: the Plugins page ran the install inside the request. It
 * takes minutes (it regenerates every permission), the browser gave up first,
 * and the install dialog stayed open although the install finished. The page
 * now starts this command detached and returns at once.
 *
 *     php artisan plugins:install-in-background attendances --user=1
 */
class InstallInBackground extends Command
{
    protected $signature = 'plugins:install-in-background {plugin} {--user=}';

    protected $description = 'Install a plugin and notify the user who started it';

    public static function lockKey(string $plugin): string
    {
        return 'plugin-manager:installing:'.$plugin;
    }

    public function handle(PluginInstallRunner $runner): int
    {
        $name = (string) $this->argument('plugin');
        $plugin = Plugin::query()->where('name', $name)->first();
        $user = $this->option('user') ? User::query()->find((int) $this->option('user')) : null;

        if (! $plugin) {
            $this->error("Unknown plugin {$name}.");

            return self::FAILURE;
        }

        // One install per plugin at a time: a second click must not start a
        // parallel install of the same migrations and permissions.
        $lock = Cache::lock(static::lockKey($name), PluginInstallRunner::TIMEOUT_SECONDS + 60);

        if (! $lock->get()) {
            $this->warn("{$name} is already being installed.");

            return self::FAILURE;
        }

        try {
            $result = $runner->install($name);

            if ($result['ok']) {
                $plugin->update(['is_installed' => true, 'is_active' => true]);

                $this->notify($user, Notification::make()
                    ->success()
                    ->title(__('plugin-manager::filament/resources/plugin.notifications.installed.title'))
                    ->body(__('plugin-manager::filament/resources/plugin.notifications.installed.body', ['name' => $name])));

                return self::SUCCESS;
            }

            logger()->error('Plugin installation failed', ['plugin' => $name, 'exit' => $result['exitCode'], 'output' => $result['output']]);

            $this->notify($user, Notification::make()
                ->danger()
                ->title(__('plugin-manager::filament/resources/plugin.notifications.installed-failed.title'))
                ->body($result['exitCode'] === 124
                    ? 'Installation timed out after '.intdiv(PluginInstallRunner::TIMEOUT_SECONDS, 60).' minutes.'
                    : "Installation failed with exit code {$result['exitCode']}.".($result['output'] ? ' Last output: '.$result['output'] : '')));

            return self::FAILURE;
        } finally {
            $lock->release();
        }
    }

    protected function notify(?User $user, Notification $notification): void
    {
        $this->line((string) $notification->getTitle());

        if ($user) {
            $notification->sendToDatabase($user);
        }
    }
}
