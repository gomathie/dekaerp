<?php

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

require_once __DIR__.'/../../Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../Helpers/ResourceStructureHelper.php';

/**
 * Phase 0 of docs/upstream-structure-alignment-plan.md: the net that must
 * exist before any resource is restructured.
 *
 * That work moves form(), infolist() and table() bodies out of each resource
 * into Schemas/ and Tables/ classes and is meant to change no behaviour at
 * all. The risk is silent: a move that drops a field, a company scope or a
 * visibleFrom() still passes the existing suites, because they assert what a
 * permitted user can see rather than the whole declared shape.
 * docs/completed/restructure-backlog.md compares code as sets, so it cannot see field
 * order either - and in Filament, field order is display order.
 *
 * Fixtures are per plugin so converting one plugin re-runs only that plugin,
 * matching the plan's one-plugin-per-commit rule.
 *
 * Every covered plugin is installed first (STRUCTURE_PLUGINS), so fixtures
 * hold real trees and come out the same from a filtered or a full run. A
 * fixture that still records `__not_installed` means that install failed:
 * treat it as a broken net, not a baseline.
 */
/**
 * Every plugin a fixture covers, installed before any snapshot is taken.
 *
 * Installing only the row's own plugin made the result depend on run order
 * and on --filter: a plugin contributes fields into another's resource
 * through the schema registries, so the same fixture came out different
 * from a filtered run and a full one (the 50-vs-30 security.json incident).
 * With the full set installed first, every run sees the same panel.
 *
 * Partners and Fields are absent on purpose: they are core, installed with
 * the ERP, and have no install command - listing them failed every test.
 */
const STRUCTURE_PLUGINS = [
    'accounts', 'accounting', 'inventories', 'sales', 'purchases', 'manufacturing',
    'products', 'projects', 'employees', 'recruitments', 'time-off',
    'timesheets', 'maintenance', 'contacts', 'logistics',
];

beforeEach(function () {
    foreach (STRUCTURE_PLUGINS as $plugin) {
        TestBootstrapHelper::ensurePluginInstalled($plugin);
    }

    FilamentHelper::actingAs();
});

function structureFixturePath(string $plugin): string
{
    return __DIR__.'/fixtures/'.$plugin.'.json';
}

it('sees registered resources at all', function () {
    // If the panel boots without resources every snapshot below would be a
    // vacuously passing empty array.
    expect(ResourceStructureHelper::resources())->not->toBeEmpty();
});

it('records the declared structure of a plugin’s resources', function (string $plugin, string $namespace) {
    $resources = ResourceStructureHelper::resources($namespace);

    // Never skipped. Which plugins the test bootstrap installs varies between
    // runs, so one run captured 7 plugins and the next only 3 - and a skip
    // reported that as success. Recording the absence puts it in the fixture,
    // where a later run that does see the plugin fails loudly on the diff
    // instead of quietly baselining less than before.
    $snapshot = $resources === []
        ? ['__not_installed' => 'No '.$namespace.' resources are registered in the admin panel for this run.']
        : [];

    foreach ($resources as $resource) {
        $snapshot[$resource] = ResourceStructureHelper::describeResource($resource);
    }

    $encoded = json_encode($snapshot, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)."\n";

    $path = structureFixturePath($plugin);

    // A missing fixture is written as the baseline; an existing one is
    // compared. Regenerate deliberately by deleting the file, never by
    // editing it to match.
    if (! file_exists($path)) {
        if (! is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        file_put_contents($path, $encoded);

        expect(file_exists($path))->toBeTrue();

        return;
    }

    expect($encoded)->toBe(file_get_contents($path));
})->with([
    ['accounts', 'Webkul\\Account'],
    ['accounting', 'Webkul\\Accounting'],
    ['inventories', 'Webkul\\Inventory'],
    ['sales', 'Webkul\\Sale'],
    ['purchases', 'Webkul\\Purchase'],
    ['manufacturing', 'Webkul\\Manufacturing'],
    ['products', 'Webkul\\Product'],
    ['partners', 'Webkul\\Partner'],
    ['projects', 'Webkul\\Project'],
    ['employees', 'Webkul\\Employee'],
    ['security', 'Webkul\\Security'],
    ['support', 'Webkul\\Support'],
    ['logistics', 'Webkul\\Logistics'],
    ['recruitments', 'Webkul\\Recruitment'],
    ['time-off', 'Webkul\\TimeOff'],
    ['timesheets', 'Webkul\\Timesheet'],
    ['maintenance', 'Webkul\\Maintenance'],
    ['fields', 'Webkul\\Field'],
    ['contacts', 'Webkul\\Contact'],
    ['plugin-manager', 'Webkul\\PluginManager'],
    ['invoices', 'Webkul\\Invoice'],
    ['platform-billing', 'Webkul\\PlatformBilling'],
]);

it('keeps declaration order instead of sorting', function () {
    // The property docs/completed/restructure-backlog.md's set comparison cannot check.
    $described = ResourceStructureHelper::describeSchema(Schema::make()->components([
        TextInput::make('zebra'),
        TextInput::make('alpha'),
    ]));

    expect(array_column($described, 'name'))->toBe(['zebra', 'alpha']);
});

it('keeps nesting instead of flattening it', function () {
    $described = ResourceStructureHelper::describeSchema(Schema::make()->components([
        Section::make('outer')->schema([
            TextInput::make('inner'),
        ]),
    ]));

    expect($described)->toHaveCount(1)
        ->and($described[0]['children'][0]['name'] ?? null)->toBe('inner');
});

it('records hidden components so losing one shows up as a diff', function () {
    $described = ResourceStructureHelper::describeSchema(Schema::make()->components([
        TextInput::make('always'),
        TextInput::make('never')->visible(false),
    ]));

    expect(array_column($described, 'name'))->toBe(['always', 'never']);
});
