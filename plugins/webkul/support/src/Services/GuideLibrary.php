<?php

namespace Webkul\Support\Services;

use Illuminate\Support\Str;
use Webkul\Support\Enums\GuideAudience;

/**
 * Reads the in-app user guide.
 *
 * The content lives in resources/data/guide.json - 18 categories and 82
 * articles, each with its own steps and instructions. It is a repo asset, not
 * user input, which is why the views are allowed to render its htmlContent and
 * instructions unescaped.
 *
 * The file is around 620KB, so it is decoded once per request and the lookups
 * below work off that single decode. The Help page used to render every article
 * on one page, which meant the whole guide became HTML on every visit; this
 * exists so a page can ask for one article instead.
 *
 * Bound as a scoped singleton in SupportServiceProvider.
 */
class GuideLibrary
{
    /**
     * Content files, merged in this order.
     *
     * guide.json is imported reference material for end users and is large and
     * machine-shaped. The administrative guides are written by hand, so they
     * live in their own files: a hand edit never has to find its place inside
     * 2,600 lines of generated HTML, and a re-import of the reference content
     * cannot silently drop them.
     */
    protected const SOURCE_FILES = [
        'guide.json',
        'guide-admin.json',
        'guide-superadmin.json',
    ];

    /**
     * @var array<int, array<string, mixed>>|null
     */
    protected ?array $categories = null;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function categories(): array
    {
        if ($this->categories !== null) {
            return $this->categories;
        }

        $categories = [];

        foreach (static::SOURCE_FILES as $file) {
            $path = dirname(__DIR__, 2).'/resources/data/'.$file;

            if (! file_exists($path)) {
                continue;
            }

            $decoded = json_decode((string) file_get_contents($path), true);

            if (is_array($decoded)) {
                $categories = array_merge($categories, $decoded);
            }
        }

        return $this->categories = $categories;
    }

    /**
     * The categories the signed-in user is allowed to read.
     *
     * Every public accessor below goes through this rather than categories(),
     * so the audience gate holds by default instead of holding only where a
     * caller remembered to ask for it. The guide has no policy and no API of
     * its own - this method is the whole boundary - so a new accessor that
     * reached for categories() directly would quietly serve administrative
     * pages to everyone.
     *
     * @return array<int, array<string, mixed>>
     */
    public function visible(?GuideAudience $only = null): array
    {
        $permitted = GuideAudience::visibleTo(auth()->user());

        return array_values(array_filter($this->categories(), function (array $category) use ($permitted, $only): bool {
            $audience = $this->audienceOf($category);

            if (! in_array($audience, $permitted, true)) {
                return false;
            }

            return $only === null || $audience === $only;
        }));
    }

    public function audienceOf(array $category): GuideAudience
    {
        return GuideAudience::tryFromValue($category['audience'] ?? null) ?? GuideAudience::default();
    }

    /**
     * The audiences this user may switch between, narrowest first.
     *
     * An audience with no categories is left out, so the page never offers a
     * tab that opens onto nothing.
     *
     * @return array<int, GuideAudience>
     */
    public function audiences(): array
    {
        return array_values(array_filter(
            GuideAudience::visibleTo(auth()->user()),
            fn (GuideAudience $audience): bool => $this->visible($audience) !== [],
        ));
    }

    public function isEmpty(): bool
    {
        return $this->visible() === [];
    }

    public function articleCount(?GuideAudience $only = null): int
    {
        return array_reduce(
            $this->visible($only),
            fn (int $total, array $category): int => $total + count($category['items'] ?? []),
            0,
        );
    }

    /**
     * Categories without their articles' bodies.
     *
     * The sidebar needs names, icons and counts only. Handing it the full
     * articles would put the entire guide back into the rendered page, which is
     * the thing this class exists to avoid.
     *
     * @return array<int, array{id: string, name: string, icon: ?string, count: int}>
     */
    public function index(?GuideAudience $only = null): array
    {
        return array_map(fn (array $category): array => [
            'id'    => (string) ($category['id'] ?? Str::slug($category['name'] ?? '')),
            'name'  => (string) ($category['name'] ?? ''),
            'icon'  => $category['icon'] ?? null,
            'count' => count($category['items'] ?? []),
        ], $this->visible($only));
    }

    /**
     * @return array<string, mixed>|null
     */
    public function category(?string $categoryId): ?array
    {
        if (blank($categoryId)) {
            return null;
        }

        foreach ($this->visible() as $category) {
            if ((string) ($category['id'] ?? '') === $categoryId) {
                return $category;
            }
        }

        return null;
    }

    /**
     * Article titles and summaries for one category, without their steps.
     *
     * @return array<int, array{id: string, title: string, summary: ?string, badge: ?string, readTime: ?string}>
     */
    public function articleList(?string $categoryId): array
    {
        $category = $this->category($categoryId);

        if (! $category) {
            return [];
        }

        return array_map(fn (array $item): array => [
            'id'       => (string) ($item['id'] ?? ''),
            'title'    => (string) ($item['title'] ?? ''),
            'summary'  => $item['summary'] ?? null,
            'badge'    => $item['badge'] ?? null,
            'readTime' => $item['readTime'] ?? null,
        ], $category['items'] ?? []);
    }

    /**
     * One article, with the category it belongs to.
     *
     * @return array{article: array<string, mixed>, category: array<string, mixed>}|null
     */
    public function article(?string $articleId): ?array
    {
        if (blank($articleId)) {
            return null;
        }

        foreach ($this->visible() as $category) {
            foreach ($category['items'] ?? [] as $item) {
                if ((string) ($item['id'] ?? '') === $articleId) {
                    return ['article' => $item, 'category' => $category];
                }
            }
        }

        return null;
    }

    /**
     * Title, summary and category-name matches, most relevant first.
     *
     * Titles rank above summaries so typing a feature name finds the page about
     * it rather than every page that mentions it in passing. Step bodies are
     * deliberately not searched: matching them would return an article without
     * showing why it matched, which reads as a wrong result.
     *
     * @return array<int, array{id: string, title: string, summary: ?string, category: string, categoryId: string}>
     */
    public function search(?string $term, int $limit = 40): array
    {
        $term = trim((string) $term);

        if (mb_strlen($term) < 2) {
            return [];
        }

        $needle = mb_strtolower($term);

        $titleHits = [];
        $otherHits = [];

        // Searches every audience the viewer may read, not just the open tab:
        // an administrator looking for "permissions" should find the admin page
        // without first guessing which guide it lives in.
        foreach ($this->visible() as $category) {
            $categoryName = (string) ($category['name'] ?? '');

            foreach ($category['items'] ?? [] as $item) {
                $title = (string) ($item['title'] ?? '');
                $summary = (string) ($item['summary'] ?? '');

                $hit = [
                    'id'         => (string) ($item['id'] ?? ''),
                    'title'      => $title,
                    'summary'    => $item['summary'] ?? null,
                    'category'   => $categoryName,
                    'categoryId' => (string) ($category['id'] ?? ''),
                ];

                if (str_contains(mb_strtolower($title), $needle)) {
                    $titleHits[] = $hit;

                    continue;
                }

                if (str_contains(mb_strtolower($summary), $needle)
                    || str_contains(mb_strtolower($categoryName), $needle)) {
                    $otherHits[] = $hit;
                }
            }
        }

        return array_slice(array_merge($titleHits, $otherHits), 0, $limit);
    }

    /**
     * The first category's id, used when the page opens with nothing selected.
     */
    public function firstCategoryId(?GuideAudience $only = null): ?string
    {
        $index = $this->index($only);

        return $index === [] ? null : $index[0]['id'];
    }
}
