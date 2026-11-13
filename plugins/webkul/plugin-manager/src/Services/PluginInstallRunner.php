<?php

namespace Webkul\PluginManager\Services;

use Webkul\PluginManager\Package;

/**
 * Runs `{plugin}:install` as its own PHP process and reports the outcome.
 *
 * A separate process keeps the install's migrations and permission rows on
 * their own database connection, as the Plugins page always did (see
 * PluginResource's install action). Bound in the container so tests can
 * replace it: a real install regenerates every permission and takes minutes.
 */
class PluginInstallRunner
{
    public const TIMEOUT_SECONDS = 600;

    /**
     * @return array{ok: bool, exitCode: int, output: string}
     */
    public function install(string $plugin): array
    {
        $php = escapeshellarg(Package::phpBinaryPath());
        $artisan = escapeshellarg(base_path('artisan'));
        $command = escapeshellarg("{$plugin}:install");

        $cmd = static::withTimeout(static::TIMEOUT_SECONDS, "$php $artisan $command --no-interaction 2>&1");

        $output = [];
        $exitCode = 0;

        exec($cmd, $output, $exitCode);

        return [
            'ok'       => $exitCode === 0,
            'exitCode' => $exitCode,
            'output'   => implode(PHP_EOL, array_slice($output, -10)),
        ];
    }

    /**
     * Start `plugins:install-in-background` detached from this request, so
     * the page answers at once (2026-11-12: a synchronous install outlived the
     * web request; the browser gave up and the dialog stayed open although
     * the install finished). Output goes to a per-plugin log for support.
     */
    public function startInBackground(string $plugin, ?int $userId): void
    {
        $php = escapeshellarg(Package::phpBinaryPath());
        $artisan = escapeshellarg(base_path('artisan'));
        $arguments = escapeshellarg($plugin).($userId ? ' --user='.(int) $userId : '');
        $log = escapeshellarg(storage_path('logs/plugin-install-'.preg_replace('/[^a-z0-9_-]/i', '', $plugin).'.log'));

        exec("nohup $php $artisan plugins:install-in-background $arguments > $log 2>&1 &");
    }

    public static function withTimeout(int $seconds, string $command): string
    {
        if (PHP_OS_FAMILY === 'Windows') {
            return $command;
        }

        if (PHP_OS_FAMILY === 'Darwin') {
            $gtimeout = trim((string) shell_exec('which gtimeout 2>/dev/null'));

            return $gtimeout !== '' ? "gtimeout {$seconds} {$command}" : $command;
        }

        return "timeout {$seconds} {$command}";
    }
}
