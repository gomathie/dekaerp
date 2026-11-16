<?php

namespace Webkul\Account\Filament\Resources\TaxGroupResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Webkul\Account\Filament\Resources\TaxGroupResource;

class ListTaxGroups extends ListRecords
{
    protected static string $resource = TaxGroupResource::class;

    protected function getHeaderActions(): array
    {
        // Back to Taxes: groups are reached from there (user, 2026-11-14).
        $taxes = str_replace('\\TaxGroupResource', '\\TaxResource', static::getResource());

        return [
            Action::make('taxes')
                ->label(fn (): string => $taxes::getNavigationLabel())
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(fn (): string => $taxes::getUrl('index'))
                ->visible(fn (): bool => class_exists($taxes) && $taxes::canViewAny()),
            CreateAction::make()
                ->icon('heroicon-o-plus-circle'),
        ];
    }
}
