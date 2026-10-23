<?php

require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

/**
 * UF-017: no chatter view may render content unescaped except through a sink
 * known to sanitise it.
 *
 * Upstream v1.6.0 escaped HTML entities in chatter change summaries to close a
 * stored XSS. Investigated on 2026-10-22: this fork was never exposed. Change
 * summaries render old_value, new_value and the field name through `{{ }}`; the
 * step icon is a hardcoded match() over static SVG paths; and activity bodies
 * are built from translation strings chosen by an enum, because no
 * getModelTitle() implementation returns a model attribute.
 *
 * So there is nothing to fix - which makes the risk that someone reintroduces
 * it. Asserting "Blade escapes {{ }}" would be tautological, so this pins the
 * decision instead: every `{!! !!}` in a chatter view must be one of the sinks
 * below, and a new one fails until it is justified and added here.
 *
 * Chatter is rendered on shipments, trips, expenses, invoices and most entity
 * types, so an unescaped write here would be a stored XSS against every tenant.
 */
const CHATTER_ALLOWED_UNESCAPED_SINKS = [
    // Laravel's HTML sanitiser strips scripts and event handlers.
    'sanitizeHtml()',

    // Filament's RichContentRenderer, fed a rich-editor document. This is the
    // legitimate rich text the upstream note warns against breaking, and it is
    // the only reason any of these views are unescaped at all.
    'ChatterMentions::render(',

    // A hardcoded match() over static SVG path data, keyed on $change['type'].
    // No user input reaches it.
    '$icon',
];

it('renders nothing unescaped in chatter views except through a known-safe sink', function () {
    // Recursive, because the views sit several directories deep and a glob
    // would quietly miss the nested ones.
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator(base_path('plugins/webkul/chatter/resources/views'))
    );

    $views = [];

    foreach ($iterator as $file) {
        if ($file->isFile() && str_ends_with($file->getFilename(), '.blade.php')) {
            $views[] = $file->getPathname();
        }
    }

    // Guards the guard: a glob that matched nothing would pass while checking
    // nothing at all.
    expect($views)->not->toBeEmpty();

    $unjustified = [];

    foreach ($views as $view) {
        $contents = (string) file_get_contents($view);

        if (! preg_match_all('/\{!!(.+?)!!\}/s', $contents, $matches)) {
            continue;
        }

        foreach ($matches[1] as $expression) {
            $isAllowed = false;

            foreach (CHATTER_ALLOWED_UNESCAPED_SINKS as $sink) {
                if (str_contains($expression, $sink)) {
                    $isAllowed = true;

                    break;
                }
            }

            if (! $isAllowed) {
                $unjustified[] = basename($view).': {!! '.trim($expression).' !!}';
            }
        }
    }

    expect($unjustified)->toBe([]);
});

it('escapes the change-summary values, which is where upstream found the hole', function () {
    // Named explicitly so that changing these three to {!! !!} fails with a
    // message that says why it matters, rather than only tripping the scan above.
    $view = base_path('plugins/webkul/chatter/resources/views/filament/infolists/components/messages/content-text-entry.blade.php');

    expect(file_exists($view))->toBeTrue();

    $contents = (string) file_get_contents($view);

    expect($contents)->toContain("{{ is_array(\$change['old_value']) ? implode(', ', \$change['old_value']) : \$change['old_value'] }}")
        ->and($contents)->toContain("{{ is_array(\$change['new_value']) ? implode(', ', \$change['new_value']) : \$change['new_value'] }}")
        ->and($contents)->toContain("{{ ucwords(str_replace('_', ' ', \$field)) }}");
});

it('keeps activity descriptions free of model attributes', function () {
    // The one place user text could reach an unescaped sink: activity bodies go
    // through ChatterMentions::render(), and they are built by interpolating
    // getModelTitle() into a translation string. Every implementation returns a
    // static string or a match() over an enum. One returning $this->name would
    // put a record's own name into unescaped HTML.
    $implementations = glob(base_path('plugins/webkul/*/src/Models/*.php')) ?: [];

    expect($implementations)->not->toBeEmpty();

    $returningAttributes = [];

    foreach ($implementations as $file) {
        $contents = (string) file_get_contents($file);

        if (! preg_match('/function getModelTitle\([^)]*\)[^{]*\{(.*?)\n    \}/s', $contents, $match)) {
            continue;
        }

        $body = $match[1];

        // A match() on an enum is fine - it selects between fixed strings. A
        // plain `return $this->something` is not.
        if (preg_match('/return\s+\$this->[a-zA-Z_]+\s*;/', $body)) {
            $returningAttributes[] = basename($file);
        }
    }

    expect($returningAttributes)->toBe([]);
});
