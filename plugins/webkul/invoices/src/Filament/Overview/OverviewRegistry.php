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
    /**
     * @param  (Closure(): list<array{label: string, count: int, url: ?string, tone: string}>)|null  $attention
     *                                                                                                           what needs someone today (Needs attention list and the greeting's summary line)
     * @param  (Closure(): list<array{label: string, url: string, icon: string}>)|null  $actions
     *                                                                                            quick "new" actions for the greeting
     */
    public static function register(string $key, Closure $label, array $widgets, Closure $filters, Closure $visible, int $sort = 0, ?string $headline = null, ?Closure $attention = null, ?Closure $actions = null): void
    {
        static::$sections[$key] = [
            'label'     => $label,
            'widgets'   => $widgets,
            'filters'   => $filters,
            'visible'   => $visible,
            'sort'      => $sort,
            // The section's card in the "At a glance" row, shown first.
            'headline'  => $headline,
            'attention' => $attention,
            'actions'   => $actions,
        ];
    }

    /**
     * Every visible section's open items, only those with something to do.
     * One source for the Needs attention list and the greeting's summary,
     * so the two never disagree.
     *
     * @return list<array{label: string, count: int, url: ?string, tone: string}>
     */
    public static function attentionItems(): array
    {
        $items = [];

        foreach (static::visible() as $section) {
            foreach ((($section['attention'] ?? null) ?: fn (): array => [])() as $item) {
                if (($item['count'] ?? 0) > 0) {
                    $items[] = $item;
                }
            }
        }

        return $items;
    }

    /**
     * @return list<array{label: string, url: string, icon: string}>
     */
    public static function quickActions(): array
    {
        $actions = [];

        foreach (static::visible() as $section) {
            array_push($actions, ...((($section['actions'] ?? null) ?: fn (): array => [])()));
        }

        return $actions;
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
