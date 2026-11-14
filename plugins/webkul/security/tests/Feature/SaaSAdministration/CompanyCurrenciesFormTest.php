<?php

use Livewire\Livewire;
use Webkul\Security\Filament\Resources\CompanyResource\Pages\EditCompany;
use Webkul\Support\Models\Currency;

require_once __DIR__.'/../Authorization/MultiCompanyAdminTest.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';

/**
 * Company currencies on the company form (CR-6, 2026-11-13): installation
 * staff set the list from the company's edit page, and it is stored on the
 * company, not on whoever saved it.
 */
it('saves the permitted currencies a Super Admin picks on the company form', function () {
    FilamentHelper::bootAdminPanel();

    $superAdmin = multiCompanyAdminTestSuperAdmin();
    $company = multiCompanyAdminTestCompany();
    // Fixed numbers: the factory's phone format sometimes fails the form's tel() rule.
    $company->forceFill(['phone' => '+260971234567', 'mobile' => '+260961234567'])->save();
    multiCompanyAdminTestAuthenticate($superAdmin, [(int) $company->getKey()]);

    $extra = Currency::factory()->create(['name' => 'ZZD', 'active' => true]);

    Livewire::test(EditCompany::class, ['record' => $company->getRouteKey()])
        ->fillForm(['permittedCurrencies' => [$extra->getKey()]])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($company->fresh()->permittedCurrencies()->pluck('currencies.id')->map(fn ($id): int => (int) $id)->all())
        ->toBe([(int) $extra->getKey()]);
});
