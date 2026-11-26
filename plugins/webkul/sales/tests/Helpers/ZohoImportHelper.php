<?php

use Illuminate\Support\Facades\DB;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Models\Move;
use Webkul\Account\Services\AccountingSetupService;
use Webkul\PluginManager\Support\PluginAccess;
use Webkul\Sale\Services\ZohoImport\MappingProposer;
use Webkul\Sale\Services\ZohoImport\ZohoExport;
use Webkul\Sale\Services\ZohoImport\ZohoImporter;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;
use Webkul\Support\Services\CompanyContext;

/*
 * Shared by the Zoho import tests. The fixture is invented: no client data.
 */

function zohoFixturePath(): string
{
    return __DIR__.'/../Fixtures/zoho-export.json';
}

/**
 * A company set up like HiTrace: GHS, Ghana, the Ghana chart from the normal
 * accounting setup, with the modules the import needs.
 */
function zohoTestCompany(): Company
{
    $ghs = Currency::query()->where('name', 'GHS')->firstOrFail();
    $ghs->forceFill(['active' => true])->saveQuietly();

    $ghana = DB::table('countries')->where('code', 'GH')->value('id');
    $creator = User::query()->orderBy('id')->firstOrFail();

    $company = Company::factory()->create([
        'currency_id' => $ghs->id,
        'country_id'  => $ghana,
        'creator_id'  => $creator->id,
    ]);

    app(AccountingSetupService::class)->setUp($company);

    foreach (['accounts', 'invoices', 'sales', 'products', 'partners', 'payments'] as $plugin) {
        $pluginId = DB::table('plugins')->where('name', $plugin)->value('id');

        if ($pluginId) {
            DB::table('company_plugin')->updateOrInsert(['company_id' => $company->id, 'plugin_id' => $pluginId]);
        }
    }

    PluginAccess::flush();

    return $company->refresh();
}

function zohoApprovedMapping(Company $company, ZohoExport $export): array
{
    $mapping = app(CompanyContext::class)->runFor($company->id, fn () => app(MappingProposer::class)->propose($company, $export));

    $mapping['approved'] = true;

    return $mapping;
}

function zohoImport(Company $company, ZohoExport $export, ?array $mapping = null, bool $force = false): array
{
    return app(ZohoImporter::class)->import($company, $export, $mapping ?? zohoApprovedMapping($company, $export), force: $force);
}

function zohoInvoice(Company $company, string $number): Move
{
    return Move::withoutGlobalScopes()
        ->where('company_id', $company->id)
        ->where('move_type', MoveType::OUT_INVOICE)
        ->where('name', $number)
        ->firstOrFail();
}

function zohoAccountBalance(Company $company, string $code): float
{
    return (float) DB::table('accounts_account_move_lines as line')
        ->join('accounts_account_moves as move', 'move.id', '=', 'line.move_id')
        ->join('accounts_accounts as account', 'account.id', '=', 'line.account_id')
        ->join('accounts_account_companies as owner', 'owner.account_id', '=', 'account.id')
        ->where('owner.company_id', $company->id)
        ->where('move.company_id', $company->id)
        ->where('move.state', MoveState::POSTED->value)
        ->where('account.code', $code)
        ->sum('line.balance');
}
