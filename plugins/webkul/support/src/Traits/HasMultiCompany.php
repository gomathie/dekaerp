<?php

namespace Webkul\Support\Traits;

use Filament\Facades\Filament;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Livewire\Livewire;
use Webkul\Support\Livewire\CompanySwitcher;
use Webkul\Support\Models\Scopes\CompanyScope;

trait HasMultiCompany
{
    protected function registerCompanySwitcher(): void
    {
        Livewire::component('company-switcher', CompanySwitcher::class);

        FilamentView::registerRenderHook(
            PanelsRenderHook::USER_MENU_BEFORE,
            function (): string {
                if (! Auth::check()) {
                    return '';
                }

                if (Filament::getCurrentPanel()?->getId() !== 'admin') {
                    return '';
                }

                return Blade::render("@livewire('company-switcher')");
            },
        );
    }

    protected function registerCompanyScopes(): void
    {
        foreach (config('company-scope.models', []) as $modelClass) {
            if (! class_exists($modelClass)) {
                continue;
            }

            CompanyScope::applyTo($modelClass);
        }
    }
}
