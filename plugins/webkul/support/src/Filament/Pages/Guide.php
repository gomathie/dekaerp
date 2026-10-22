<?php

namespace Webkul\Support\Filament\Pages;

use Filament\Pages\Page;
use Livewire\Attributes\Url;
use Webkul\Support\Enums\NavigationGroup;
use Webkul\Support\Services\GuideLibrary;

/**
 * The user guide, browsable inside the app.
 *
 * The content was already here - 82 articles in resources/data/guide.json - but
 * the Help page rendered all of it as nested accordions on a single page. That
 * put the whole guide into every response and left navigation to scrolling, so
 * the one thing it was missing was a way to find anything (user, 2026-10-22:
 * "make it easy to navigate").
 *
 * This renders one article at a time, with the category list as navigation and
 * a search across titles and summaries. The three pieces of state are in the
 * URL, so a page can be linked to, bookmarked, and reached with the browser's
 * back button.
 *
 * No permission of its own: this is product documentation, and every
 * authenticated user needs it. The admin-only guide stays on the Help page.
 */
class Guide extends Page
{
    protected string $view = 'support::pages.guide';

    protected static ?string $slug = 'guide';

    protected static ?int $navigationSort = 2;

    #[Url(as: 'category', keep: true)]
    public ?string $categoryId = null;

    #[Url(as: 'article', keep: true)]
    public ?string $articleId = null;

    #[Url(as: 'q')]
    public string $search = '';

    public static function getNavigationLabel(): string
    {
        return __('support::filament/pages/guide.navigation.label');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Help;
    }

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-book-open';
    }

    /**
     * Hidden when there is no guide shipped, rather than offering an empty page.
     */
    public static function shouldRegisterNavigation(): bool
    {
        return ! app(GuideLibrary::class)->isEmpty();
    }

    public function getTitle(): string
    {
        return __('support::filament/pages/guide.title');
    }

    public function getHeading(): string
    {
        return __('support::filament/pages/guide.heading');
    }

    public function getSubheading(): ?string
    {
        return trans_choice('support::filament/pages/guide.subheading', $this->library()->articleCount(), [
            'count' => $this->library()->articleCount(),
        ]);
    }

    public function mount(): void
    {
        // An article in the URL decides the category, so a deep link opens with
        // the right section expanded even when no category was passed.
        if (filled($this->articleId)) {
            $found = $this->library()->article($this->articleId);

            if ($found) {
                $this->categoryId = (string) ($found['category']['id'] ?? $this->categoryId);

                return;
            }

            // A stale or mistyped article id falls back to browsing rather than
            // showing an empty pane with no way out.
            $this->articleId = null;
        }

        $this->categoryId ??= $this->library()->firstCategoryId();
    }

    public function selectCategory(string $categoryId): void
    {
        $this->categoryId = $categoryId;
        $this->articleId = null;
        $this->search = '';
    }

    public function selectArticle(string $articleId): void
    {
        $found = $this->library()->article($articleId);

        if (! $found) {
            return;
        }

        $this->articleId = $articleId;
        $this->categoryId = (string) ($found['category']['id'] ?? $this->categoryId);
        $this->search = '';
    }

    public function clearArticle(): void
    {
        $this->articleId = null;
    }

    public function clearSearch(): void
    {
        $this->search = '';
    }

    /**
     * @return array<int, array{id: string, name: string, icon: ?string, count: int}>
     */
    public function categories(): array
    {
        return $this->library()->index();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function searchResults(): array
    {
        return $this->library()->search($this->search);
    }

    public function isSearching(): bool
    {
        return mb_strlen(trim($this->search)) >= 2;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function articleList(): array
    {
        return $this->library()->articleList($this->categoryId);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function article(): ?array
    {
        $found = $this->library()->article($this->articleId);

        return $found['article'] ?? null;
    }

    public function categoryName(): ?string
    {
        $category = $this->library()->category($this->categoryId);

        return $category['name'] ?? null;
    }

    protected function library(): GuideLibrary
    {
        return app(GuideLibrary::class);
    }
}
