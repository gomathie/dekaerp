<?php

namespace Webkul\Account\Filament\Resources\BankAccountResource\Schemas;

use Filament\Schemas\Schema;
use Webkul\Partner\Filament\Resources\BankAccountResource as BaseBankAccountResource;

class BankAccountForm
{
    public static function configure(Schema $schema): Schema
    {
        $schema = BaseBankAccountResource::form($schema);

        $components = collect($schema->getComponents())->forget(1)->all();

        $schema->components($components);

        return $schema;
    }
}
