<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;
use Webkul\Security\Filament\Resources\CompanyResource\Pages\CreateCompany;
use Webkul\Security\Filament\Resources\CompanyResource\Pages\EditCompany;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Country;
use Webkul\Support\Models\Currency;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensureERPInstalled();
    FilamentHelper::bootAdminPanel();

    $actor = User::query()->where('email', 'admin@example.com')->firstOrFail();
    Auth::shouldUse('web');
    Auth::guard('web')->login($actor);
});

/**
 * 2.1 plan CR-4, D-CR-1 (2026-11-12): on the new-company form, choosing a
 * country fills the default currency from that country. On edit it does not:
 * changing an existing company's base currency is a deliberate, warned action
 * (D-CR-4), never a side effect of correcting its address.
 */
function countryWithCurrency(): array
{
    $currency = Currency::query()->where('active', true)->orderByDesc('id')->firstOrFail();
    $country = Country::query()->firstOrFail();
    $country->forceFill(['currency_id' => $currency->id])->save();

    return [$country, $currency];
}

it('fills the default currency from the country on a new company', function (): void {
    [$country, $currency] = countryWithCurrency();

    Livewire::test(CreateCompany::class)
        ->fillForm(['currency_id' => null])
        ->fillForm(['country_id' => $country->id])
        ->assertSchemaStateSet(['currency_id' => $currency->id]);
});

it('leaves an existing company currency alone when its country changes', function (): void {
    [$country, $currency] = countryWithCurrency();
    $other = Currency::query()->whereKeyNot($currency->id)->firstOrFail();
    $company = Company::factory()->create(['currency_id' => $other->id]);

    Livewire::test(EditCompany::class, ['record' => $company->getRouteKey()])
        ->fillForm(['country_id' => $country->id])
        ->assertSchemaStateSet(['currency_id' => $other->id]);
});
