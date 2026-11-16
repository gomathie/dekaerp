<?php

namespace Webkul\Support\Traits;

use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Facades\FilamentView;
use Filament\Tables\Table;
use Filament\View\PanelsRenderHook;
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
        // What's New and the version, in the profile menu (WN-5; moved out of
        // the top bar, user 2026-11-12), with a dot while the newest release
        // is one this user has not opened (D-WN-2).
        FilamentView::registerRenderHook(
            PanelsRenderHook::USER_MENU_PROFILE_BEFORE,
            function (): string {
                if (filament()->getCurrentPanel()?->getId() !== 'admin' || ! WhatsNew::canAccess()) {
                    return '';
                }

                return view('support::whats-new-shortcut', [
                    'url'     => WhatsNew::getUrl(),
                    'unseen'  => WhatsNew::hasUnseenRelease(),
                    // From config, not a literal: this said 1.5.0 while
                    // production ran 1.7.0 (2026-11-12).
                    'version' => (string) config('app.version'),
                ])->render();
            },
        );
    }
}
