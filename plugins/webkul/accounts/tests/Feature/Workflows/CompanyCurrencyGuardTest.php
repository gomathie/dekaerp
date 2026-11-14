<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Services\BaseCurrencyChange;
use Webkul\Chatter\Models\Message;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Currency;

require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../Helpers/AccountHelper.php';

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
    $this->otherCurrency = Currency::factory()->create(['name' => 'CGC']);
});

function postAnInvoice(): void
{
    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE);

    AccountHelper::productLine($invoice, AccountHelper::account('income'), 1, 500);

    AccountHelper::post(AccountHelper::compute($invoice));
}

it('allows changing the company currency while no journal items exist', function () {
    $this->company->update(['currency_id' => $this->otherCurrency->id]);

    expect($this->company->refresh()->currency_id)->toBe($this->otherCurrency->id);
});

it('blocks changing the company currency once journal items exist', function () {
    postAnInvoice();

    expect(fn () => $this->company->update(['currency_id' => $this->otherCurrency->id]))
        ->toThrow(ValidationException::class);
});

it('keeps the original currency when the change is blocked', function () {
    postAnInvoice();

    $original = $this->company->currency_id;

    try {
        $this->company->update(['currency_id' => $this->otherCurrency->id]);
    } catch (ValidationException) {
    }

    expect($this->company->refresh()->currency_id)->toBe($original);
});

it('reports the blocked change against the currency form field', function () {
    postAnInvoice();

    try {
        $this->company->update(['currency_id' => $this->otherCurrency->id]);
    } catch (ValidationException $exception) {
        expect($exception->errors())->toHaveKey('data.currency_id');

        return;
    }

    $this->fail('The currency change was not blocked.');
});

it('allows editing other company fields once journal items exist', function () {
    postAnInvoice();

    $this->company->update(['name' => 'Renamed Company']);

    expect($this->company->refresh()->name)->toBe('Renamed Company');
});

it('blocks changing a parent company currency when only a branch has journal items', function () {
    $parent = CompanyHelper::company();

    DB::table('companies')
        ->where('id', $this->company->id)
        ->update(['parent_id' => $parent->id]);

    postAnInvoice();

    expect(fn () => $parent->update(['currency_id' => $this->otherCurrency->id]))
        ->toThrow(ValidationException::class);
});

it('blocks changing a branch currency when only the parent has journal items', function () {
    $branch = CompanyHelper::company();

    DB::table('companies')
        ->where('id', $branch->id)
        ->update(['parent_id' => $this->company->id]);

    $branch->refresh();

    postAnInvoice();

    expect(fn () => $branch->update(['currency_id' => $this->otherCurrency->id]))
        ->toThrow(ValidationException::class);
});

it('ignores journal items belonging to an unrelated company', function () {
    $unrelated = CompanyHelper::company();

    postAnInvoice();

    $unrelated->update(['currency_id' => $this->otherCurrency->id]);

    expect($unrelated->refresh()->currency_id)->toBe($this->otherCurrency->id);
});

/*
 * D-CR-4 (user decision, 2026-11-13): after entries exist, a Super Admin may
 * still change the base currency, with a reason, and the change is audited.
 * Everyone and every other path stays refused.
 */
function baseCurrencyTestActAs(bool $superAdmin): User
{
    $user = User::factory()->create(['is_active' => true]);

    if ($superAdmin) {
        $user->assignRole(Role::findOrCreate('super_admin', 'web'));
    }

    Auth::guard('web')->login($user);

    return $user;
}

it('lets an active Super Admin change the base currency after posting, and audits it', function () {
    postAnInvoice();
    $original = (int) $this->company->currency_id;
    $actor = baseCurrencyTestActAs(true);

    app(BaseCurrencyChange::class)
        ->change($this->company, (int) $this->otherCurrency->id, 'Client moved its books to CGC');

    expect((int) $this->company->refresh()->currency_id)->toBe((int) $this->otherCurrency->id);

    $audit = Message::withoutGlobalScopes()
        ->where('messageable_id', $this->company->id)
        ->where('event', BaseCurrencyChange::AUDIT_ACTION)
        ->firstOrFail();

    expect((int) $audit->causer_id)->toBe((int) $actor->id)
        ->and($audit->properties['before']['currency_id'])->toBe($original)
        ->and($audit->properties['after']['currency_id'])->toBe((int) $this->otherCurrency->id)
        ->and($audit->properties['after']['reason'])->toBe('Client moved its books to CGC')
        ->and($audit->properties['after']['posted_entries'])->toBeGreaterThan(0);
});

it('refuses the base currency change to a user who is not a Super Admin', function () {
    postAnInvoice();
    baseCurrencyTestActAs(false);

    expect(fn () => app(BaseCurrencyChange::class)
        ->change($this->company, (int) $this->otherCurrency->id, 'reason'))
        ->toThrow(AuthorizationException::class, 'Only an active Super Admin');
});

it('refuses the base currency change without a reason', function () {
    postAnInvoice();
    baseCurrencyTestActAs(true);

    expect(fn () => app(BaseCurrencyChange::class)
        ->change($this->company, (int) $this->otherCurrency->id, '   '))
        ->toThrow(ValidationException::class, 'Give a reason');
});

it('still refuses a plain currency save after the audited change has run', function () {
    postAnInvoice();
    baseCurrencyTestActAs(true);
    $third = Currency::factory()->create(['name' => 'CGD']);

    app(BaseCurrencyChange::class)
        ->change($this->company, (int) $this->otherCurrency->id, 'reason');

    expect(fn () => $this->company->refresh()->update(['currency_id' => $third->id]))
        ->toThrow(ValidationException::class);
});
