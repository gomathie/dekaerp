<?php

namespace Webkul\Support\Filament\Contributions;

use Closure;
use Illuminate\Support\HtmlString;

/**
 * Slots other plugins can contribute into the dashboard welcome banner.
 *
 * The banner lives in `support`, the base plugin, while what it can offer
 * (clocks, presence, anything module-specific) belongs to plugins above it.
 * Contributing through a registry is what keeps `support` from referencing a
 * plugin that depends on it - the same reason CompanySchemaRegistry exists.
 *
 * A section is keyed, so contributing twice replaces rather than duplicates:
 * `packageBooted()` runs again from the installer and the test bootstrapper.
 */
class DashboardBannerRegistry
{
    /**
     * @var array<string, Closure>
     */
    protected static array $sections = [];

    /**
     * @var array<int, Closure>
     */
    protected static array $locations = [];

    /**
     * @var array<string, Closure(): list<string>>
     */
    protected static array $summaries = [];

    /**
     * @var array<string, Closure(): list<array{label: string, url: string, icon: string}>>
     */
    protected static array $actions = [];

    public static function section(string $key, Closure $render): void
    {
        static::$sections[$key] = $render;
    }

    /**
     * Short "today" phrases under the greeting, e.g. "3 invoices overdue"
     * (user, 2026-11-13). Keyed like section(), so re-booting replaces.
     */
    public static function summary(string $key, Closure $phrases): void
    {
        static::$summaries[$key] = $phrases;
    }

    /**
     * Quick "new" actions shown in the banner. The contributor decides what
     * the viewer may create; the banner only renders.
     */
    public static function actions(string $key, Closure $actions): void
    {
        static::$actions[$key] = $actions;
    }

    /**
     * @return list<string>
     */
    public static function summaryPhrases(): array
    {
        $phrases = [];

        foreach (static::$summaries as $resolve) {
            array_push($phrases, ...array_values(array_filter((array) $resolve(), 'is_string')));
        }

        return $phrases;
    }

    /**
     * At most three: more buttons than that turned the banner into a toolbar.
     *
     * @return list<array{label: string, url: string, icon: string}>
     */
    public static function quickActions(): array
    {
        $actions = [];

        foreach (static::$actions as $resolve) {
            array_push($actions, ...(array) $resolve());
        }

        return array_slice($actions, 0, 3);
    }

    /**
     * Where the person is working. Falls back to the company name when no
     * plugin answers, so the greeting never renders an empty sentence.
     */
    public static function location(Closure $resolve): void
    {
        static::$locations[] = $resolve;
    }

    public static function renderedSections(): HtmlString
    {
        $html = '';

        foreach (static::$sections as $render) {
            $html .= (string) $render();
        }

        return new HtmlString($html);
    }

    public static function resolvedLocation(): ?string
    {
        foreach (static::$locations as $resolve) {
            $location = $resolve();

            if (is_string($location) && trim($location) !== '') {
                return $location;
            }
        }

        return null;
    }

    public static function flush(): void
    {
        static::$sections = [];
        static::$locations = [];
        static::$summaries = [];
        static::$actions = [];
    }
}
