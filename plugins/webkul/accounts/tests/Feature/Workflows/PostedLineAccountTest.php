<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Webkul\Account\Enums\DisplayType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\PaymentState;
use Webkul\Account\Models\Account;
use Webkul\Account\Models\Move;
use Webkul\Account\Models\MoveLine;
use Webkul\Account\Models\Product;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../Helpers/AccountHelper.php';

/**
 * UF-030 (upstream PR #1697, 2026-11-25): MoveLine::computeAccountId() runs on
 * every save and re-derived a posted line's account from its product. Paying
 * the invoice saves its lines again, so changing a product's income account
 * after posting silently moved the booked revenue to the new account. A posted
 * line now keeps its account; a draft line still follows its product.
 */
beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('accounts');

    DB::table('plugins')->updateOrInsert(
        ['name' => 'accounts'],
        ['is_installed' => true, 'is_active' => true, 'updated_at' => now()],
    );

    Package::$plugins = Plugin::all()->keyBy('name');

    URL::resolveMissingNamedRoutesUsing(fn () => '#');

    AccountHelper::actingAsAdmin();

    $this->company = AccountHelper::company();

    CompanyHelper::setActive([$this->company->id]);

    $this->original = AccountHelper::account('income');
    $this->replacement = AccountHelper::account('income');

    $this->product = Product::query()->findOrFail(AccountHelper::product()->id);
    $this->product->property_account_income_id = $this->original->id;
    $this->product->save();
});

function postedLineAccountTestInvoice(Product $product, Account $account): array
{
    $move = AccountHelper::invoice(MoveType::OUT_INVOICE);

    $line = MoveLine::factory()->create([
        'move_id'      => $move->id,
        'display_type' => DisplayType::PRODUCT,
        'account_id'   => $account->id,
        'product_id'   => $product->id,
        'uom_id'       => AccountHelper::unitsUom()->id,
        'quantity'     => 1,
        'price_unit'   => 250,
        'currency_id'  => $move->currency_id,
        'company_id'   => $move->company_id,
    ]);

    return [AccountHelper::compute($move), $line->refresh()];
}

function postedLineAccountTestChangeIncomeAccount(Product $product, Account $account): void
{
    $product = Product::query()->findOrFail($product->id);
    $product->property_account_income_id = $account->id;
    $product->save();
}

it('keeps the account of a posted product line when the product account changes', function () {
    /** @var Move $move */
    [$move, $line] = postedLineAccountTestInvoice($this->product, $this->original);

    $move = AccountHelper::post($move);

    expect($move->refresh()->state)->toBe(MoveState::POSTED)
        ->and($line->refresh()->account_id)->toBe($this->original->id);

    postedLineAccountTestChangeIncomeAccount($this->product, $this->replacement);

    AccountHelper::pay($move->refresh());

    expect($move->refresh()->payment_state)->toBeIn([PaymentState::PAID, PaymentState::IN_PAYMENT]);

    // Any later save of the posted line, not only the ones payment makes.
    MoveLine::query()->findOrFail($line->id)->save();

    expect($line->refresh()->account_id)->toBe($this->original->id);
});

it('still gives a draft product line its product account', function () {
    [, $line] = postedLineAccountTestInvoice($this->product, $this->original);

    expect($line->account_id)->toBe($this->original->id);

    postedLineAccountTestChangeIncomeAccount($this->product, $this->replacement);

    MoveLine::query()->findOrFail($line->id)->save();

    expect($line->refresh()->account_id)->toBe($this->replacement->id);
});
