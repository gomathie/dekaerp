<?php

namespace Webkul\Invoice\Filament\Overview;

use Closure;

/**
 * The sections of the combined Overview dashboard (user, 2026-11-11: Sales,
 * Purchases and Invoices on one page, filterable by section).
 *
 * Sales and Purchases depend on Invoices, never the reverse, so each plugin
 * contributes its own section here instead of Invoices naming them. A
 * section is shown only when its own dashboard's canAccess() allows it, so
 * the module switch and the page permission keep deciding, in one place.
 *
 * Static and keyed: packageBooted() can run more than once, and a second
 * registration replaces the first instead of adding a duplicate.
 */
class OverviewRegistry
{
    /** @var array<string, array{label: Closure, widgets: list<class-string>, filters: Closure, visible: Closure, sort: int}> */
    protected static array $sections = [];

    /**
     * @param  Closure(): string  $label
     * @param  list<class-string>  $widgets
     * @param  Closure(): array<int, mixed>  $filters  extra filter fields, shown when only this section is selected
     * @param  Closure(): bool  $visible
     */
    public static function register(string $key, Closure $label, array $widgets, Closure $filters, Closure $visible, int $sort = 0): void
    {
        static::$sections[$key] = [
            'label'   => $label,
            'widgets' => $widgets,
            'filters' => $filters,
            'visible' => $visible,
            'sort'    => $sort,
        ];
    }

    /**
     * The sections the current user may see, in display order.
     *
     * @return array<string, array{label: Closure, widgets: list<class-string>, filters: Closure, visible: Closure, sort: int}>
     */
    public static function visible(): array
    {
        $sections = array_filter(static::$sections, fn (array $section): bool => (bool) ($section['visible'])());

        uasort($sections, fn (array $a, array $b): int => $a['sort'] <=> $b['sort']);

        return $sections;
    }

    public static function forget(string $key): void
    {
        unset(static::$sections[$key]);
    }

    public static function flush(): void
    {
        static::$sections = [];
    }
}
