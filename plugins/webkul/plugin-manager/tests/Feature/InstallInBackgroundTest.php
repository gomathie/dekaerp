<?php

use Illuminate\Support\Facades\Cache;
use Webkul\PluginManager\Console\Commands\InstallInBackground;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Services\PluginInstallRunner;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensureERPInstalled();
});

/**
 * Installs run outside the web request since 2026-11-12: the page starts
 * plugins:install-in-background and returns, because an in-request install
 * outlived the request and left the dialog open. The command marks the plugin
 * installed and notifies the user, or tells them why it failed, and refuses a
 * second install of the same plugin while one is running.
 */
function fakeInstallRunner(bool $ok, int $exitCode = 0, string $output = ''): void
{
    app()->instance(PluginInstallRunner::class, new class($ok, $exitCode, $output) extends PluginInstallRunner
    {
        public array $installed = [];

        public function __construct(private bool $ok, private int $exitCode, private string $output) {}

        public function install(string $plugin): array
        {
            $this->installed[] = $plugin;

            return ['ok' => $this->ok, 'exitCode' => $this->exitCode, 'output' => $this->output];
        }
    });
}

function backgroundTestPlugin(): Plugin
{
    return Plugin::query()->create([
        'name'         => 'zz-background-demo',
        'author'       => 'Test',
        'is_installed' => false,
        'is_active'    => false,
    ]);
}

it('marks the plugin installed and notifies the user who started it', function (): void {
    fakeInstallRunner(true);
    $plugin = backgroundTestPlugin();
    $user = User::factory()->create();

    $this->artisan('plugins:install-in-background', ['plugin' => $plugin->name, '--user' => $user->id])
        ->assertSuccessful();

    expect($plugin->refresh()->is_installed)->toBeTrue()
        ->and($plugin->is_active)->toBeTrue()
        ->and($user->notifications()->count())->toBe(1);
});

it('leaves the plugin uninstalled and tells the user why when the install fails', function (): void {
    fakeInstallRunner(false, 1, 'SQLSTATE broken');
    $plugin = backgroundTestPlugin();
    $user = User::factory()->create();

    $this->artisan('plugins:install-in-background', ['plugin' => $plugin->name, '--user' => $user->id])
        ->assertFailed();

    expect($plugin->refresh()->is_installed)->toBeFalse()
        ->and($user->notifications()->count())->toBe(1)
        ->and(json_encode($user->notifications()->first()->data))->toContain('SQLSTATE broken');
});

it('refuses a second install of the same plugin while one is running', function (): void {
    fakeInstallRunner(true);
    $plugin = backgroundTestPlugin();

    $lock = Cache::lock(InstallInBackground::lockKey($plugin->name), 60);
    $lock->get();

    try {
        $this->artisan('plugins:install-in-background', ['plugin' => $plugin->name])->assertFailed();

        expect($plugin->refresh()->is_installed)->toBeFalse();
    } finally {
        $lock->release();
    }
});

it('refuses an unknown plugin', function (): void {
    fakeInstallRunner(true);

    $this->artisan('plugins:install-in-background', ['plugin' => 'no-such-plugin'])->assertFailed();
});
