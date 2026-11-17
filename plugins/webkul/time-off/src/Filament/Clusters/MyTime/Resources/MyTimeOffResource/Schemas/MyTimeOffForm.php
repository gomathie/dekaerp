<?php

namespace Webkul\TimeOff\Filament\Clusters\MyTime\Resources\MyTimeOffResource\Schemas;

use Filament\Schemas\Schema;

class MyTimeOffForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components((new self)->getFormSchema());
    }
}
