<?php

namespace Webkul\Recruitment\Filament\Clusters\Configurations\Resources\DepartmentResource\Tables;

use Filament\Tables\Table;
use Webkul\Employee\Filament\Resources\DepartmentResource as BaseDepartmentResource;

class DepartmentsTable
{
    public static function configure(Table $table): Table
    {
        return BaseDepartmentResource::table($table)
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ]);
    }
}
