<?php

namespace Webkul\TimeOff\Filament\Clusters\MyTime\Resources\MyTimeOffResource\Tables;

use Filament\Tables\Table;
use Webkul\TimeOff\Filament\Clusters\Management\Resources\TimeOffResource;

class MyTimeOffsTable
{
    public static function configure(Table $table): Table
    {
        return $table = TimeOffResource::table($table);
    }
}
