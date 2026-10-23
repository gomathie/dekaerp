<?php

use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Employee\Filament\Resources\EmployeeResource\Pages\ManageResume;
use Webkul\Employee\Models\Employee;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('employees');

    URL::resolveMissingNamedRoutesUsing(fn (): string => '#');
});

it('renders the current resume records page without an attachment model', function () {
    $employee = Employee::factory()->create();

    FilamentHelper::actingAs([
        'view_any_employee_employee',
        'view_employee_employee',
    ]);

    Livewire::test(ManageResume::class, ['record' => $employee->id])
        ->assertOk();
});
