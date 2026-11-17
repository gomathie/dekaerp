<?php

namespace Webkul\Logistics\Filament\Clusters\Configurations\Resources\ExpenseCategoryResource\Schemas;

use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Webkul\Logistics\Filament\Clusters\Configurations\Resources\ExpenseCategoryResource;

class ExpenseCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                ExpenseCategoryResource::nameField(),
                ExpenseCategoryResource::codeField(),
                Toggle::make('requires_receipt')
                    ->label(__(ExpenseCategoryResource::$lang.'.form.fields.requires-receipt')),
                Toggle::make('is_subcontracting')
                    ->label(__(ExpenseCategoryResource::$lang.'.form.fields.is-subcontracting'))
                    ->helperText(__(ExpenseCategoryResource::$lang.'.form.fields.is-subcontracting-helper')),
                ExpenseCategoryResource::companyField(),
                ExpenseCategoryResource::activeField(),
            ])
            ->columns(1);
    }
}
