<?php

use Filament\Widgets\Concerns\CanPoll;
use Filament\Widgets\Widget;
use Symfony\Component\Finder\Finder;

require_once __DIR__.'/../../Helpers/TestBootstrapHelper.php';

/**
 * Dashboard plan DB-6 (2026-11-23): no widget refreshes more often than every
 * five minutes. Filament's stats and chart widgets poll every 5 seconds unless
 * told otherwise (CanPoll), which re-runs every query of every open dashboard
 * twelve times a minute; on the Overview that was Sentry DEKAERP-11. Projects
 * and Recruitments polled every 15 s, Logistics every 60 s, and three widgets
 * kept the 5-second default.
 *
 * Scans every widget class in every plugin, so a new widget that forgets to set
 * an interval fails here rather than on a client's database.
 */
it('never refreshes a widget more often than every five minutes', function () {
    $offenders = [];
    $checked = [];

    $files = Finder::create()->files()->name('*.php')->in(base_path('plugins/webkul/*/src'))->contains('/class \w+ extends/');

    foreach ($files as $file) {
        $source = $file->getContents();

        if (! preg_match('/^namespace\s+([^;]+);/m', $source, $namespace) || ! preg_match('/^(?:final\s+|abstract\s+)?class\s+(\w+)/m', $source, $class)) {
            continue;
        }

        $name = $namespace[1].'\\'.$class[1];

        if (! class_exists($name) || ! is_subclass_of($name, Widget::class) || (new ReflectionClass($name))->isAbstract()) {
            continue;
        }

        if (! in_array(CanPoll::class, class_uses_recursive($name), true)) {
            continue;
        }

        $checked[] = $name;

        $interval = (new ReflectionClass($name))->getDefaultProperties()['pollingInterval'] ?? null;

        if ($interval === null) {
            continue;
        }

        $seconds = (int) $interval * (str_ends_with((string) $interval, 'm') ? 60 : 1);

        if ($seconds < 300) {
            $offenders[$name] = $interval;
        }
    }

    // A scan that found nothing would pass vacuously; the plugins hold dozens of
    // stats and chart widgets, including the ones this test was written for.
    expect(count($checked))->toBeGreaterThan(20)
        ->and($checked)->toContain(\Webkul\Project\Filament\Widgets\StatsOverviewWidget::class)
        ->and($offenders)->toBe([]);
});
