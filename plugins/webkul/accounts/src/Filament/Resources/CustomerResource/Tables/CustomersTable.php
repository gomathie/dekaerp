<?php

namespace Webkul\Account\Filament\Resources\CustomerResource\Tables;

use Filament\Tables\Table;
use Webkul\Account\Filament\Resources\PartnerResource;

class CustomersTable
{
    public static function configure(Table $table): Table
    {
        $table = PartnerResource::table($table);

        $table->modifyQueryUsing(fn ($query) => $query->where('customer_rank', '>', 0));

        return $table;
    }
}
