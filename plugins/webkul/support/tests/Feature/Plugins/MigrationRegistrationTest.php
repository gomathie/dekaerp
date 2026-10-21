<?php

require_once __DIR__.'/../../Helpers/TestBootstrapHelper.php';

/**
 * Every migration file must be named in its own plugin's hasMigrations() list.
 *
 * This repo registers plugin migrations explicitly rather than discovering
 * them, and nothing enforces it: a migration that is written but not listed
 * simply never runs. Nothing fails at the point of the mistake - the column is
 * absent later, somewhere unrelated, and the error names the symptom instead of
 * the cause.
 *
 * Three were found unregistered on 2026-10-21 alone:
 *   - logistics 2026_10_21_000005_add_telematics_agent_id_to_logistics_vehicles_table
 *   - security  2026_10_21_000002_add_must_change_password_to_users_table
 *   - security  2026_09_24_000001_provision_company_admin_role (months old; the
 *     Company Admin role was created only by DatabaseSeeder, never by an install)
 *
 * The check is deliberately a plain substring search of the provider source
 * rather than reflection over a built Package: it needs no plugin to be
 * installed, no database, and it cannot be fooled by a provider that fails to
 * boot. Plugins whose provider does not use an explicit hasMigrations([ ]) list
 * are skipped, because for those there is nothing to fall out of step with.
 */
/**
 * Already unregistered when this guard was written, each needing its own
 * decision rather than bulk registration - which is exactly why they are listed
 * instead of fixed here. Remove an entry as it is resolved; do not add one
 * without a reason.
 *
 * - accounts/2025_11_21_100010_create_accounts_account_companies_table:
 *   a dead duplicate. It runs the same Schema::create('accounts_account_companies')
 *   as the registered 2025_01_30_054955. Registering it would break
 *   migrate:fresh with "relation already exists", so it wants deleting.
 * - support/2026_03_09_000001_add_unique_index_to_companies_name:
 *   adds a UNIQUE index on companies.name. Applying it now would fail on any
 *   installation that already holds two companies with the same name, so it
 *   needs checking against production data first.
 * - the three employees calendar tables and the time-off leave-type share:
 *   not yet examined. Each may be needed, superseded, or dead.
 */
const KNOWN_UNREGISTERED_MIGRATIONS = [
    'accounts: 2025_11_21_100010_create_accounts_account_companies_table',
    'employees: 2024_12_11_100426_create_employees_calendars_table',
    'employees: 2024_12_11_100435_create_employees_calendar_attendances_table',
    'employees: 2024_12_11_100442_create_employees_calendar_leaves_table',
    'support: 2026_03_09_000001_add_unique_index_to_companies_name',
    'time-off: 2026_08_04_100000_share_default_time_off_leave_types',
];

it('names every plugin migration in that plugin’s hasMigrations list', function () {
    $pluginDirectories = glob(base_path('plugins/*/*'), GLOB_ONLYDIR) ?: [];

    expect($pluginDirectories)->not->toBeEmpty();

    $unregistered = [];
    $checkedPlugins = 0;

    foreach ($pluginDirectories as $pluginDirectory) {
        $migrations = glob($pluginDirectory.'/database/migrations/*.php') ?: [];

        if ($migrations === []) {
            continue;
        }

        $providers = glob($pluginDirectory.'/src/*ServiceProvider.php') ?: [];

        if ($providers === []) {
            continue;
        }

        $source = implode("\n", array_map(fn (string $file): string => (string) file_get_contents($file), $providers));

        // Auto-discovering plugins have no list to drift from.
        if (! str_contains($source, 'hasMigrations([')) {
            continue;
        }

        $checkedPlugins++;

        foreach ($migrations as $migration) {
            $name = basename($migration, '.php');

            if (! str_contains($source, $name)) {
                $unregistered[] = basename($pluginDirectory).': '.$name;
            }
        }
    }

    // Guards the guard: if the discovery above silently matched nothing, the
    // assertion below would pass while checking nothing at all.
    expect($checkedPlugins)->toBeGreaterThan(0);

    expect(array_values(array_diff($unregistered, KNOWN_UNREGISTERED_MIGRATIONS)))->toBe([]);
});

it('keeps the known-unregistered list honest', function () {
    // An allowlist nobody prunes becomes a place to hide new mistakes. This
    // fails once an entry is fixed, which is the prompt to delete the entry.
    $pluginDirectories = glob(base_path('plugins/*/*'), GLOB_ONLYDIR) ?: [];

    $stillUnregistered = [];

    foreach ($pluginDirectories as $pluginDirectory) {
        $providers = glob($pluginDirectory.'/src/*ServiceProvider.php') ?: [];

        if ($providers === []) {
            continue;
        }

        $source = implode("\n", array_map(fn (string $file): string => (string) file_get_contents($file), $providers));

        foreach (glob($pluginDirectory.'/database/migrations/*.php') ?: [] as $migration) {
            $name = basename($migration, '.php');

            if (! str_contains($source, $name)) {
                $stillUnregistered[] = basename($pluginDirectory).': '.$name;
            }
        }
    }

    $resolved = array_values(array_diff(KNOWN_UNREGISTERED_MIGRATIONS, $stillUnregistered));

    expect($resolved)->toBe([]);
});
