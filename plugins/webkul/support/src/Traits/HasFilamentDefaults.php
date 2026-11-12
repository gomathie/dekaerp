<?php

namespace Webkul\Support\Traits;

use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Facades\FilamentView;
use Filament\Tables\Table;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;
use Webkul\Support\Filament\Pages\WhatsNew;

trait HasFilamentDefaults
{
    protected function registerFilamentDefaults(): void
    {
        Fieldset::configureUsing(fn (Fieldset $fieldset) => $fieldset->columnSpanFull());

        Grid::configureUsing(fn (Grid $grid) => $grid->columnSpanFull());

        Section::configureUsing(fn (Section $section) => $section->columnSpanFull());

        Table::configureUsing(fn (Table $table) => $table->defaultCurrency(fn (): string => default_currency_code()));

        Schema::configureUsing(fn (Schema $schema) => $schema->defaultCurrency(fn (): string => default_currency_code()));
    }

    protected function registerHooks(): void
    {
        FilamentView::registerRenderHook(
            PanelsRenderHook::USER_MENU_PROFILE_BEFORE,
            fn (): string => Blade::render(<<<'BLADE'
                <x-filament::dropdown.list>
                    <x-filament::dropdown.list.item tag="a" :href="$whatsNewUrl">
                        <div class="flex items-center gap-2">
                            <img
                                src="{{ url('cache/logo.png') }}"
                                width="24"
                                height="24"
                                alt=""
                            />

                            {{ __('support::support.version', ['version' => $version]) }}
                        </div>
                    </x-filament::dropdown.list.item>
                </x-filament::dropdown.list>
            BLADE, [
                // From config, not a literal here: this said 1.5.0 while
                // production ran 1.7.0 (2026-11-12). Links to the notes.
                'version'     => (string) config('app.version'),
                'whatsNewUrl' => WhatsNew::getUrl(),
            ]),
        );

        // Top-bar shortcut to the release notes (WN-5), with a dot while the
        // newest release is one this user has not opened (D-WN-2).
        FilamentView::registerRenderHook(
            PanelsRenderHook::GLOBAL_SEARCH_AFTER,
            function (): string {
                if (filament()->getCurrentPanel()?->getId() !== 'admin' || ! WhatsNew::canAccess()) {
                    return '';
                }

                return view('support::whats-new-shortcut', [
                    'url'    => WhatsNew::getUrl(),
                    'unseen' => WhatsNew::hasUnseenRelease(),
                ])->render();
            },
        );
    }
}
