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

    public static function section(string $key, Closure $render): void
    {
        static::$sections[$key] = $render;
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
    }
}
