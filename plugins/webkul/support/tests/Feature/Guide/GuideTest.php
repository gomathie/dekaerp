<?php

use Livewire\Livewire;
use Webkul\Support\Filament\Pages\Guide;
use Webkul\Support\Services\GuideLibrary;

require_once __DIR__.'/../../Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../Helpers/FilamentHelper.php';

/**
 * The in-app user guide (user, 2026-10-22: "bring the documents directly in
 * app and make it easy to navigate... we will remove the guide from the main
 * website").
 *
 * The content was already shipped in resources/data/guide.json. What was
 * missing was navigation: the Help page rendered all 82 articles as nested
 * accordions in every response. These tests pin the navigation, because that
 * is the part that was built.
 */
beforeEach(function () {
    FilamentHelper::actingAs();
});

it('ships a guide with categories and articles', function () {
    // If the asset were missing or unreadable every assertion below would pass
    // against an empty guide while proving nothing.
    $library = app(GuideLibrary::class);

    expect($library->isEmpty())->toBeFalse()
        ->and($library->index())->not->toBeEmpty()
        ->and($library->articleCount())->toBeGreaterThan(1);
});

it('opens on the first category without an article selected', function () {
    $first = app(GuideLibrary::class)->firstCategoryId();

    Livewire::test(Guide::class)
        ->assertOk()
        ->assertSet('categoryId', $first)
        ->assertSet('articleId', null);
});

it('keeps the sidebar free of article bodies', function () {
    // The whole point of the rewrite: the index must not carry steps, or the
    // entire guide is back in the rendered page.
    $index = app(GuideLibrary::class)->index();

    foreach ($index as $category) {
        expect(array_keys($category))->toBe(['id', 'name', 'icon', 'count'])
            ->and($category)->not->toHaveKey('items');
    }
});

it('shows one article when one is selected', function () {
    $library = app(GuideLibrary::class);
    $categoryId = $library->firstCategoryId();
    $articleId = $library->articleList($categoryId)[0]['id'];

    Livewire::test(Guide::class)
        ->call('selectArticle', $articleId)
        ->assertSet('articleId', $articleId)
        ->assertSet('categoryId', $categoryId);
});

it('resolves the category from a deep-linked article', function () {
    // A bookmarked or shared link carries only the article; the page has to
    // work out which section it belongs to.
    $library = app(GuideLibrary::class);

    $categories = $library->index();
    $lastCategoryId = end($categories)['id'];
    $articleId = $library->articleList($lastCategoryId)[0]['id'];

    Livewire::test(Guide::class, ['articleId' => $articleId])
        ->assertOk()
        ->assertSet('categoryId', $lastCategoryId)
        ->assertSet('articleId', $articleId);
});

it('falls back to browsing when the linked article does not exist', function () {
    // A stale link must not strand the reader on an empty pane.
    Livewire::test(Guide::class, ['articleId' => 'no-such-article-'.uniqid()])
        ->assertOk()
        ->assertSet('articleId', null)
        ->assertSet('categoryId', app(GuideLibrary::class)->firstCategoryId());
});

it('finds an article by its own title', function () {
    $library = app(GuideLibrary::class);
    $article = $library->articleList($library->firstCategoryId())[0];

    $results = $library->search($article['title']);

    expect(array_column($results, 'id'))->toContain($article['id']);
});

it('ignores a search too short to be meaningful', function () {
    // One character would match most of the guide and read as broken.
    expect(app(GuideLibrary::class)->search('a'))->toBe([])
        ->and(app(GuideLibrary::class)->search(''))->toBe([]);
});

it('ranks a title match above a summary match', function () {
    // Typing a feature name should surface the page about it, not every page
    // that mentions it in passing.
    $library = app(GuideLibrary::class);
    $article = $library->articleList($library->firstCategoryId())[0];

    $results = $library->search($article['title']);

    expect($results)->not->toBeEmpty()
        ->and($results[0]['id'])->toBe($article['id']);
});

it('clears the article when switching category, so the pane matches the sidebar', function () {
    $library = app(GuideLibrary::class);
    $categories = $library->index();
    $articleId = $library->articleList($categories[0]['id'])[0]['id'];

    Livewire::test(Guide::class)
        ->call('selectArticle', $articleId)
        ->assertSet('articleId', $articleId)
        ->call('selectCategory', $categories[1]['id'])
        ->assertSet('articleId', null)
        ->assertSet('categoryId', $categories[1]['id']);
});

it('hides itself from navigation when no guide is shipped', function () {
    // Better than offering a page that opens empty.
    expect(Guide::shouldRegisterNavigation())->toBeTrue();

    app()->instance(GuideLibrary::class, new class extends GuideLibrary
    {
        public function categories(): array
        {
            return [];
        }
    });

    expect(Guide::shouldRegisterNavigation())->toBeFalse();
});
