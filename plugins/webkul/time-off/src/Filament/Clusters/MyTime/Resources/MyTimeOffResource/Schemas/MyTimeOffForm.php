<?php

namespace Webkul\TimeOff\Filament\Clusters\MyTime\Resources\MyTimeOffResource\Schemas;

use Filament\Schemas\Schema;
use Webkul\TimeOff\Filament\Clusters\MyTime\Resources\MyTimeOffResource;

class MyTimeOffForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components((new MyTimeOffResource)->getFormSchema());
    }
}
