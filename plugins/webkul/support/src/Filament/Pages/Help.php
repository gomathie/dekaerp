<?php

namespace Webkul\Support\Filament\Pages;

use Filament\Infolists\Components\ViewEntry;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Webkul\Support\Enums\NavigationGroup;

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
        return $this->hasServices() || $this->hasResources() || filled($this->contactUrl());
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
