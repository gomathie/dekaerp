<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Webkul\Account\Enums\DisplayType;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\PaymentState;
use Webkul\Account\Models\Journal;
use Webkul\Account\Models\LockDate;
use Webkul\Account\Models\Move;
use Webkul\Account\Models\Partner;
use Webkul\Account\Models\Payment;
use Webkul\Account\Models\Product;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\Support\PluginAccess;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Models\Order;
use Webkul\Sale\Services\ZohoImport\MappingProposer;
use Webkul\Sale\Services\ZohoImport\ZohoComparison;
use Webkul\Sale\Services\ZohoImport\ZohoExport;
use Webkul\Sale\Services\ZohoImport\ZohoImporter;
use Webkul\Sale\Services\ZohoImport\ZohoImportException;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Services\CompanyContext;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../Helpers/ZohoImportHelper.php';

/**
 * Zoho Books import (user decisions, 2026-11-25): a synthetic export with
 * every case HiTrace's history has - tax groups, a tax-inclusive invoice, a
 * line without an item, a draft, a void, partial payment, tax withheld by
 * the customer, expenses from three money accounts - lands in a Ghana
 * company and reproduces Zoho's balances to the pesewa. Client data never
 * appears here: the fixture is invented.
 */
beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('sales');

    DB::table('plugins')->updateOrInsert(
        ['name' => 'sales'],
        ['is_installed' => true, 'is_active' => true, 'updated_at' => now()],
    );

    Package::$plugins = Plugin::all()->keyBy('name');
    PluginAccess::flush();

    URL::resolveMissingNamedRoutesUsing(fn () => '#');

    Mail::fake();
    Notification::fake();

    $this->company = zohoTestCompany();
    $this->export = ZohoExport::fromFile(zohoFixturePath());

    // The importer runs as the console: nobody signed in.
    Auth::guard('web')->logout();
    app()->forgetInstance(CompanyContext::class);
});

it('proposes a mapping of accounts, taxes, banks, items, contacts and numbering', function () {
    $mapping = zohoApprovedMapping($this->company, $this->export);

    expect($mapping)->toHaveKeys(['format', 'version', 'company', 'source', 'accounts', 'taxes', 'bank_accounts', 'payment_modes', 'items', 'contacts', 'sequences'])
        ->and($mapping['accounts']['A-SALES'])->toMatchArray(['action' => 'map', 'code' => '400000'])
        ->and($mapping['accounts']['A-AR'])->toMatchArray(['action' => 'map', 'code' => '121000'])
        ->and($mapping['accounts']['A-TAX'])->toMatchArray(['action' => 'map', 'code' => '251000'])
        ->and($mapping['accounts']['A-TRAVEL'])->toMatchArray(['action' => 'create', 'type' => 'expense'])
        ->and($mapping['accounts']['A-UNUSED']['action'])->toBe('unused')
        ->and($mapping['bank_accounts']['B-BANK']['journal_code'])->toBe('BANK')
        ->and($mapping['bank_accounts']['B-MOMO']['journal_code'])->toBe('MOMO')
        ->and($mapping['bank_accounts']['B-PETTY']['journal_code'])->toBe('CASH')
        ->and(array_keys($mapping['taxes']))->toEqualCanonicalizing(['T-G20:exclusive', 'T-G20:inclusive', 'T-G219:exclusive'])
        ->and($mapping['taxes']['T-G20:inclusive']['components'])->toHaveCount(2)
        ->and($mapping['items']['IT-2']['product_type'])->toBe('goods')
        ->and($mapping['contacts']['V-1']['zoho_type'])->toBe('vendor')
        ->and($mapping['sequences']['invoices'])->toMatchArray(['action' => 'keep', 'prefix' => 'INV-', 'next' => 8])
        ->and($mapping['contacts']['C-9']['action'])->toBe('skip')
        ->and($mapping['options'])->toBe(['numbering' => 'deka', 'skip_unused_samples' => true]);

    expect(app(MappingProposer::class)->markdown($mapping))
        ->toContain('## Accounts')
        ->toContain('Travel & Transportation');
});

it('reproduces every Zoho balance, and the comparison says OK on every row', function () {
    $mapping = zohoApprovedMapping($this->company, $this->export);

    zohoImport($this->company, $this->export, $mapping);

    $rows = app(ZohoComparison::class)->compare($this->company, $this->export, $mapping);
    $differences = array_values(array_filter($rows, fn (array $row) => $row['status'] === 'DIFF'));

    expect($differences)->toBe([])
        ->and(zohoAccountBalance($this->company, '101401'))->toBe(310.0)
        ->and(zohoAccountBalance($this->company, '101501'))->toBe(93.0)
        ->and(zohoAccountBalance($this->company, '121000'))->toBe(2689.87);
});

it('reports a difference when Zoho and DEKA disagree', function () {
    $mapping = zohoApprovedMapping($this->company, $this->export);
    zohoImport($this->company, $this->export, $mapping);

    $data = json_decode(file_get_contents(zohoFixturePath()), true);
    $data['bankaccounts'][0]['balance'] = 250;
    $changed = new ZohoExport($data);

    $bank = collect(app(ZohoComparison::class)->compare($this->company, $changed, $mapping))
        ->firstWhere('section', 'Bank and cash balance');

    expect($bank['status'])->toBe('DIFF')
        ->and($bank['difference'])->toBe(60.0);
});

it('skips what it already imported when run again', function () {
    $mapping = zohoApprovedMapping($this->company, $this->export);
    zohoImport($this->company, $this->export, $mapping);

    $moves = Move::withoutGlobalScopes()->where('company_id', $this->company->id)->count();
    $payments = Payment::withoutGlobalScopes()->where('company_id', $this->company->id)->count();

    $second = zohoImport($this->company, $this->export, $mapping);

    expect(Move::withoutGlobalScopes()->where('company_id', $this->company->id)->count())->toBe($moves)
        ->and(Payment::withoutGlobalScopes()->where('company_id', $this->company->id)->count())->toBe($payments)
        ->and($second['invoices skipped (already imported)'])->toBe(8)
        ->and($second)->not->toHaveKey('invoices imported');
});

it('skips every record it already imported when given a newer export (safety net)', function () {
    $mapping = zohoApprovedMapping($this->company, $this->export);
    zohoImport($this->company, $this->export, $mapping);

    // HiTrace kept working in Zoho after the first export (2026-11-25).
    $data = json_decode(file_get_contents(zohoFixturePath()), true);
    $data['expenses'][] = array_merge($data['expenses'][0], [
        'expense_id'   => 'X-3',
        'date'         => '2025-04-20',
        'created_time' => '2025-04-20T09:00:00+0000',
        'total'        => 12,
        'line_items'   => [['account_id' => 'A-TRAVEL', 'account_name' => 'Travel & Transportation', 'amount' => 12, 'description' => 'Bus']],
    ]);

    $second = zohoImport($this->company, new ZohoExport($data), $mapping);

    expect($second['expenses imported'] ?? 0)->toBe(1)
        ->and($second['expenses skipped (already imported)'])->toBe(2)
        ->and($second)->not->toHaveKey('invoices imported')
        ->and(zohoAccountBalance($this->company, '101401'))->toBe(298.0);
});

it('keeps Zoho numbers and leaves the invoice counters exactly where they were', function () {
    // The customer invoice journal: every imported invoice carries its Zoho
    // number, so none of them may take or move one of its counters. (Payments
    // and expense entries are DEKA's own documents and are numbered by DEKA.)
    $journalIds = DB::table('accounts_journals')->where('company_id', $this->company->id)->where('type', 'sale')->pluck('id');
    $counters = fn () => DB::table('sequences')
        ->where('scope_type', (new Journal)->getMorphClass())
        ->whereIn('scope_id', $journalIds)
        ->orderBy('id')
        ->get(['scope_id', 'variant', 'prefix', 'padding', 'next_number', 'reset_frequency'])
        ->map(fn ($row) => (array) $row)
        ->all();

    // Every journal counter exists before the first Zoho number does.
    Journal::withoutGlobalScopes()->whereIn('id', $journalIds)->get()->each->ensureSequences();
    $before = $counters();

    zohoImport($this->company, $this->export);

    expect(zohoInvoice($this->company, 'INV-000001')->state)->toBe(MoveState::POSTED)
        ->and($counters())->toBe($before);
});

it('makes a missing counter before importing, so Zoho numbers do not set where it starts', function () {
    $journal = Journal::withoutGlobalScopes()
        ->where('company_id', $this->company->id)
        ->where('type', JournalType::SALE)
        ->firstOrFail();

    DB::table('sequences')
        ->where('scope_type', $journal->getMorphClass())
        ->where('scope_id', $journal->id)
        ->delete();

    zohoImport($this->company, $this->export);

    $counter = DB::table('sequences')
        ->where('scope_type', $journal->getMorphClass())
        ->where('scope_id', $journal->id)
        ->where('variant', '')
        ->first();

    // Made before INV-000007 existed: it starts at 1, not at 8.
    expect((int) $counter->next_number)->toBe(1);
});

it('continues Zoho\'s counter only when that option is chosen', function () {
    $mapping = app(CompanyContext::class)->runFor($this->company->id, fn () => app(MappingProposer::class)->propose($this->company, $this->export, ['numbering' => MappingProposer::NUMBERING_ZOHO]));
    $mapping['approved'] = true;

    zohoImport($this->company, $this->export, $mapping);

    $sequence = DB::table('sequences')
        ->where('company_id', $this->company->id)
        ->where('scope_type', (new Journal)->getMorphClass())
        ->where('variant', '')
        ->where('prefix', 'INV-')
        ->first();

    expect($mapping['sequences']['invoices']['action'])->toBe('apply')
        ->and((int) $sequence->next_number)->toBe(8)
        ->and((int) $sequence->padding)->toBe(6);
});

it('skips a Zoho demo contact no document uses', function () {
    zohoImport($this->company, $this->export);

    expect(DB::table('partners_partners')->where('company_id', $this->company->id)->where('name', 'Demo Customer (Sample)')->exists())->toBeFalse()
        ->and(DB::table('partners_partners')->where('company_id', $this->company->id)->where('name', 'Synthetic Customer Two')->exists())->toBeTrue();
});

it('computes a tax-inclusive line, a tax group and a line without an item exactly as Zoho', function () {
    zohoImport($this->company, $this->export);

    $inclusive = zohoInvoice($this->company, 'INV-000002');
    $lineWithoutItem = $inclusive->lines->firstWhere('display_type', DisplayType::PRODUCT);

    expect((float) $inclusive->amount_untaxed)->toBe(100.0)
        ->and((float) $inclusive->amount_tax)->toBe(20.0)
        ->and((float) $inclusive->amount_total)->toBe(120.0)
        ->and($lineWithoutItem->product_id)->toBeNull()
        ->and($lineWithoutItem->account->code)->toBe('400000');
});

it("rounds each tax on the document total when Zoho did, so five equal lines keep Zoho's tax", function () {
    zohoImport($this->company, $this->export);

    $invoice = zohoInvoice($this->company, 'INV-000006');

    // Rounded per line, 5 x (68.34 + 25.79) would be 470.65.
    expect((float) $invoice->amount_tax)->toBe(470.67)
        ->and((float) $invoice->amount_total)->toBe(2619.87);
});

it('keeps a line total Zoho typed when its rounded rate cannot reproduce it', function () {
    zohoImport($this->company, $this->export);

    $invoice = zohoInvoice($this->company, 'INV-000007');
    $line = $invoice->invoiceLines->first();

    expect((float) $invoice->amount_total)->toBe(8500.0)
        ->and((float) $line->quantity)->toBe(1.0)
        ->and($line->name)->toContain('942 x 9.023355');
});

it('keeps a draft as a draft and a void as a cancelled invoice with its number', function () {
    zohoImport($this->company, $this->export);

    expect(zohoInvoice($this->company, 'INV-000003')->state)->toBe(MoveState::DRAFT)
        ->and(zohoInvoice($this->company, 'INV-000004')->state)->toBe(MoveState::CANCEL);
});

it('marks invoices paid and partly paid, with withheld tax written off', function () {
    zohoImport($this->company, $this->export);

    $partly = zohoInvoice($this->company, 'INV-000002');
    $withheld = zohoInvoice($this->company, 'INV-000005');

    expect(zohoInvoice($this->company, 'INV-000001')->payment_state)->toBe(PaymentState::PAID)
        ->and($partly->payment_state)->toBe(PaymentState::PARTIAL)
        ->and((float) $partly->amount_residual)->toBe(70.0)
        ->and($withheld->payment_state)->toBe(PaymentState::PAID)
        ->and(zohoAccountBalance($this->company, '132100'))->toBe(7.0)
        ->and(zohoAccountBalance($this->company, '101405'))->toBe(50.0);
});

it('records an expense as a journal entry: expense account debited, money account credited', function () {
    zohoImport($this->company, $this->export);

    $entry = Move::withoutGlobalScopes()
        ->where('company_id', $this->company->id)
        ->where('move_type', MoveType::ENTRY)
        ->whereNull('origin_payment_id')
        ->whereDate('date', '2025-01-20')
        ->firstOrFail();

    $lines = $entry->lines->keyBy(fn ($line) => $line->account->code);

    expect($entry->state)->toBe(MoveState::POSTED)
        ->and((float) $lines['613000']->debit)->toBe(30.0)
        ->and((float) $lines['101401']->credit)->toBe(30.0)
        ->and($lines['613000']->partner?->name)->toBe('Synthetic Vendor');
});

it('imports estimates as quotations, an invoiced one staying a quotation so the sale counts once', function () {
    zohoImport($this->company, $this->export);

    $orders = Order::withoutGlobalScopes()->where('company_id', $this->company->id)->get()->keyBy('name');

    expect($orders['QT-000001']->state)->toBe(OrderState::SENT)
        ->and((float) $orders['QT-000001']->amount_total)->toBe(240.0)
        ->and($orders['QT-000002']->state)->toBe(OrderState::SENT)
        ->and($orders['QT-000002']->note)->toContain('INV-000001')
        ->and(DB::table('sales_order_invoices')->whereIn('order_id', $orders->pluck('id'))->count())->toBe(0);
});

it('records an owner contribution as a bank entry and skips the bank side of a payment', function () {
    zohoImport($this->company, $this->export);

    $entries = Move::withoutGlobalScopes()
        ->where('company_id', $this->company->id)
        ->where('move_type', MoveType::ENTRY)
        ->whereNull('origin_payment_id')
        ->whereDate('date', '2025-01-02')
        ->get();

    $lines = $entries->first()->lines->keyBy(fn ($line) => $line->account->code);

    // One entry: the customer payment's own bank line is already the payment.
    expect($entries)->toHaveCount(1)
        ->and((float) $lines['101401']->debit)->toBe(100.0)
        ->and((float) $lines['301000']->credit)->toBe(100.0);
});

it('records a sales receipt as an invoice paid at once into its money account', function () {
    zohoImport($this->company, $this->export);

    $receipt = zohoInvoice($this->company, 'SR-00001');

    expect($receipt->payment_state)->toBe(PaymentState::PAID)
        ->and((float) $receipt->amount_total)->toBe(20.0)
        ->and($receipt->partner?->name)->toBe('Synthetic Customer Two');
});

it('maps payment terms, units and item default taxes to their DEKA equivalents', function () {
    zohoImport($this->company, $this->export);

    $customer = Partner::withoutGlobalScopes()->where('company_id', $this->company->id)->where('name', 'Synthetic Customer One')->firstOrFail();
    $subscription = Product::withoutGlobalScopes()->where('company_id', $this->company->id)->where('name', 'Tracking subscription')->firstOrFail();
    $device = Product::withoutGlobalScopes()->where('company_id', $this->company->id)->where('name', 'Tracker device')->firstOrFail();

    expect(DB::table('accounts_payment_terms')->where('id', $customer->companyPropertyValue('property_payment_term_id', $this->company->id))->value('name'))->toBe('Immediate Payment')
        ->and($subscription->uom?->name)->toBe('Units')
        ->and($device->uom?->name)->toBe('kg')
        ->and($subscription->productTaxes->pluck('name')->all())->toBe(['VAT AND LEVIES 20 %']);
});

it('sends no email and no notification while importing', function () {
    zohoImport($this->company, $this->export);

    Mail::assertNothingSent();
    Mail::assertNothingQueued();
    Notification::assertNothingSent();
});

it('refuses a company that already has documents of its own', function () {
    Move::withoutGlobalScopes()->create([
        'move_type'  => MoveType::ENTRY,
        'company_id' => $this->company->id,
        'journal_id' => DB::table('accounts_journals')->where('company_id', $this->company->id)->where('type', 'general')->value('id'),
        'creator_id' => $this->company->creator_id,
        'date'       => '2025-01-01',
    ]);

    expect(fn () => zohoImport($this->company, $this->export))
        ->toThrow(ZohoImportException::class, 'did not create');
});

it('refuses to write into a locked period', function () {
    LockDate::query()->create([
        'company_id' => $this->company->id,
        'lock_date'  => '2025-02-28',
        'changed_by' => $this->company->creator_id,
    ]);

    expect(fn () => zohoImport($this->company, $this->export))
        ->toThrow(ZohoImportException::class, 'locked up to 2025-02-28');
});

it('refuses a mapping nobody approved', function () {
    $mapping = zohoApprovedMapping($this->company, $this->export);
    $mapping['approved'] = false;

    expect(fn () => zohoImport($this->company, $this->export, $mapping))
        ->toThrow(ZohoImportException::class, 'not approved');
});

it('leaves every other company untouched', function () {
    $other = zohoTestCompany();
    $before = Move::withoutGlobalScopes()->where('company_id', $other->id)->count();

    zohoImport($this->company, $this->export);

    expect(Move::withoutGlobalScopes()->where('company_id', $other->id)->count())->toBe($before)
        ->and(DB::table('sales_zoho_import_records')->where('company_id', $other->id)->count())->toBe(0)
        ->and(DB::table('accounts_taxes')->where('company_id', $other->id)->where('name', 'like', 'VAT AND LEVIES%')->count())->toBe(0);
});

it('runs from the console: map, import, compare', function () {
    $dir = sys_get_temp_dir().'/zoho-'.uniqid();
    mkdir($dir);

    expect(Artisan::call('zoho:map', ['company' => $this->company->id, 'file' => zohoFixturePath(), '--output' => $dir]))->toBe(0)
        ->and(file_exists($dir.'/mapping.md'))->toBeTrue();

    $mapping = json_decode(file_get_contents($dir.'/mapping.json'), true);
    $mapping['approved'] = true;
    file_put_contents($dir.'/mapping.json', json_encode($mapping));

    expect(Artisan::call('zoho:import', ['company' => $this->company->id, 'file' => zohoFixturePath(), '--mapping' => $dir.'/mapping.json']))->toBe(0)
        ->and(Artisan::call('zoho:compare', ['company' => $this->company->id, 'file' => zohoFixturePath(), '--mapping' => $dir.'/mapping.json', '--anonymous' => true]))->toBe(0);
});

it('refuses a newer export once the initial load is done, because both systems now hold the new records', function () {
    $mapping = zohoApprovedMapping($this->company, $this->export);
    $counts = zohoImport($this->company, $this->export, $mapping);
    ZohoImporter::recordInitialLoad((int) $this->company->id, null, $this->export->exportedAt(), $counts, $mapping);

    expect(fn () => zohoImport($this->company, $this->export, $mapping))
        ->toThrow(ZohoImportException::class, 'The initial load into this company is already done (Zoho export of 2025-05-01 10:00 UTC). Since then new records are entered in both Zoho and DEKA, so importing a newer export would record them twice.');
});

it('tops up only records dated before the cut-off when that is asked for', function () {
    $mapping = zohoApprovedMapping($this->company, $this->export);

    // The initial load missed the last expense; a top-up after it must add
    // that record and nothing dated from the cut-off on.
    $data = json_decode(file_get_contents(zohoFixturePath()), true);
    $missed = array_pop($data['expenses']);
    $counts = zohoImport($this->company, new ZohoExport($data), $mapping);
    ZohoImporter::recordInitialLoad((int) $this->company->id, null, '2025-05-01T10:00:00Z', $counts, $mapping);

    $data['expenses'][] = $missed;
    $data['expenses'][] = array_merge($missed, ['expense_id' => 'X-LATE', 'date' => '2025-05-02', 'created_time' => '2025-05-02T09:00:00+0000']);

    $topUp = app(ZohoImporter::class)->import($this->company, new ZohoExport($data), $mapping, beforeCutoff: true);

    expect($topUp['expenses imported'])->toBe(1)
        ->and(DB::table('sales_zoho_import_records')->where('company_id', $this->company->id)->where('zoho_id', 'X-LATE')->exists())->toBeFalse();
});

it('lists, in plain words, the documents found in one system only', function () {
    $mapping = zohoApprovedMapping($this->company, $this->export);
    zohoImport($this->company, $this->export, $mapping);

    $data = json_decode(file_get_contents(zohoFixturePath()), true);
    // Entered in Zoho only during the parallel run...
    $data['invoices'][] = array_merge($data['invoices'][4], ['invoice_id' => 'I-NEW', 'invoice_number' => 'INV-000099', 'date' => '2025-04-20', 'created_time' => '2025-04-20T09:00:00+0000']);
    // ...and an expense that exists in DEKA only.
    array_pop($data['expenses']);

    $unmatched = collect(app(ZohoComparison::class)->unmatched($this->company, new ZohoExport($data), $mapping))->pluck('text')->all();

    expect($unmatched)->toContain('In Zoho, not in DEKA: invoice INV-000099, 100.00 on 2025-04-20 (Synthetic Customer One)')
        ->and(collect($unmatched)->contains(fn (string $text) => str_starts_with($text, 'In DEKA, not in Zoho: expense ') && str_contains($text, '20.00 on 2025-02-20')))->toBeTrue()
        ->and($unmatched)->toHaveCount(2);
});
