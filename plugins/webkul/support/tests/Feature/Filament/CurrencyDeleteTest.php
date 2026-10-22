<?php

use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Testing\TestAction;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Event;
use Livewire\Livewire;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\Support\Filament\Resources\CurrencyResource\Pages\ListCurrencies;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;

use function Pest\Laravel\assertDatabaseHas;

require_once __DIR__.'/../../Helpers/FilamentHelper.php';
require_once __DIR__.'/../../Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensureERPInstalled();

    Package::$plugins = Plugin::all()->keyBy('name');

    FilamentHelper::actingAs(['view_any_support_currency', 'delete_any_support_currency']);
});

it('reports a bulk currency delete query failure instead of throwing', function () {
    $currency = Currency::factory()->create(['active' => true]);
    $company = Company::query()->firstOrFail();
    $company->update(['currency_id' => $currency->id]);

    expect($currency->fresh()->isInUse())->toBeTrue();

    Event::listen('eloquent.deleting: '.Currency::class, function (): never {
        throw new QueryException('pgsql', 'delete from currencies', [], new RuntimeException('currency is in use'));
    });

    Livewire::test(ListCurrencies::class)
        ->selectTableRecords([$currency->id])
        ->callAction(TestAction::make(DeleteBulkAction::class)->table()->bulk())
        ->assertNotified();

    assertDatabaseHas('currencies', ['id' => $currency->id]);
    assertDatabaseHas('companies', ['id' => $company->id, 'currency_id' => $currency->id]);
});
