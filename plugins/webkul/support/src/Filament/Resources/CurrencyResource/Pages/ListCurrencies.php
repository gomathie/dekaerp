<?php

namespace Webkul\Support\Filament\Resources\CurrencyResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Webkul\Support\Filament\Resources\CurrencyResource;
use Webkul\Support\Models\Currency;

class ListCurrencies extends ListRecords
{
    protected static string $resource = CurrencyResource::class;

    /**
     * Where the daily rates come from (user, 2026-11-22). Also the
     * attribution the free source's terms ask for.
     */
    public function getSubheading(): ?string
    {
        return config('exchange-rates.enabled')
            ? __('support::filament/resources/currency/pages/list-currency.rates-source')
            : null;
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('support::filament/resources/currency/pages/list-currency.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('support::filament/resources/currency/pages/list-currency.tabs.all'))
                ->badge(Currency::count()),
            'active' => Tab::make(__('support::filament/resources/currency/pages/list-currency.tabs.active'))
                ->badge(Currency::active()->count())
                ->modifyQueryUsing(function ($query) {
                    return $query->active();
                }),
            'inactive' => Tab::make(__('support::filament/resources/currency/pages/list-currency.tabs.inactive'))
                ->badge(Currency::where('active', false)->count())
                ->modifyQueryUsing(function ($query) {
                    return $query->where('active', false);
                }),
        ];
    }
}
