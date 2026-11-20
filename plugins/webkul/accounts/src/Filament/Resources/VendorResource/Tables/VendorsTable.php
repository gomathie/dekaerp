<?php

namespace Webkul\Account\Filament\Resources\VendorResource\Tables;

use Filament\Tables\Table;
use Webkul\Account\Filament\Resources\PartnerResource;

class VendorsTable
{
    public static function configure(Table $table): Table
    {
        $table = PartnerResource::table($table);

        $table->modifyQueryUsing(fn ($query) => $query->where('supplier_rank', '>', 0));

        return $table;
    }
}
