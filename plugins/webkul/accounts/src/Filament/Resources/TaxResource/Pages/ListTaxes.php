<?php

namespace Webkul\Account\Filament\Resources\TaxResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Account\Enums\TaxScope;
use Webkul\Account\Enums\TypeTaxUse;
use Webkul\Account\Filament\Resources\TaxResource;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

class ListTaxes extends ListRecords
{
    use HasTableViews;

    protected static string $resource = TaxResource::class;

    protected function getHeaderActions(): array
    {
        // Tax groups live under Taxes, as in Zoho Books (user, 2026-11-14):
        // the groups screen is reached from here, not from the side menu.
        $groups = str_replace('\\TaxResource', '\\TaxGroupResource', static::getResource());

        return [
            Action::make('taxGroups')
                ->label(fn (): string => $groups::getNavigationLabel())
                ->icon('heroicon-o-rectangle-group')
                ->color('gray')
                ->url(fn (): string => $groups::getUrl('index'))
                ->visible(fn (): bool => class_exists($groups) && $groups::canViewAny()),
            CreateAction::make()
                ->icon('heroicon-o-plus-circle'),
        ];
    }

    public function getPresetTableViews(): array
    {
        return [
            'sale' => PresetView::make('sale')
                ->icon('heroicon-o-scale')
                ->favorite()
                ->label(__('accounts::filament/resources/tax/pages/list-tax.tabs.sale'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type_tax_use', TypeTaxUse::SALE)),
            'purchase' => PresetView::make('purchase')
                ->icon('heroicon-o-currency-dollar')
                ->favorite()
                ->label(__('accounts::filament/resources/tax/pages/list-tax.tabs.purchase'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type_tax_use', TypeTaxUse::PURCHASE)),
            'tax_scope' => PresetView::make('tax_scope')
                ->icon('heroicon-o-magnifying-glass-circle')
                ->favorite()
                ->label(__('accounts::filament/resources/tax/pages/list-tax.tabs.tax-scope'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('tax_scope', TaxScope::SERVICE)),
            'goods' => PresetView::make('goods')
                ->icon('heroicon-o-check')
                ->favorite()
                ->label(__('accounts::filament/resources/tax/pages/list-tax.tabs.goods'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('tax_scope', TaxScope::CONSU)),
            'active' => PresetView::make('Active')
                ->icon('heroicon-o-check-circle')
                ->favorite()
                ->label(__('accounts::filament/resources/tax/pages/list-tax.tabs.active'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_active', true)),
            'in_active' => PresetView::make('In active')
                ->icon('heroicon-o-x-circle')
                ->favorite()
                ->label(__('accounts::filament/resources/tax/pages/list-tax.tabs.in-active'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_active', false)),
        ];
    }
}
