<?php

namespace Webkul\Support\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use UnitEnum;
use Webkul\Support\Enums\NavigationGroup;

/**
 * In-app release notes, read from the root CHANGELOG.md (plan
 * docs/completed/whats-new-plan.md, WN-1 to WN-6).
 *
 * The class name and the `whats-new` slug are kept on purpose: the profile
 * menu, the top-bar shortcut and old bookmarks link here.
 */
class WhatsNew extends Page
{
    /** Categories the page filters on. Anything else keeps its own heading. */
    public const CATEGORIES = ['new', 'improved', 'fixed', 'upgrade'];

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
        return __('support::filament/pages/whats-new.heading', ['app' => $this->appName()]);
    }

    public function getSubheading(): ?string
    {
        return __('support::filament/pages/whats-new.subheading', ['app' => $this->appName()]);
    }

    /**
     * Opening the page clears the top-bar "new" dot (D-WN-2: per user, so it
     * follows them across devices).
     */
    public function mount(): void
    {
        static::markSeen();
    }

    /**
     * See Help::appName() - same reasoning, kept in sync with it.
     */
    protected function appName(): string
    {
        return (string) config('app.name', 'ERP');
    }

    /**
     * @return array<int, array{id: string, version: string, date: ?string, sections: array<int, array{category: string, label: string, items: array<int, array{html: string, text: string, security: bool}>}>}>
     */
    public function releases(): array
    {
        return static::groupedReleases();
    }

    /**
     * Releases as people read them: a hotfix (vX.Y.Z, Z > 0) is folded into
     * its release vX.Y.0, each of its items tagged with the hotfix version,
     * instead of being a section of its own (user, 2026-11-15: too many
     * release sections). A hotfix whose release is not in the file stays on
     * its own. CHANGELOG.md keeps one heading per version, and loadReleases()
     * stays ungrouped: the newest version still drives the "new" dot.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function groupedReleases(?array $releases = null): array
    {
        $releases ??= static::loadReleases();
        $byMinor = [];

        foreach ($releases as $index => $release) {
            if (preg_match('/^v(\d+)\.(\d+)\.0$/', $release['version'], $m)) {
                $byMinor[$m[1].'.'.$m[2]] = $index;
            }
        }

        $grouped = $releases;

        foreach ($releases as $index => $release) {
            if (! preg_match('/^v(\d+)\.(\d+)\.(\d+)$/', $release['version'], $m) || (int) $m[3] === 0) {
                continue;
            }

            $parent = $byMinor[$m[1].'.'.$m[2]] ?? null;

            if ($parent === null) {
                continue;
            }

            foreach ($release['sections'] as $section) {
                $items = array_map(fn (array $item): array => [
                    ...$item,
                    'html' => $item['html'].' <span class="fi-whats-new-hotfix">('.e($release['version']).')</span>',
                    'text' => $item['text'].' ('.$release['version'].')',
                ], $section['items']);

                $target = null;

                foreach ($grouped[$parent]['sections'] as $i => $existing) {
                    if ($existing['category'] === $section['category']) {
                        $target = $i;

                        break;
                    }
                }

                if ($target === null) {
                    $grouped[$parent]['sections'][] = [...$section, 'items' => $items];
                } else {
                    $grouped[$parent]['sections'][$target]['items'] = [...$items, ...$grouped[$parent]['sections'][$target]['items']];
                }
            }

            // Old links to the hotfix's own PDF (/whats-new/pdf/1-11-2) must
            // keep working: the release remembers the ids it absorbed.
            $grouped[$parent]['ids'] = [...($grouped[$parent]['ids'] ?? [$grouped[$parent]['id']]), $release['id']];

            unset($grouped[$index]);
        }

        return array_values($grouped);
    }

    public function hasReleases(): bool
    {
        return $this->releases() !== [];
    }

    /**
     * CHANGELOG.md is the single source of truth (no second copy that could
     * drift). Cached on the file's mtime, so an edit shows at once. The PDF
     * controller calls this too: one parser, one read.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function loadReleases(): array
    {
        $path = base_path('CHANGELOG.md');

        if (! is_file($path)) {
            return [];
        }

        // Keyed on the content, not filemtime(): a deploy can keep the old
        // timestamp, and then the page served the previous release's notes
        // for a day (v1.18.0 missing on What's New, 2026-11-27).
        return Cache::remember(
            'support.whats-new.v3.'.md5_file($path),
            now()->addDay(),
            fn (): array => static::parseChangelog((string) file_get_contents($path)),
        );
    }

    public static function latestVersion(): ?string
    {
        return static::loadReleases()[0]['version'] ?? null;
    }

    /**
     * True when the newest release is one this user has not opened yet.
     */
    public static function hasUnseenRelease(): bool
    {
        $user = auth()->user();
        $latest = static::latestVersion();

        if (! $user || ! $latest || ! static::canTrackSeen()) {
            return false;
        }

        $seen = $user->whats_new_seen_version;

        return blank($seen) || version_compare(ltrim($latest, 'v'), ltrim((string) $seen, 'v'), '>');
    }

    public static function markSeen(): void
    {
        $user = auth()->user();
        $latest = static::latestVersion();

        if (! $user || ! $latest || ! static::canTrackSeen() || $user->whats_new_seen_version === $latest) {
            return;
        }

        // A direct column write: no model events, so opening release notes
        // never counts as a profile change or touches updated_at.
        $user->newQuery()->whereKey($user->getKey())->update(['whats_new_seen_version' => $latest]);
        $user->setRawAttributes(array_merge($user->getAttributes(), ['whats_new_seen_version' => $latest]), true);
    }

    /** Before the migration has run (deploy window), the dot simply stays off. */
    protected static function canTrackSeen(): bool
    {
        return once(fn (): bool => Schema::hasColumn('users', 'whats_new_seen_version'));
    }

    public static function sectionLabel(array $section): string
    {
        return $section['category'] === 'other'
            ? $section['label']
            : __('support::filament/pages/whats-new.categories.'.$section['category']);
    }

    public static function sectionIcon(string $category): string
    {
        return match ($category) {
            'new'      => 'heroicon-o-sparkles',
            'improved' => 'heroicon-o-arrow-trending-up',
            'fixed'    => 'heroicon-o-wrench-screwdriver',
            'upgrade'  => 'heroicon-o-arrow-up-circle',
            default    => 'heroicon-o-document-text',
        };
    }

    /**
     * Heading text to a fixed category, whatever emoji or wording it carries
     * (G8: "🧩 Features" and "Features" were shown as different labels).
     */
    public static function categoryFor(string $label): string
    {
        $label = Str::lower($label);

        return match (true) {
            str_contains($label, 'feature') || $label === 'new' || str_contains($label, 'added')  => 'new',
            str_contains($label, 'improve') || str_contains($label, 'changed')                    => 'improved',
            str_contains($label, 'fix')                                                           => 'fixed',
            str_contains($label, 'upgrade')                                                       => 'upgrade',
            default                                                                               => 'other',
        };
    }

    /**
     * Parse CHANGELOG.md into releases.
     *
     * - A `#` or `##` heading carrying a version starts a release, in either
     *   form: "# 🚀 CHANGELOG — v1.6.0" or "## 1.8.0 — 2026-11-20". Text after
     *   the version's dash is the release date.
     * - "## Unreleased" starts a block that is not shown (D-WN-1): entries
     *   wait there until the production push that ships them.
     * - `###` starts a section. A bullet is an entry, and its wrapped or
     *   indented lines belong to it (G4: they used to be dropped). Prose
     *   under a heading is one entry. HTML comments are authoring notes and
     *   are removed. Code fences are kept whole.
     * - Rendered with raw HTML stripped and unsafe links refused (G1).
     *
     * @return array<int, array<string, mixed>>
     */
    public static function parseChangelog(string $markdown): array
    {
        $markdown = (string) preg_replace('/<!--.*?-->/s', '', $markdown);

        $releases = [];
        $release = null;
        $section = null;
        $buffer = null;
        $inFence = false;
        $skipping = false;

        $flush = function () use (&$release, &$section, &$buffer): void {
            if ($buffer !== null && $release !== null && $section !== null && trim($buffer) !== '') {
                $release['sections'][$section]['items'][] = static::makeItem($buffer);
            }

            $buffer = null;
        };

        foreach (preg_split('/\r?\n/', $markdown) as $line) {
            if (preg_match('/^\s*(```|~~~)/', $line)) {
                $inFence = ! $inFence;
                $buffer = ($buffer ?? '').$line."\n";

                continue;
            }

            if ($inFence) {
                $buffer = ($buffer ?? '').$line."\n";

                continue;
            }

            if (preg_match('/^#{1,2}\s+(.+?)\s*$/u', $line, $heading)) {
                $flush();

                if ($release) {
                    $releases[] = $release;
                }

                $release = null;
                $section = null;
                $skipping = true;

                if (preg_match('/v?(\d+\.\d+(?:\.\d+)?(?:-[\w.]+)?)(?:\s*[—–-]\s*(.+))?$/u', $heading[1], $version)
                    && ! Str::contains(Str::lower($heading[1]), 'unreleased')) {
                    $skipping = false;
                    $release = [
                        'id'       => Str::slug(str_replace('.', '-', $version[1])),
                        'version'  => 'v'.$version[1],
                        'date'     => isset($version[2]) ? trim($version[2]) : null,
                        'sections' => [],
                    ];
                }

                continue;
            }

            if ($skipping || ! $release) {
                continue;
            }

            if (preg_match('/^###\s*(.+?)\s*$/u', $line, $matches)) {
                $flush();

                $label = trim((string) preg_replace('/^[^\p{L}]+/u', '', $matches[1]));
                $category = static::categoryFor($label);

                $release['sections'][] = [
                    'category' => $category,
                    // Translated at render time, not here: the parsed result is
                    // cached once for every locale.
                    'label'    => $label,
                    'items'    => [],
                ];
                $section = array_key_last($release['sections']);

                continue;
            }

            if ($section === null) {
                continue;
            }

            if (preg_match('/^[*-]\s+(.*)$/u', $line, $matches)) {
                $flush();
                $buffer = $matches[1];

                continue;
            }

            if (trim($line) === '') {
                $flush();

                continue;
            }

            $buffer = $buffer === null ? trim($line) : $buffer.' '.trim($line);
        }

        $flush();

        if ($release) {
            $releases[] = $release;
        }

        return $releases;
    }

    /**
     * @return array{html: string, text: string, security: bool}
     */
    protected static function makeItem(string $source): array
    {
        $source = trim($source);
        $security = (bool) preg_match('/^\[Security\]\s*/i', $source);

        if ($security) {
            $source = (string) preg_replace('/^\[Security\]\s*/i', '', $source);
        }

        $html = (string) Str::markdown($source, [
            'html_input'         => 'strip',
            'allow_unsafe_links' => false,
        ]);

        // A one-paragraph entry renders inline inside its list item.
        if (substr_count($html, '<p>') === 1 && ! str_contains($html, '<pre')) {
            $html = (string) preg_replace('#^\s*<p>(.*)</p>\s*$#s', '$1', $html);
        }

        return [
            'html'     => trim($html),
            'text'     => Str::lower(trim(strip_tags($html))),
            'security' => $security,
        ];
    }
}
