<?php

namespace Webkul\Support\Filament\Pages;

use Filament\Infolists\Components\ViewEntry;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Webkul\Support\Enums\GuideAudience;
use Webkul\Support\Enums\NavigationGroup;
use Webkul\Support\Services\GuideLibrary;

class Help extends Page
{
    protected string $view = 'support::pages.help';

    protected static ?string $slug = 'help';

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('support::filament/pages/help.navigation.label');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Help;
    }

    public function getTitle(): string
    {
        return __('support::filament/pages/help.title');
    }

    public function getHeading(): string
    {
        return __('support::filament/pages/help.heading');
    }

    public function getSubheading(): ?string
    {
        return __('support::filament/pages/help.subheading', ['app' => $this->appName()]);
    }

    /**
     * The product name shown to users.
     *
     * The strings said "AureusERP" in every locale, which is the upstream
     * project rather than this product. Reading APP_NAME keeps them correct
     * without a second place to rename.
     */
    protected function appName(): string
    {
        return (string) config('app.name', 'ERP');
    }

    public function servicesInfolist(Schema $schema): Schema
    {
        return $schema->components([
            $this->cardsGrid($this->services()),
        ]);
    }

    public function resourcesInfolist(Schema $schema): Schema
    {
        return $schema->components([
            $this->cardsGrid($this->resources()),
        ]);
    }

    /**
     * @return array<int, array{
     *     icon: string,
     *     title: string,
     *     description: string,
     *     button: string,
     *     sections: array<int, array{title: string, items: array<int, string>}>
     * }>
     */
    /**
     * One card per guide the viewer may read.
     *
     * Driven by the audiences GuideLibrary reports rather than by a hardcoded
     * list, so a card appears only when that guide has content and the viewer
     * is entitled to it. Each card links into the Guide page on its own tab;
     * before this, the admin card linked nowhere at all and its only content
     * was a static checklist in the translation files.
     *
     * @return array<int, array<string, mixed>>
     */
    public function guides(): array
    {
        $library = app(GuideLibrary::class);

        $guides = [];

        foreach ($library->audiences() as $audience) {
            $key = $audience->value;

            $guides[] = [
                'icon'        => $audience->getIcon(),
                'title'       => $audience->getLabel(),
                'description' => __("support::filament/pages/help.guides.{$key}.description", ['app' => $this->appName()]),
                'button'      => __("support::filament/pages/help.guides.{$key}.button"),
                'url'         => Guide::getUrl(['audience' => $key]),
                'count'       => $library->articleCount($audience),
                // The admin checklist predates the admin guide and is still the
                // best short answer to "what do I set up first", so it is kept
                // where the translations already provide one.
                'sections'    => $this->guideSections($key),
            ];
        }

        return $guides;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function guideCategories(): array
    {
        // Was a second copy of GuideLibrary's file read and decode. Two readers
        // of the same 620KB asset meant the Help page silently missed anything
        // the library learned to do - the audience filter among it.
        return app(GuideLibrary::class)->visible();
    }

    public function guidePageCount(): int
    {
        return app(GuideLibrary::class)->articleCount();
    }

    /**
     * Whether the viewer may read the administrator guide.
     *
     * Was super-admin-only, which hid the admin guide from the very people it
     * is written for: Role::COMPANY_ADMIN, a customer's own administrator.
     */
    public function canViewAdminGuide(): bool
    {
        return GuideAudience::allowsUser(auth()->user(), GuideAudience::Admin);
    }

    protected function guideSections(string $guide): array
    {
        $sections = trans("support::filament/pages/help.guides.{$guide}.sections");

        return is_array($sections) ? $sections : [];
    }

    protected function cardsGrid(array $cards): Grid
    {
        return Grid::make([
            'default' => 1,
            'md'      => 2,
            'xl'      => 3,
        ])->schema(
            array_map(
                fn (array $card, int $index): ViewEntry => ViewEntry::make("card_{$index}")
                    ->hiddenLabel()
                    ->view('support::pages.partials.help-card', ['card' => $card]),
                $cards,
                array_keys($cards),
            )
        );
    }

    /**
     * Whether either card section has anything to show.
     *
     * The view uses this to drop the section headings too - a bare "Services"
     * label above nothing looks broken.
     */
    public function hasServices(): bool
    {
        return $this->services() !== [];
    }

    public function hasGuides(): bool
    {
        return $this->guides() !== [];
    }

    public function hasUserGuide(): bool
    {
        return $this->guideCategories() !== [];
    }

    public function hasResources(): bool
    {
        return $this->resources() !== [];
    }

    public function contactUrl(): ?string
    {
        return config('deka.help.contact_url');
    }

    /**
     * Whether the page has anything at all to show.
     *
     * Every card is config-driven, so a deployment that configures none of
     * them would otherwise render an empty shell under a "Help & Resources"
     * heading - which reads as a broken page rather than an unconfigured one.
     */
    public function hasAnyContent(): bool
    {
        return $this->hasGuides() || $this->hasServices() || $this->hasResources() || filled($this->contactUrl());
    }

    protected function services(): array
    {
        return $this->withConfiguredUrls('services', [
            'cloud' => [
                'icon'        => 'heroicon-o-cloud',
                'title'       => __('support::filament/pages/help.services.cloud.title'),
                'description' => __('support::filament/pages/help.services.cloud.description'),
                'button'      => __('support::filament/pages/help.services.cloud.button'),
            ],
            'support' => [
                'icon'        => 'heroicon-o-lifebuoy',
                'title'       => __('support::filament/pages/help.services.support.title'),
                'description' => __('support::filament/pages/help.services.support.description'),
                'button'      => __('support::filament/pages/help.services.support.button'),
            ],
            'paid' => [
                'icon'        => 'heroicon-o-key',
                'title'       => __('support::filament/pages/help.services.paid.title'),
                'description' => __('support::filament/pages/help.services.paid.description'),
                'button'      => __('support::filament/pages/help.services.paid.button'),
            ],
        ]);
    }

    protected function resources(): array
    {
        return $this->withConfiguredUrls('resources', [
            'extensions' => [
                'icon'        => 'heroicon-o-puzzle-piece',
                'title'       => __('support::filament/pages/help.resources.extensions.title'),
                'description' => __('support::filament/pages/help.resources.extensions.description', ['app' => $this->appName()]),
                'button'      => __('support::filament/pages/help.resources.extensions.button'),
            ],
            'docs' => [
                'icon'        => 'heroicon-o-document-text',
                'title'       => __('support::filament/pages/help.resources.docs.title'),
                'description' => __('support::filament/pages/help.resources.docs.description'),
                'button'      => __('support::filament/pages/help.resources.docs.button'),
            ],
            'guide' => [
                'icon'        => 'heroicon-o-book-open',
                'title'       => __('support::filament/pages/help.resources.guide.title'),
                'description' => __('support::filament/pages/help.resources.guide.description'),
                'button'      => __('support::filament/pages/help.resources.guide.button'),
            ],
            'website' => [
                'icon'        => 'heroicon-o-globe-alt',
                'title'       => __('support::filament/pages/help.resources.website.title', ['app' => $this->appName()]),
                'description' => __('support::filament/pages/help.resources.website.description', ['app' => $this->appName()]),
                'button'      => __('support::filament/pages/help.resources.website.button'),
            ],
        ]);
    }

    /**
     * Attach configured URLs and drop every card that has none.
     *
     * Upstream hardcodes these to aureuserp.com and store.webkul.com. They are
     * config-driven here so this product does not send its own customers to a
     * different vendor, and so cards can be switched on individually as the
     * marketing pages ship - see config/deka.php.
     */
    protected function withConfiguredUrls(string $group, array $cards): array
    {
        $urls = config("deka.help.{$group}", []);

        $configured = [];

        foreach ($cards as $key => $card) {
            $url = $urls[$key] ?? null;

            if (blank($url)) {
                continue;
            }

            $configured[] = $card + ['url' => $url];
        }

        return $configured;
    }
}
