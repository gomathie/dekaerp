<?php

use Illuminate\Support\Facades\DB;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Models\Journal;
use Webkul\Account\Models\Partner;
use Webkul\Account\Support\DocumentCurrency;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;
use Webkul\Support\Support\CompanyCurrencyEquivalent;

require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('accounts');
});

function documentCurrency(string $code): Currency
{
    // Seeded currencies are mostly inactive, and the rule skips an inactive
    // one: switch it on, as a company using it would.
    $currency = Currency::query()->firstOrCreate(['name' => $code], ['symbol' => $code, 'active' => true]);
    $currency->update(['active' => true]);

    return $currency;
}

/**
 * Gives the company one journal of the type, in the currency given (null:
 * no currency of its own), replacing whatever automatic setup made.
 */
function journalInCurrency(Company $company, JournalType $type, ?Currency $currency): void
{
    Journal::withoutGlobalScopes()->where('company_id', $company->getKey())->where('type', $type)->update(['currency_id' => $currency?->getKey()]);

    if (! Journal::withoutGlobalScopes()->where('company_id', $company->getKey())->where('type', $type)->exists()) {
        Journal::factory()->create(['company_id' => $company->getKey(), 'type' => $type, 'currency_id' => $currency?->getKey()]);
    }
}

/**
 * TRACE365, user 2026-11-23: books in cedis, suppliers in dollars, some
 * customers in dollars. A document opens in the contact's own currency,
 * else the currency of the company's journal for it, else the company's,
 * and a currency the company may not use is never offered.
 */
it('opens a document in the contact currency, else the journal currency, else the company currency', function () {
    $ghs = documentCurrency('GHS');
    $usd = documentCurrency('USD');
    $eur = documentCurrency('EUR');

    [$company, $other] = CompanyHelper::asNobody(fn () => [
        Company::factory()->create(['currency_id' => $ghs->getKey()]),
        Company::factory()->create(['currency_id' => $ghs->getKey()]),
    ]);

    journalInCurrency($company, JournalType::PURCHASE, $usd);
    journalInCurrency($company, JournalType::SALE, null);

    [$euroSupplier, $plainCustomer, $outsider] = CompanyHelper::asNobody(fn () => [
        Partner::factory()->create(['company_id' => $company->getKey(), 'default_currency_id' => $eur->getKey()]),
        Partner::factory()->create(['company_id' => $company->getKey()]),
        Partner::factory()->create(['company_id' => $other->getKey(), 'default_currency_id' => $eur->getKey()]),
    ]);

    $companyId = $company->getKey();

    expect(DocumentCurrency::for(null, JournalType::PURCHASE, $companyId))->toBe($usd->getKey())
        ->and(DocumentCurrency::for(null, JournalType::SALE, $companyId))->toBe($ghs->getKey())
        ->and(DocumentCurrency::for($euroSupplier->getKey(), JournalType::PURCHASE, $companyId))->toBe($eur->getKey())
        ->and(DocumentCurrency::for($plainCustomer->getKey(), JournalType::SALE, $companyId))->toBe($ghs->getKey())
        // Changing the contact only switches when the contact has a currency.
        ->and(DocumentCurrency::ofContact($euroSupplier->getKey(), $companyId))->toBe($eur->getKey())
        ->and(DocumentCurrency::ofContact($plainCustomer->getKey(), $companyId))->toBeNull()
        // Another tenant's contact decides nothing here.
        ->and(DocumentCurrency::ofContact($outsider->getKey(), $companyId))->toBeNull();

    // A list of permitted currencies without EUR (CR-6): EUR is skipped.
    DB::table('company_currencies')->insert(['company_id' => $companyId, 'currency_id' => $usd->getKey()]);

    expect(DocumentCurrency::ofContact($euroSupplier->getKey(), $companyId))->toBeNull()
        ->and(DocumentCurrency::for($euroSupplier->getKey(), JournalType::PURCHASE, $companyId))->toBe($usd->getKey());

    DB::table('company_currencies')->where('company_id', $companyId)->delete();

    // An inactive currency is skipped too.
    $eur->update(['active' => false]);

    expect(DocumentCurrency::ofContact($euroSupplier->getKey(), $companyId))->toBeNull();
});

/**
 * TRACE365, user 2026-11-23: a USD purchase shows its cedi value beside it,
 * at the document's own rate, never as a 1:1 guess.
 */
it('shows the company-currency value of a foreign document at its own rate', function () {
    $ghs = documentCurrency('GHS');
    $usd = documentCurrency('USD');

    $company = CompanyHelper::asNobody(fn () => Company::factory()->create(['currency_id' => $ghs->getKey()]));

    // Stored rate: 1 GHS = 1/11.8 USD.
    $line = CompanyCurrencyEquivalent::line(100.0, $usd, $company, '2026-07-01', 1 / 11.8);

    expect($line)->toContain('1,180.00')
        ->and($line)->toContain('1 USD = 11.80 GHS')
        // Same currency as the company: nothing to add.
        ->and(CompanyCurrencyEquivalent::line(100.0, $ghs, $company))->toBeNull()
        // A rate of 1 between two currencies is a document booked before a
        // rate existed; with no rate on file there is no line at all.
        ->and(CompanyCurrencyEquivalent::line(100.0, $usd, $company, '2026-07-01', 1.0))->toBeNull()
        ->and(CompanyCurrencyEquivalent::short(-1180.0, $usd->getKey(), $company))->toBe('≈ '.money(1180, 'GHS'))
        ->and(CompanyCurrencyEquivalent::short(100.0, $ghs->getKey(), $company))->toBeNull();
});
