<?php

namespace Webkul\Support\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Cache;
use UnitEnum;
use Webkul\Support\Enums\NavigationGroup;

class WhatsNew extends Page
{
    protected string $view = 'support::pages.whats-new';

    protected static ?string $slug = 'whats-new';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-megaphone';

    protected static ?int $navigationSort = 0;

    public static function getNavigationLabel(): string
    {
        return __('support::filament/pages/whats-new.navigation.label');
    }

    public static function getNavigationGroup(): string|UnitEnum
    {
        return NavigationGroup::Help;
    }

    public function getTitle(): string
    {
        return __('support::filament/pages/whats-new.title');
    }

    public function getHeading(): string
    {
        return __('support::filament/pages/whats-new.heading');
    }

    public function getSubheading(): ?string
    {
        return __('support::filament/pages/whats-new.subheading', ['app' => $this->appName()]);
    }

    /**
     * The product name shown to users.
     *
     * See Help::appName() - same reasoning, kept in sync with it.
     */
    protected function appName(): string
    {
        return (string) config('app.name', 'ERP');
    }

    /**
     * CHANGELOG.md, parsed into releases.
     *
     * CHANGELOG.md is the single source of truth for release notes (also used
     * for GitHub releases etc.), so this page reads it rather than duplicating
     * its content in a second, easily-forgotten place. Cached against the
     * file's own mtime: an edit to CHANGELOG.md is a new cache key, so it
     * shows up immediately without needing the cache cleared, while a repeat
     * request within the day skips re-parsing several hundred lines of
     * markdown.
     *
     * @return array<int, array{version: string, sections: array<int, array{label: string, items: array<int, string>}>}>
     */
    public function releases(): array
    {
        $path = base_path('CHANGELOG.md');

        if (! is_file($path)) {
            return [];
        }

        return Cache::remember(
            'support.whats-new.'.filemtime($path),
            now()->addDay(),
            fn (): array => $this->parseChangelog((string) file_get_contents($path)),
        );
    }

    public function hasReleases(): bool
    {
        return $this->releases() !== [];
    }

    /**
     * Icon for a release section, matched against its heading label.
     */
    public function sectionIcon(string $label): string
    {
        return match (true) {
            str_contains($label, 'Feature')     => 'heroicon-o-sparkles',
            str_contains($label, 'Improvement') => 'heroicon-o-arrow-trending-up',
            str_contains($label, 'Fix')         => 'heroicon-o-wrench-screwdriver',
            str_contains($label, 'Upgrade')     => 'heroicon-o-arrow-up-circle',
            default                             => 'heroicon-o-document-text',
        };
    }

    /**
     * Turn CHANGELOG.md's markdown into a list of releases.
     *
     * The format is a flat, unnumbered outline:
     *
     *   # 🚀 CHANGELOG — v1.5.0
     *   ### 🧩 Features
     *   * item
     *   * item
     *   ### 🐛 Fixes
     *   * item
     *
     * Every "# " line starts a new release (its version is whatever trails
     * the heading, e.g. "v1.5.0" or "v1.3.0-BETA3"); every "### " line starts
     * a new section within it, labelled by its text with the leading emoji
     * stripped; every "* " line is an item in whichever section came last.
     * Anything before the first release, or before a release's first
     * section, is not release-notes content and is skipped.
     *
     * @return array<int, array{version: string, sections: array<int, array{label: string, items: array<int, string>}>}>
     */
    protected function parseChangelog(string $markdown): array
    {
        $releases = [];
        $release = null;
        $section = null;

        foreach (preg_split('/\r?\n/', $markdown) as $line) {
            if (preg_match('/^#\s.*(v\d[\w.\-]*)\s*$/u', $line, $matches)) {
                if ($release) {
                    $releases[] = $release;
                }

                $release = ['version' => $matches[1], 'sections' => []];
                $section = null;

                continue;
            }

            if (! $release) {
                continue;
            }

            if (preg_match('/^###\s*(.+?)\s*$/u', $line, $matches)) {
                $label = trim((string) preg_replace('/^[^\p{L}]+/u', '', $matches[1]));

                $release['sections'][] = ['label' => $label, 'items' => []];
                $section = array_key_last($release['sections']);

                continue;
            }

            if ($section === null) {
                continue;
            }

            if (preg_match('/^\*\s+(.+?)\s*$/u', $line, $matches)) {
                $release['sections'][$section]['items'][] = $matches[1];
            }
        }

        if ($release) {
            $releases[] = $release;
        }

        return $releases;
    }
}
