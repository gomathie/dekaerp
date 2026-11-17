<?php

namespace Webkul\Account\Filament\Resources\BankAccountResource\Tables;

use Filament\Tables\Table;
use Webkul\Partner\Filament\Resources\BankAccountResource as BaseBankAccountResource;

class BankAccountsTable
{
    public static function configure(Table $table): Table
    {
        $table = BaseBankAccountResource::table($table);

        $components = collect($table->getColumns())->forget('can_send_money')->all();

        $table->columns($components);

        return $table;
    }
}
