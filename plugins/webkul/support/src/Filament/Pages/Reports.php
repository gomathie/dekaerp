<?php

namespace Webkul\Support\Filament\Pages;

use BackedEnum;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Resources\Resource;
use Illuminate\Contracts\Support\Htmlable;
use Throwable;
use UnitEnum;
use Webkul\Support\Enums\NavigationGroup;

/**
 * One place to find every report (reports plan R1, user decisions REP-D1/2,
 * 2026-11-12): each module's reports stay in its own Reporting section, and
 * this page lists the ones the viewer can open, grouped by module.
 *
 * Discovered, not registered: every page or resource whose cluster is a
 * module's `Reporting` cluster appears, so a new report shows up without a
 * second list to keep in step (a registry would be one more thing to forget).
 * An entry shows only when its own canAccess() / canViewAny() passes, so the
 * module switch, the Shield permission and the company all come from the
 * report itself; this page has no permission of its own and opens for anyone
 * who can see at least one report.
 */
class Reports extends Page
{
    protected string $view = 'support::filament.pages.reports';

    protected static ?int $navigationSort = -9;

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return NavigationGroup::Dashboard;
    }

    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null
    {
        return null;
    }

    public static function getNavigationLabel(): string
    {
        return __('support::filament/pages/reports.title');
    }

    public function getTitle(): string|Htmlable
    {
        return __('support::filament/pages/reports.title');
    }

    public static function canAccess(): bool
    {
        return static::entries() !== [];
    }

    /**
     * The reports this user can open, grouped by module label, sorted.
     *
     * @return array<string, list<array{label: string, url: string, icon: ?string}>>
     */
    public static function entries(): array
    {
        $panel = Filament::getCurrentPanel() ?? Filament::getPanel('admin');

        $groups = [];

        foreach ([...$panel->getPages(), ...$panel->getResources()] as $class) {
            try {
                $cluster = $class::getCluster();

                if ($cluster === null || class_basename($cluster) !== 'Reporting') {
                    continue;
                }

                $isResource = is_subclass_of($class, Resource::class);

                if (! ($isResource ? $class::canViewAny() : $class::canAccess())) {
                    continue;
                }

                $group = $cluster::getNavigationGroup();
                $groupLabel = $group instanceof NavigationGroup ? $group->getLabel() : (string) ($group ?? class_basename($cluster));

                $icon = $class::getNavigationIcon();

                $groups[$groupLabel][] = [
                    'label' => $isResource ? $class::getNavigationLabel() : $class::getNavigationLabel(),
                    'url'   => $isResource ? $class::getUrl('index') : $class::getUrl(),
                    'icon'  => is_string($icon) ? $icon : null,
                ];
            } catch (Throwable $exception) {
                // One broken report page must not hide every other report.
                report($exception);
            }
        }

        ksort($groups);

        foreach ($groups as &$items) {
            usort($items, fn (array $a, array $b): int => strcmp($a['label'], $b['label']));
        }

        return $groups;
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        return ['groups' => static::entries()];
    }
}
