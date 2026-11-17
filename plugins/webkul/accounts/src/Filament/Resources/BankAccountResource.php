<?php

namespace Webkul\Account\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Account\Filament\Resources\BankAccountResource\Pages\ListBankAccounts;
use Webkul\Account\Filament\Resources\BankAccountResource\Schemas\BankAccountForm;
use Webkul\Account\Filament\Resources\BankAccountResource\Tables\BankAccountsTable;
use Webkul\Partner\Filament\Resources\BankAccountResource as BaseBankAccountResource;

class BankAccountResource extends BaseBankAccountResource
{
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return BankAccountForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BankAccountsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBankAccounts::route('/'),
        ];
    }
}
