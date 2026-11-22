<?php

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Account\Database\Factories\PaymentMethodFactory;
use Webkul\Account\Filament\Resources\InvoiceResource\Pages\CreateInvoice;
use Webkul\Account\Models\Journal;
use Webkul\Account\Models\PaymentMethodLine;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../Helpers/AccountHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('accounts');

    Package::$plugins = Plugin::all()->keyBy('name');

    URL::resolveMissingNamedRoutesUsing(fn () => '#');
});

function paymentMethodLineIn(Company $company, string $name): PaymentMethodLine
{
    return CompanyHelper::asNobody(function () use ($company, $name): PaymentMethodLine {
        $journal = Journal::factory()->create(['company_id' => $company->getKey(), 'name' => $name.' journal']);

        // Not PaymentMethodLineFactory: its defaults call PaymentMethod::factory(),
        // which the model does not resolve.
        return PaymentMethodLine::query()->create([
            'journal_id'        => $journal->getKey(),
            'payment_method_id' => PaymentMethodFactory::new()->create()->getKey(),
            'name'              => $name,
        ]);
    });
}

/**
 * 2026-11-22, found from Sentry DEKAERP-23: payment method lines have no
 * company scope, so the invoice's "Preferred payment method" offered every
 * client's lines (one journal lookup per line for its label) and the API
 * accepted their ids. Lines are now those of the document's company, with
 * the label's journal and method loaded with the options.
 */
it('offers only the company\'s own payment methods on the invoice form, loaded in one go', function () {
    $own = AccountHelper::company();
    $other = CompanyHelper::asNobody(fn () => Company::factory()->create());

    $ownLine = paymentMethodLineIn($own, 'Own cash');
    $otherLine = paymentMethodLineIn($other, 'Other client cash');

    FilamentHelper::actingAsCompanyUser($own, ['view_any_account_invoice', 'create_account_invoice']);

    $journalLookups = 0;

    DB::listen(function (QueryExecuted $query) use (&$journalLookups): void {
        // The selected sale journal's own label lookup filters by type; a
        // payment method label's lazy journal read does not.
        if (str_contains($query->sql, 'from "accounts_journals" where "accounts_journals"."id" = ?') && ! str_contains($query->sql, '"type"')) {
            $journalLookups++;
        }
    });

    $options = Livewire::test(CreateInvoice::class)
        ->instance()->form->getComponent('preferred_payment_method_line_id')->getOptions();

    expect($options)->toHaveKey($ownLine->getKey())
        ->and($options)->not->toHaveKey($otherLine->getKey())
        ->and($options[$ownLine->getKey()])->toBe('Own cash (Own cash journal)')
        ->and($journalLookups)->toBe(0)
        ->and(PaymentMethodLine::query()->forCompany($own->getKey())->pluck('id')->all())->toContain($ownLine->getKey())->not->toContain($otherLine->getKey())
        // Naming the other company (the API's company_id body field) still
        // finds nothing: the journal's own company scope applies too.
        ->and(PaymentMethodLine::query()->forCompany($other->getKey())->pluck('id')->all())->toBe([]);
});
