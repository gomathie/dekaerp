<?php

use Webkul\Support\Enums\NavigationGroup;

require_once __DIR__.'/../../Helpers/TestBootstrapHelper.php';

/**
 * The module sidebar prints a section heading whenever the section changes
 * from one module to the next (user, 2026-11-10). Case order is the sidebar
 * order, so a module placed away from its section's siblings would print
 * that heading twice. Modules keep their own entries; only headings group them.
 */
it('keeps the modules of each sidebar section next to each other', function () {
    $seen = [];
    $previous = null;

    foreach (NavigationGroup::cases() as $case) {
        $section = $case->section();

        if ($section !== null && $section !== $previous) {
            expect($seen)->not->toContain($section, "Section {$section} is split around {$case->name}.");
            $seen[] = $section;
        }

        $previous = $section;
    }

    expect($seen)->toBe([
        'contacts-crm',
        'sales-purchases',
        'finance',
        'operations',
        'projects',
        'human-resources',
        'administration',
    ]);
});

it('places each module under the section the user approved', function (NavigationGroup $case, ?string $section) {
    expect($case->section())->toBe($section);
})->with([
    [NavigationGroup::Dashboard, null],
    // Changed 2026-11-11 (user): Contacts gets its own "Contacts & CRM"
    // heading, where a CRM module will join it.
    [NavigationGroup::Contact, 'contacts-crm'],
    [NavigationGroup::Sale, 'sales-purchases'],
    [NavigationGroup::Purchase, 'sales-purchases'],
    [NavigationGroup::Invoice, 'sales-purchases'],
    [NavigationGroup::Accounting, 'finance'],
    [NavigationGroup::Inventory, 'operations'],
    [NavigationGroup::Logistics, 'operations'],
    [NavigationGroup::Barcode, 'operations'],
    [NavigationGroup::Project, 'projects'],
    [NavigationGroup::Employee, 'human-resources'],
    [NavigationGroup::TimeOff, 'human-resources'],
    [NavigationGroup::Recruitment, 'human-resources'],
    [NavigationGroup::Setting, 'administration'],
]);

it('translates every section heading in each supported language', function (string $locale) {
    foreach (NavigationGroup::cases() as $case) {
        if ($case->section() === null) {
            continue;
        }

        $key = 'admin.navigation-sections.'.$case->section();

        expect(trans()->hasForLocale($key, $locale))->toBeTrue("Missing {$key} for {$locale}.");
    }
})->with(['en', 'fr', 'es', 'pt_BR', 'ar']);
