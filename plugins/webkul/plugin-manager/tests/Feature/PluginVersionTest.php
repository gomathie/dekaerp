<?php

use Webkul\PluginManager\Models\Plugin;

/**
 * Plugin versions (user, 2026-11-13): a plugin we improved shows the app
 * release that last changed it, read from its own composer.json, never
 * upstream's number or the old "1.0.0" fallback.
 */
it('gives every plugin a version in its composer.json', function () {
    foreach (glob(base_path('plugins/webkul/*/composer.json')) as $file) {
        $version = data_get(json_decode(file_get_contents($file), true), 'version');

        expect($version)->toBeString()->toMatch('/^\d+\.\d+\.\d+$/', $file);
    }
});

it('shows the version from the plugin composer.json, not the stored one', function () {
    $expected = data_get(json_decode(file_get_contents(base_path('plugins/webkul/accounts/composer.json')), true), 'version');

    $plugin = new Plugin(['name' => 'accounts', 'latest_version' => '1.0.0']);

    expect($plugin->currentVersion())->toBe($expected);
});

it('falls back to the stored version for a plugin without a folder', function () {
    $plugin = new Plugin(['name' => 'not-a-plugin', 'latest_version' => '2.3.4']);

    expect($plugin->currentVersion())->toBe('2.3.4');
});

it('never reads outside the plugins folder', function () {
    $plugin = new Plugin(['name' => '../../composer', 'latest_version' => '1.0.0']);

    // basename() turns the name into "composer", so the root composer.json
    // (the app's own) is never read as a plugin's.
    expect($plugin->currentVersion())->toBe('1.0.0');
});
