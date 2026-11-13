<?php

namespace Webkul\Support\Filament\Contributions;

use Closure;
use Throwable;

/**
 * "Across the company" cards on the Overview's All tab (mockup approved by
 * the user, 2026-11-13): one compact card per module with its three key
 * items and one small visual.
 *
 * In `support` because Projects, Recruitment, Time Off and Logistics do not
 * depend on Invoices (which owns the Overview); each contributes its card
 * here and the Overview reads them, so no module names another.
 *
 * Keyed, so a second packageBooted() replaces rather than duplicates. Each
 * card decides its own visibility with its module's existing access rule
 * (resource canViewAny(), which carries the module switch and permission);
 * there is no separate dashboard permission to forget.
 */
class DashboardCardRegistry
{
    /**
     * @var array<string, array{sort: int, visible: Closure, data: Closure}>
     */
    protected static array $cards = [];

    /**
     * @param  Closure(): bool  $visible
     * @param  Closure(): array{label: string, url: ?string, items: list<array{label: string, value: string, tone?: string}>, bar?: array{percent: float|int, tone: string, caption: string}}  $data
     */
    public static function card(string $key, Closure $visible, Closure $data, int $sort = 0): void
    {
        static::$cards[$key] = ['sort' => $sort, 'visible' => $visible, 'data' => $data];
    }

    /**
     * The cards the current user may see, in order. A card whose query
     * fails is reported and left out, so one module's fault never takes
     * the whole dashboard down.
     *
     * @return list<array<string, mixed>>
     */
    public static function visible(): array
    {
        $cards = static::$cards;

        uasort($cards, fn (array $a, array $b): int => $a['sort'] <=> $b['sort']);

        $result = [];

        foreach ($cards as $key => $card) {
            try {
                if (! ($card['visible'])()) {
                    continue;
                }

                $result[] = ['key' => $key, ...($card['data'])()];
            } catch (Throwable $exception) {
                report($exception);
            }
        }

        return $result;
    }

    public static function forget(string $key): void
    {
        unset(static::$cards[$key]);
    }

    public static function flush(): void
    {
        static::$cards = [];
    }
}
